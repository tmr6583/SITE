# MCP Locaweb - Betinalimpeza.com.br

**Versão**: 1.0  
**Última Atualização**: 2026-09-17  
**Status**: ✅ Operacional (SSH + FTP)

---

## 📋 Sumário Executivo

Este MCP (Model Context Protocol) fornece acesso automatizado e seguro ao site **betinalimpeza.com.br** hospedado na Locaweb. Permite manutenção, melhorias, backups e verificação de segurança.

### Características Principais

| Recurso | Método | Disponibilidade | Fallback |
|---------|--------|-----------------|----------|
| **Leitura de arquivos** | SSH/FTP | SSH quando ativo, senão FTP | Automático |
| **Escrita de arquivos** | SSH/FTP | SSH quando ativo, senão FTP | Automático |
| **Execução de comandos** | SSH | Quando ativo no painel | N/A |
| **Listagem de arquivos** | SSH/FTP | SSH quando ativo, senão FTP | Automático |
| **Backups** | SSH/FTP | Contínuo | Automático |
| **Scans de malware** | SSH/FTP | Contínuo | Automático |

---

## ⚠️ PONTOS CRÍTICOS

### 1️⃣ IP Público Dinâmico

**O IP público do servidor **PODE MUDAR** periodicamente.**

**Impacto:**
- SSH pode ficar indisponível se o IP mudar
- FTP continuará funcionando (usa domínio + porta 21)
- Chaves SSH pré-configuradas podem exigir revalidação

**Solução Implementada:**
```python
# O MCP tenta resolver IP automaticamente via DNS
ip = socket.gethostbyname("betinalimpeza.com.br")
```

**Se SSH falhar:**
1. Verificar no painel Locaweb qual é o IP atual
2. Atualizar variável de ambiente:
   ```bash
   export BETINA_LOCAWEB_IP=<novo_ip>
   ```
3. O MCP detectará automaticamente na próxima execução

---

### 2️⃣ SSH Requer Ativação Manual

**SSH NÃO está permanentemente ativo.** Você DEVE habilitar a cada 3 horas no painel Locaweb.

**Como Habilitar SSH:**

1. Acesse: https://painelhospedagem.locaweb.com.br/dashboard/8291801
2. Menu: **Hospedagem** → **Ambientes** → **Acesso**
3. Ativar: **SSH** (checkbox)
4. Confirmar e aguardar ~1 minuto
5. Executar no terminal:
   ```bash
   export BETINA_LOCAWEB_SSH_ENABLED=true
   python mcp_locaweb_server.py
   ```

**Duração:** SSH permanece ativo por **3 horas** após ativação
**Fallback:** Se SSH não está disponível, o MCP usa FTP automaticamente

---

## 🚀 Setup Inicial

### Pré-requisitos

```bash
# Linux/macOS/Windows (Git Bash)
python3 --version          # Python 3.8+
ssh -V                      # OpenSSH 7.0+
ssh-keygen --version       # Para gerar chaves se necessário
```

### 1. Configurar Variáveis de Ambiente

```bash
# Criar arquivo .env (não commitar!)
cat > .env << 'EOF'
# Credenciais Locaweb
BETINA_LOCAWEB_IP=187.45.240.49
BETINA_LOCAWEB_HOST=187.45.240.49
BETINA_LOCAWEB_USER=betinalimpeza
BETINA_LOCAWEB_PASSWORD=Esquilo08!!!!!

# SSH (quando ativo no painel)
BETINA_LOCAWEB_SSH_USER=betinalimpeza
BETINA_LOCAWEB_SSH_KEY=~/.ssh/id_rsa_betinalimpeza
BETINA_LOCAWEB_SSH_PORT=22
BETINA_LOCAWEB_SSH_ENABLED=false  # Mude para true quando ativar no painel
EOF

# Carregar variáveis
source .env  # ou: set -a && source .env && set +a (bash/zsh)
```

**Windows (PowerShell):**
```powershell
$env:BETINA_LOCAWEB_PASSWORD = "Esquilo08!!!!!"
$env:BETINA_LOCAWEB_SSH_ENABLED = "false"
```

### 2. Preparar Chave SSH

```bash
# Se já tem chave (verificar)
ls ~/.ssh/id_rsa_betinalimpeza
# Saída esperada: /home/user/.ssh/id_rsa_betinalimpeza

# Se não tem, gerar nova
ssh-keygen -t ed25519 -f ~/.ssh/id_rsa_betinalimpeza -N "" -C "betinalimpeza"

# Verificar permissões
chmod 600 ~/.ssh/id_rsa_betinalimpeza
chmod 644 ~/.ssh/id_rsa_betinalimpeza.pub
```

### 3. Adicionar Chave Pública ao Servidor

