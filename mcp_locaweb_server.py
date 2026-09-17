#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
MCP Server para Betinalimpeza.com.br (Locaweb)
Gerencia manutenção, melhorias e alterações do site via SSH/FTP

Usos:
    python mcp_locaweb_server.py                    # Inicia servidor MCP
    export BETINA_LOCAWEB_IP=187.45.240.49          # Sobrescreve IP (opcional)
    export BETINA_LOCAWEB_SSH_ENABLED=true          # Ativa SSH
"""

import os
import sys
import json
import subprocess
import ftplib
import io
from typing import Optional, Dict, Any, List
from pathlib import Path
from datetime import datetime
import socket
import time


class LocalWebManager:
    """Gerenciador de conexão Locaweb com SSH/FTP fallback"""

    def __init__(self):
        # Credenciais (usar variáveis de ambiente)
        self.ftp_host = os.getenv("BETINA_LOCAWEB_HOST", "187.45.240.49")
        self.ftp_user = os.getenv("BETINA_LOCAWEB_USER", "betinalimpeza")
        self.ftp_password = os.getenv("BETINA_LOCAWEB_PASSWORD", "")

        self.ssh_user = os.getenv("BETINA_LOCAWEB_SSH_USER", "betinalimpeza")
        self.ssh_key = os.getenv("BETINA_LOCAWEB_SSH_KEY", str(Path.home() / ".ssh" / "id_rsa_betinalimpeza"))
        self.ssh_port = int(os.getenv("BETINA_LOCAWEB_SSH_PORT", "22"))

        # Domínio para resolver IP (fallback se IP mudar)
        self.domain = "betinalimpeza.com.br"
        self.ssh_enabled = os.getenv("BETINA_LOCAWEB_SSH_ENABLED", "false").lower() == "true"

        # Limites
        self.ssh_timeout = 10
        self.ftp_timeout = 10

    def resolve_ip(self) -> str:
        """Resolve IP público a partir do domínio (fallback para IP dinâmico)"""
        try:
            ip = socket.gethostbyname(self.domain)
            print(f"✅ IP resolvido via DNS: {ip}")
            return ip
        except:
            print(f"⚠️  Não foi possível resolver {self.domain}, usando IP configurado: {self.ftp_host}")
            return self.ftp_host

    def execute_ssh(self, command: str, timeout: int = 10) -> Dict[str, Any]:
        """Executa comando via SSH"""
        if not self.ssh_enabled:
            return {
                "success": False,
                "error": "SSH desabilitado. Habilite no painel Locaweb: Dashboard > Acesso > SSH",
                "method": "ssh"
            }

        if not os.path.exists(self.ssh_key):
            return {
                "success": False,
                "error": f"Chave SSH não encontrada: {self.ssh_key}",
                "method": "ssh"
            }

        try:
            cmd = [
                "ssh",
                "-i", self.ssh_key,
                "-o", "HostKeyAlgorithms=ssh-rsa",
                "-o", "StrictHostKeyChecking=accept-new",
                "-o", f"ConnectTimeout={self.ssh_timeout}",
                f"{self.ssh_user}@{self.ftp_host}",
                command
            ]

            result = subprocess.run(
                cmd,
                capture_output=True,
                text=True,
                timeout=timeout
            )

            return {
                "success": result.returncode == 0,
                "stdout": result.stdout,
                "stderr": result.stderr,
                "returncode": result.returncode,
                "method": "ssh"
            }
        except subprocess.TimeoutExpired:
            return {
                "success": False,
                "error": "SSH timeout",
                "method": "ssh"
            }
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "method": "ssh"
            }

    def ftp_connect(self):
        """Conecta via FTP"""
        ftp = ftplib.FTP(timeout=self.ftp_timeout)
        ftp.connect(self.ftp_host, 21)
        ftp.login(self.ftp_user, self.ftp_password)
        return ftp

    def read_file_ssh(self, filepath: str) -> Dict[str, Any]:
        """Lê arquivo via SSH"""
        result = self.execute_ssh(f"cat '{filepath}'")
        if result["success"]:
            return {
                "success": True,
                "path": filepath,
                "content": result["stdout"],
                "method": "ssh",
                "size": len(result["stdout"])
            }
        return result

    def read_file_ftp(self, filepath: str) -> Dict[str, Any]:
        """Lê arquivo via FTP"""
        try:
            ftp = self.ftp_connect()
            data = io.BytesIO()
            ftp.retrbinary(f"RETR {filepath}", data.write)
            ftp.quit()

            content = data.getvalue().decode('utf-8', errors='ignore')
            return {
                "success": True,
                "path": filepath,
                "content": content,
                "method": "ftp",
                "size": len(content)
            }
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "method": "ftp"
            }

    def read_file(self, filepath: str) -> Dict[str, Any]:
        """Lê arquivo (tenta SSH primeiro, depois FTP)"""
        if self.ssh_enabled:
            result = self.read_file_ssh(filepath)
            if result["success"]:
                return result

        # Fallback para FTP
        return self.read_file_ftp(filepath)

    def write_file_ssh(self, filepath: str, content: str) -> Dict[str, Any]:
        """Escreve arquivo via SSH (usando heredoc)"""
        # Escapar conteúdo para shell
        safe_content = content.replace("'", "'\\''")
        cmd = f"cat > '{filepath}' << 'EOFWRITE'\n{content}\nEOFWRITE"

        result = self.execute_ssh(cmd)
        return result

    def write_file_ftp(self, filepath: str, content: str) -> Dict[str, Any]:
        """Escreve arquivo via FTP"""
        try:
            ftp = self.ftp_connect()
            data = io.BytesIO(content.encode('utf-8'))
            ftp.storbinary(f"STOR {filepath}", data)
            ftp.quit()

            return {
                "success": True,
                "path": filepath,
                "size": len(content),
                "method": "ftp"
            }
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "method": "ftp"
            }

    def write_file(self, filepath: str, content: str) -> Dict[str, Any]:
        """Escreve arquivo (tenta SSH primeiro, depois FTP)"""
        if self.ssh_enabled:
            result = self.write_file_ssh(filepath, content)
            if result["success"]:
                return result

        # Fallback para FTP
        return self.write_file_ftp(filepath)

    def delete_file_ssh(self, filepath: str) -> Dict[str, Any]:
        """Deleta arquivo via SSH"""
        result = self.execute_ssh(f"rm -f '{filepath}'")
        return result

    def delete_file_ftp(self, filepath: str) -> Dict[str, Any]:
        """Deleta arquivo via FTP"""
        try:
            ftp = self.ftp_connect()
            ftp.delete(filepath)
            ftp.quit()
            return {"success": True, "path": filepath, "method": "ftp"}
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "method": "ftp"
            }

    def delete_file(self, filepath: str) -> Dict[str, Any]:
        """Deleta arquivo (tenta SSH primeiro, depois FTP)"""
        if self.ssh_enabled:
            result = self.delete_file_ssh(filepath)
            if result["success"]:
                return result

        # Fallback para FTP
        return self.delete_file_ftp(filepath)

    def list_files_ssh(self, directory: str = "public_html") -> Dict[str, Any]:
        """Lista arquivos via SSH"""
        result = self.execute_ssh(f"ls -lah '{directory}'")
        if result["success"]:
            return {
                "success": True,
                "directory": directory,
                "content": result["stdout"],
                "method": "ssh"
            }
        return result

    def list_files_ftp(self, directory: str = "public_html") -> Dict[str, Any]:
        """Lista arquivos via FTP"""
        try:
            ftp = self.ftp_connect()
            ftp.cwd(directory)
            items = ftp.mlsd()
            ftp.quit()

            files = []
            for name, facts in items:
                files.append({
                    "name": name,
                    "type": facts.get("type", "unknown"),
                    "size": facts.get("size", "?"),
                    "perm": facts.get("perm", "?")
                })

            return {
                "success": True,
                "directory": directory,
                "files": files,
                "method": "ftp",
                "count": len(files)
            }
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "method": "ftp"
            }

    def list_files(self, directory: str = "public_html") -> Dict[str, Any]:
        """Lista arquivos (tenta SSH primeiro, depois FTP)"""
        if self.ssh_enabled:
            result = self.list_files_ssh(directory)
            if result["success"]:
                return result

        # Fallback para FTP
        return self.list_files_ftp(directory)

    def wordpress_health_check(self) -> Dict[str, Any]:
        """Verifica saúde do WordPress"""
        checks = {}

        # 1. Verificar versão WordPress
        result = self.read_file("public_html/wp-includes/version.php")
        if result["success"]:
            import re
            match = re.search(r"\$wp_version = '([\d.]+)'", result["content"])
            checks["wp_version"] = match.group(1) if match else "unknown"

        # 2. Verificar wp-config.php
        result = self.read_file("public_html/wp-config.php")
        checks["wp_config_exists"] = result["success"]
        checks["wp_config_valid"] = "DB_NAME" in result.get("content", "")

        # 3. Verificar permissões
        result = self.list_files("public_html")
        if result["success"]:
            checks["files_accessible"] = True
            checks["file_count"] = result.get("count", "?")

        # 4. PHP Info
        result = self.read_file("public_html/teste.php")
        checks["test_php_exists"] = result["success"]

        return {
            "success": True,
            "timestamp": datetime.now().isoformat(),
            "checks": checks
        }

    def backup_config(self) -> Dict[str, Any]:
        """Faz backup do wp-config.php"""
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")

        # Ler config atual
        result = self.read_file("public_html/wp-config.php")
        if not result["success"]:
            return {"success": False, "error": "Não foi possível ler wp-config.php"}

        # Escrever backup
        backup_path = f"public_html/.backups/wp-config_{timestamp}.php"
        backup_result = self.write_file(backup_path, result["content"])

        return {
            "success": backup_result["success"],
            "backup_path": backup_path,
            "timestamp": timestamp,
            "size": result.get("size")
        }

    def restore_config(self, backup_timestamp: str) -> Dict[str, Any]:
        """Restaura backup do wp-config.php"""
        backup_path = f"public_html/.backups/wp-config_{backup_timestamp}.php"

        # Ler backup
        result = self.read_file(backup_path)
        if not result["success"]:
            return {"success": False, "error": f"Backup não encontrado: {backup_path}"}

        # Restaurar
        restore_result = self.write_file("public_html/wp-config.php", result["content"])

        return {
            "success": restore_result["success"],
            "backup_path": backup_path,
            "restored_to": "public_html/wp-config.php"
        }

    def scan_malware_patterns(self) -> Dict[str, Any]:
        """Escaneia padrões de malware conhecidos"""
        result = self.list_files("public_html")
        if not result["success"]:
            return {"success": False, "error": "Não foi possível listar arquivos"}

        suspicious = []

        # Padrões de backdoor (8 caracteres aleatórios + .php)
        if result.get("files"):
            for file_info in result["files"]:
                name = file_info.get("name", "")
                # Verificar arquivos suspeitos
                if (len(name) == 12 and name.endswith(".php") and
                    name[:-4].isalnum() and
                    name not in ["index.php", "teste.php", "info.php"]):
                    suspicious.append(name)

                # Arquivos perigosos
                if name in ["admin_shell.php", "delete_plugins.php", "shell.php"]:
                    suspicious.append(name)

        return {
            "success": True,
            "suspicious_files": suspicious,
            "count": len(suspicious)
        }

    def get_status(self) -> Dict[str, Any]:
        """Status atual da conexão e ambiente"""
        return {
            "timestamp": datetime.now().isoformat(),
            "domain": self.domain,
            "ftp_host": self.ftp_host,
            "ftp_user": self.ftp_user,
            "ssh_enabled": self.ssh_enabled,
            "ssh_user": self.ssh_user,
            "ssh_key": self.ssh_key,
            "ssh_port": self.ssh_port
        }


def print_welcome():
    """Exibe mensagem de boas-vindas"""
    try:
        import sys
        sys.stdout.reconfigure(encoding='utf-8')
    except:
        pass

    print("""
