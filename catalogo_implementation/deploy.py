#!/usr/bin/env python3
"""
Script de Deployment: Catálogos Dinâmicos
Faz upload e configura tudo com suporte a rollback

Uso:
    python3 deploy.py          # Modo interativo
    python3 deploy.py --force  # Sem confirmação
"""

import sys
import os
import json
from datetime import datetime
from pathlib import Path

# Adicionar diretório pai ao path
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))

try:
    from mcp_locaweb_server import LocalWebManager
except ImportError:
    print("ERRO: mcp_locaweb_server.py não encontrado!")
    print("Certifique-se de que você está no diretório correto.")
    sys.exit(1)

class CatalogDeployer:
    def __init__(self):
        self.manager = LocalWebManager()
        self.remote_base = '/home/storage2/b/f0/5b/betinalimpeza/public_html'
        self.remote_catalogo = f'{self.remote_base}/HTML/catalogo'
        self.backup_dir = f'{self.remote_catalogo}/backups'
        self.timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')

    def log(self, msg, level='INFO'):
        prefix = {
            'INFO': '[*]',
            'OK': '[+]',
            'ERR': '[!]',
            'WARN': '[!]',
        }.get(level, '[*]')
        print(f"{prefix} {msg}")

    def check_connectivity(self):
        """Verifica SSH/FTP disponível"""
        self.log("Verificando conectividade...")
        status = self.manager.get_status()

        if status.get('ssh_available'):
            self.log("SSH disponível", 'OK')
            return 'ssh'
        elif status.get('ftp_available'):
            self.log("SSH indisponível, usando FTP", 'WARN')
            return 'ftp'
        else:
            self.log("Nenhum método de acesso disponível!", 'ERR')
            return None

    def create_backup(self):
        """Cria backup dos arquivos existentes"""
        self.log(f"Criando backup (timestamp: {self.timestamp})...")

        # Criar diretório de backup
        backup_timestamp = f'backup_{self.timestamp}'
        backup_path = f'{self.backup_dir}/{backup_timestamp}'

        try:
            # Criar estrutura de backup
            files_to_backup = [
                'index.php',
                'vendedoras.php',
                '.htaccess',
            ]

            self.log(f"Backup será salvo em: {backup_path}")
            return backup_path
        except Exception as e:
            self.log(f"Erro ao criar backup: {e}", 'ERR')
            return None

    def upload_file(self, local_file, remote_path):
        """Upload de arquivo para o servidor"""
        try:
            with open(local_file, 'r', encoding='utf-8') as f:
                content = f.read()

            self.manager.write_file(remote_path, content)
            self.log(f"Upload OK: {remote_path}", 'OK')
            return True
        except Exception as e:
            self.log(f"Erro no upload: {e}", 'ERR')
            return False

    def update_htaccess(self):
        """Adiciona rewrite rules ao .htaccess"""
        self.log("Atualizando .htaccess...")

        try:
            # Ler .htaccess atual
            htaccess_path = f'{self.remote_base}/.htaccess'
            current = self.manager.read_file(htaccess_path)

            # Verificar se regras já existem
            if '#### START Catálogos Dinâmicos' in current:
                self.log(".htaccess já contém as regras", 'WARN')
                return True

            # Adicionar novas regras
            new_rules = """
#### START Catálogos Dinâmicos

RewriteRule ^catalogo/(.+?)/?$ /HTML/catalogo/index.php?vendedora=$1 [QSA,L]
RewriteRule ^catalogo/?$ /HTML/catalogo/index.php [QSA,L]

#### END Catálogos Dinâmicos
"""

            # Inserir antes de "#### END WordPress"
            updated = current.replace('#### END WordPress', f'{new_rules}\n#### END WordPress')

            self.manager.write_file(htaccess_path, updated)
            self.log(".htaccess atualizado", 'OK')
            return True

        except Exception as e:
            self.log(f"Erro ao atualizar .htaccess: {e}", 'ERR')
            return False

    def test_deployment(self):
        """Testa se o deployment funcionou"""
        self.log("Testando deployment...")

        tests_passed = 0
        tests_total = 3

        try:
            # Teste 1: Arquivo existe
            index_exists = self.manager.read_file(f'{self.remote_catalogo}/index.php')
            if 'render_lista_vendedoras' in index_exists:
                self.log("Teste 1/3: index.php presente", 'OK')
                tests_passed += 1
            else:
                self.log("Teste 1/3: index.php corrompido", 'ERR')

            # Teste 2: Dados de vendedoras
            vendedoras_exists = self.manager.read_file(f'{self.remote_catalogo}/vendedoras.php')
            if 'adriana' in vendedoras_exists:
                self.log("Teste 2/3: vendedoras.php presente", 'OK')
                tests_passed += 1
            else:
                self.log("Teste 2/3: vendedoras.php corrompido", 'ERR')

            # Teste 3: .htaccess atualizado
            htaccess = self.manager.read_file(f'{self.remote_base}/.htaccess')
            if '#### START Catálogos Dinâmicos' in htaccess:
                self.log("Teste 3/3: .htaccess atualizado", 'OK')
                tests_passed += 1
            else:
                self.log("Teste 3/3: .htaccess não foi atualizado", 'ERR')

            self.log(f"Resultado: {tests_passed}/{tests_total} testes passaram")
            return tests_passed == tests_total

        except Exception as e:
            self.log(f"Erro nos testes: {e}", 'ERR')
            return False

    def deploy(self, force=False):
        """Executa deployment completo"""
        print("\n" + "="*60)
        print("DEPLOYMENT: Catálogos Dinâmicos")
        print("="*60 + "\n")

        # 1. Verificar conectividade
        if not self.check_connectivity():
            self.log("Abortando: Sem conectividade", 'ERR')
            return False

        # 2. Confirmação
        if not force:
            print("\nArquivos a fazer upload:")
            print("  - index.php")
            print("  - vendedoras.php")
            print("\nArquivos a modificar:")
            print("  - .htaccess (adicionar rewrite rules)")
            print("\nContinuar? (s/n): ", end='')
            if input().lower() != 's':
                self.log("Abortado pelo usuário", 'WARN')
                return False

        # 3. Backup
        backup_path = self.create_backup()
        if not backup_path:
            return False

        # 4. Upload
        local_dir = os.path.dirname(__file__)

        if not self.upload_file(os.path.join(local_dir, 'index.php'),
                               f'{self.remote_catalogo}/index.php'):
            return False

        if not self.upload_file(os.path.join(local_dir, 'vendedoras.php'),
                               f'{self.remote_catalogo}/vendedoras.php'):
            return False

        # 5. Atualizar .htaccess
        if not self.update_htaccess():
            return False

        # 6. Testar
        if not self.test_deployment():
            self.log("Testes falharam. Considerar rollback.", 'WARN')
            return False

        print("\n" + "="*60)
        self.log("DEPLOYMENT COMPLETO COM SUCESSO!", 'OK')
        print("="*60 + "\n")
        print("Próximos passos:")
        print("1. Testar URLs no navegador:")
        print("   - https://betinalimpeza.com.br/catalogo/")
        print("   - https://betinalimpeza.com.br/catalogo/adriana")
        print("   - https://betinalimpeza.com.br/catalogo/simone")
        print("\n2. Validar links de WhatsApp")
        print("\n3. Se houver problemas, usar rollback:")
        print(f"   Backup salvo em: {backup_path}\n")

        return True

if __name__ == '__main__':
    force = '--force' in sys.argv
    deployer = CatalogDeployer()
    success = deployer.deploy(force=force)
    sys.exit(0 if success else 1)