A chave pública deve estar configurada no servidor Locaweb.

```bash
# 1. Copiar conteúdo da chave pública
cat ~/.ssh/id_rsa_betinalimpeza.pub
# Saída: ssh-ed25519 AAAAC3Nza... betinalimpeza

# 2. No painel Locaweb → Hospedagem → SSH → Chaves Públicas
#    Adicionar a chave copiada acima

# 3. Testar conexão
ssh -i ~/.ssh/id_rsa_betinalimpeza \
    -o HostKeyAlgorithms=ssh-rsa \
    betinalimpeza@187.45.240.49 \
    "echo 'SSH OK'"
# Saída esperada: SSH OK
```

### 4. Configurar SSH Config (Opcional mas Recomendado)

```bash
# Adicionar ao ~/.ssh/config
cat >> ~/.ssh/config << 'EOF'
Host betinalimpeza
    HostName 187.45.240.49
    User betinalimpeza
    Port 22
    IdentityFile ~/.ssh/id_rsa_betinalimpeza
    HostKeyAlgorithms ssh-rsa
    StrictHostKeyChecking accept-new
    ServerAliveInterval 60
    ServerAliveCountMax 3
    ConnectTimeout 10
EOF

# Agora pode usar
ssh betinalimpeza "whoami"
```

---

## 📱 Uso do MCP

### Método 1: Testes Diretos (Python)

```bash
# Carregar variáveis de ambiente
source .env

# Executar testes básicos
python mcp_locaweb_server.py

# Saída esperada:
# ✅ Status da Conexão
# ✅ Verificando WordPress
# ✅ Escaneando Padrões de Malware
```

### Método 2: Importar no Seu Código

```python
from mcp_locaweb_server import LocalWebManager
import os

# Carregar credenciais
os.environ["BETINA_LOCAWEB_PASSWORD"] = "sua_senha"
os.environ["BETINA_LOCAWEB_SSH_ENABLED"] = "true"  # Se SSH está ativo

# Criar gerenciador
manager = LocalWebManager()

# Ler arquivo
result = manager.read_file("public_html/wp-config.php")
if result["success"]:
    print(result["content"][:200])

# Escrever arquivo
new_content = """<?php
// Seu conteúdo aqui
?>"""
result = manager.write_file("public_html/teste.php", new_content)
print(f"Escrito via: {result['method']}")  # SSH ou FTP

# Listar arquivos
result = manager.list_files("public_html/wp-content/plugins")
for file in result.get("files", []):
    print(f"{file['name']} ({file['type']})")

# Health check
health = manager.wordpress_health_check()
print(health["checks"])

# Scan de malware
malware = manager.scan_malware_patterns()
print(f"Arquivos suspeitos encontrados: {malware['count']}")

# Backup
backup = manager.backup_config()
print(f"Backup em: {backup['backup_path']}")
```

---

## 🔧 Operações Comuns

### 1. Verificar Status do Site

```bash
export BETINA_LOCAWEB_PASSWORD="Esquilo08!!!!!"
python << 'PYEOF'
from mcp_locaweb_server import LocalWebManager

manager = LocalWebManager()
health = manager.wordpress_health_check()

print("\n📊 WordPress Status:")
for check, value in health["checks"].items():
    status = "✅" if value else "❌"
    print(f"  {status} {check}: {value}")
PYEOF
```

### 2. Criar Backup Rápido

```bash
python << 'PYEOF'
import os
from mcp_locaweb_server import LocalWebManager

os.environ["BETINA_LOCAWEB_PASSWORD"] = "Esquilo08!!!!!"
manager = LocalWebManager()

# Backup de múltiplos arquivos críticos
files_to_backup = [
    "public_html/wp-config.php",
    "public_html/.htaccess",
    "public_html/index.php"
]

for filepath in files_to_backup:
    result = manager.read_file(filepath)
    if result["success"]:
        backup_name = filepath.replace("/", "_").replace(".", "_") + ".bak"
        print(f"✅ Lido: {filepath} ({result['size']} bytes)")
    else:
        print(f"❌ Erro: {filepath}")
PYEOF
```

### 3. Limpar Arquivos Suspeitos

```bash
python << 'PYEOF'
import os
from mcp_locaweb_server import LocalWebManager

os.environ["BETINA_LOCAWEB_PASSWORD"] = "Esquilo08!!!!!"
manager = LocalWebManager()

# Escanear
malware = manager.scan_malware_patterns()

print(f"🔍 Encontrados {malware['count']} arquivos suspeitos:")
for file in malware["suspicious_files"]:
    print(f"  ⚠️  {file}")

# Deletar (com confirmação)
for file in malware["suspicious_files"]:
    result = manager.delete_file(f"public_html/{file}")
    status = "✅ Deletado" if result["success"] else f"❌ Erro: {result.get('error')}"
    print(f"  {status}: {file} (via {result['method']})")
PYEOF
```