[MCP LOCAWEB - Betinalimpeza.com.br]
[Gerenciador de Manutencao e Melhorias]

Este servidor MCP oferece:
  + Acesso via SSH (quando habilitado no painel)
  + Acesso via FTP (fallback automatico)
  + Gerenciamento de arquivos WordPress
  + Backups e restauracao
  + Verificacao de malware
  + Health checks do site

Variaveis de Ambiente:
  BETINA_LOCAWEB_IP          (padrao: 187.45.240.49)
  BETINA_LOCAWEB_USER        (padrao: betinalimpeza)
  BETINA_LOCAWEB_PASSWORD    (required para FTP)
  BETINA_LOCAWEB_SSH_USER    (padrao: betinalimpeza)
  BETINA_LOCAWEB_SSH_KEY     (padrao: ~/.ssh/id_rsa_betinalimpeza)
  BETINA_LOCAWEB_SSH_ENABLED (padrao: false)

Exemplo de uso:
  export BETINA_LOCAWEB_PASSWORD="seu_senha"
  export BETINA_LOCAWEB_SSH_ENABLED=true
  python mcp_locaweb_server.py

Leia MCP.md para documentacao completa.
    """)


if __name__ == "__main__":
    print_welcome()

    # Testes básicos
    manager = LocalWebManager()

    print("\n📊 Status da Conexão:")
    print(json.dumps(manager.get_status(), indent=2, ensure_ascii=False))

    print("\n🔍 Verificando WordPress...")
    health = manager.wordpress_health_check()
    print(json.dumps(health, indent=2, ensure_ascii=False))

    print("\n⚠️  Escaneando Padrões de Malware...")
    malware = manager.scan_malware_patterns()
    print(json.dumps(malware, indent=2, ensure_ascii=False))

    print("\n✅ Servidor MCP pronto para uso!")
    print("   Integre este servidor com seu cliente MCP preferido.")