### 4. Editar wp-config.php

```bash
python << 'PYEOF'
import os
from mcp_locaweb_server import LocalWebManager

os.environ["BETINA_LOCAWEB_PASSWORD"] = "Esquilo08!!!!!"
manager = LocalWebManager()

# Ler atual
current = manager.read_file("public_html/wp-config.php")
print("Conteúdo atual lido via:", current["method"])

# Fazer backup
backup = manager.backup_config()
print(f"Backup salvo em: {backup['backup_path']}")

# Modificar (exemplo: adicionar DEBUG)
if current["success"]:
    content = current["content"]
    # Adicionar define('WP_DEBUG', false);
    new_content = content.replace(
        "/* That's all, stop editing!",
        "define('WP_DEBUG', false);\n\n/* That's all, stop editing!"
    )
    
    result = manager.write_file("public_html/wp-config.php", new_content)
    print(f"✅ Atualizado via: {result['method']}")
PYEOF
```

### 5. Executar Comando SSH (quando ativo)

```bash
export BETINA_LOCAWEB_SSH_ENABLED=true
export BETINA_LOCAWEB_PASSWORD="Esquilo08!!!!!"

python << 'PYEOF'
from mcp_locaweb_server import LocalWebManager

manager = LocalWebManager()

# Comando
result = manager.execute_ssh("ls -lah public_html/wp-content/plugins | head -10")

if result["success"]:
    print("✅ Comando executado:")
    print(result["stdout"])
else:
    print(f"❌ Erro: {result['error']}")
    print(f"   Dica: SSH está ativo no painel? {manager.ssh_enabled}")
PYEOF
```

---

## 🔍 Troubleshooting

### Erro: "SSH desabilitado"

```
❌ SSH desabilitado. Habilite no painel Locaweb: Dashboard > Acesso > SSH
```

**Solução:**
1. Acessar painel: https://painelhospedagem.locaweb.com.br/dashboard/8291801
2. Ativar SSH em **Hospedagem** → **Ambientes** → **Acesso**
3. Definir variável: `export BETINA_LOCAWEB_SSH_ENABLED=true`

---

### Erro: "Chave SSH não encontrada"

```
❌ Chave SSH não encontrada: /home/user/.ssh/id_rsa_betinalimpeza
```

**Solução:**
```bash
# Gerar nova chave
ssh-keygen -t ed25519 -f ~/.ssh/id_rsa_betinalimpeza -N "" -C "betinalimpeza"

# Copiar chave pública para o servidor Locaweb
cat ~/.ssh/id_rsa_betinalimpeza.pub
# Colar em: Painel Locaweb → SSH → Chaves Públicas
```

---

### Erro: "Connection timed out"

```
❌ Conexão SSH expirou
```

**Possíveis causas:**
1. **IP Mudou**: Verificar IP atual e atualizar
   ```bash
   nslookup betinalimpeza.com.br
   export BETINA_LOCAWEB_IP=<novo_ip>
   ```

2. **SSH Inativo**: Ativar no painel (válido por 3 horas)

3. **Firewall**: Se estiver em VPN/Firewall corporativo, pode bloquear porta 22

**Fallback Automático:** O MCP tenta FTP se SSH falhar

---

### Erro: "FTP login failed"

```
❌ FTP login failed
```

**Verificar:**
```bash
# Testar credenciais
export BETINA_LOCAWEB_PASSWORD="sua_senha"

python << 'PYEOF'
import ftplib
ftp = ftplib.FTP()
ftp.connect("187.45.240.49", 21, timeout=10)
ftp.login("betinalimpeza", os.environ["BETINA_LOCAWEB_PASSWORD"])
print("✅ FTP OK")
PYEOF
```

---

### Erro: "Permission denied" ao escrever

```
❌ 550 Permission denied
```

**Solução:** Verificar permissões de arquivo/pasta

```bash
# Via SSH (se disponível)
ssh betinalimpeza "chmod 755 public_html && chmod 644 public_html/*.php"

# Via FTP (script)
python << 'PYEOF'
from mcp_locaweb_server import LocalWebManager
import os

os.environ["BETINA_LOCAWEB_PASSWORD"] = "Esquilo08!!!!!"
manager = LocalWebManager()

# Tentar via SSH (mais seguro)
manager.ssh_enabled = True
result = manager.execute_ssh("chmod 755 public_html && chmod 644 public_html/*.php")
print(result)
PYEOF
```

---

## 🔐 Segurança

### Credenciais

**NUNCA fazer commit de credenciais!**

```bash
# ✅ Certo: Usar variáveis de ambiente
export BETINA_LOCAWEB_PASSWORD="..."
python mcp_locaweb_server.py

# ❌ Errado: Hardcoded no código
password = "Esquilo08!!!!!"
```

**Guardar em local seguro:**
- File: `~/.env` (local machine only)
- Vault: GitHub Secrets, Azure Key Vault, etc.
- Manager: `pass`, `1password`, `bitwarden`, etc.

### Chaves SSH

```bash
# Permissões corretas
chmod 600 ~/.ssh/id_rsa_betinalimpeza      # Chave privada
chmod 644 ~/.ssh/id_rsa_betinalimpeza.pub  # Chave pública
chmod 700 ~/.ssh                            # Diretório

# Verificar
ls -la ~/.ssh/id_rsa_betinalimpeza*
```

### IP Dinâmico

O servidor Locaweb pode ter IP público que muda. **Isso é normal.**

```bash
# O MCP detecta automaticamente via DNS
# Mas você pode forçar atualizar:
nslookup betinalimpeza.com.br
export BETINA_LOCAWEB_IP=<resultado>
```

---

## 📊 Status de Conectividade

### Verificar Tudo de Uma Vez

```bash
python << 'PYEOF'
import os
import json
from mcp_locaweb_server import LocalWebManager

# Carregar credenciais
os.environ["BETINA_LOCAWEB_PASSWORD"] = os.getenv("BETINA_LOCAWEB_PASSWORD", "")

manager = LocalWebManager()

print("=" * 70)
print("🔍 DIAGNÓSTICO DE CONECTIVIDADE")
print("=" * 70)

# Status
status = manager.get_status()
print("\n📋 Configuração:")
print(f"  Domain: {status['domain']}")
print(f"  IP: {status['ftp_host']}")
print(f"  SSH Habilitado: {status['ssh_enabled']}")
print(f"  SSH Key: {status['ssh_key']}")

# Testar FTP
print("\n🔗 Testando FTP...")
result = manager.list_files("public_html")
if result["success"]:
    print(f"  ✅ FTP OK ({result.get('count')} arquivos)")
else:
    print(f"  ❌ FTP Falhou: {result.get('error')}")

# Testar SSH
if status['ssh_enabled']:
    print("\n🔗 Testando SSH...")
    result = manager.execute_ssh("whoami")
    if result["success"]:
        print(f"  ✅ SSH OK (usuário: {result['stdout'].strip()})")
    else:
        print(f"  ❌ SSH Falhou: {result.get('error')}")
else:
    print("\n🔗 SSH não habilitado (use no painel)")

# WordPress health
print("\n📊 WordPress Health Check...")
health = manager.wordpress_health_check()
for check, value in health["checks"].items():
    icon = "✅" if value else "⚠️ "
    print(f"  {icon} {check}: {value}")

print("\n" + "=" * 70)
PYEOF
```

---

## 📚 Referência Rápida

```bash
# Carregar credenciais
source .env

# Habilitar SSH (3 horas)
export BETINA_LOCAWEB_SSH_ENABLED=true

# Testar tudo
python mcp_locaweb_server.py

# Verificar IP atual
nslookup betinalimpeza.com.br

# SSH direto (se ativo)
ssh -i ~/.ssh/id_rsa_betinalimpeza \
    -o HostKeyAlgorithms=ssh-rsa \
    betinalimpeza@187.45.240.49 \
    "whoami"

# FTP direto
ftp 187.45.240.49
# Login: betinalimpeza / Esquilo08!!!!!
```

---

## 📞 Suporte

| Item | Link/Info |
|------|-----------|
| **Painel Locaweb** | https://painelhospedagem.locaweb.com.br/dashboard/8291801 |
| **cPanel** | https://betinalimpeza.com.br:2083 |
| **Site** | https://betinalimpeza.com.br |
| **Diretório Raiz** | /home/betinalimpeza/public_html |
| **Ticket Suporte** | https://www.locaweb.com.br/painel/support |

---

## 🎯 Próximos Passos

- [ ] Configurar variáveis de ambiente (.env)
- [ ] Testar SSH (habilitar no painel por 3 horas)
- [ ] Executar `python mcp_locaweb_server.py` para validar
- [ ] Executar health check do WordPress
- [ ] Fazer backup inicial (wp-config.php)
- [ ] Integrar com seu cliente MCP preferido

---

## 📝 Histórico

| Data | Mudança |
|------|---------|
| 2026-09-17 | v1.0 - Lançamento inicial (SSH + FTP + Fallback) |

---

**Última Atualização**: 2026-09-17  
**Mantido por**: Infraestrutura (Claude Code)  
**Status**: ✅ Operacional
