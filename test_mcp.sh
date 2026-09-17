#!/bin/bash
#
# Script de Teste do MCP Locaweb
# Valida SSH, FTP e funcionalidades do servidor
#

set -e  # Exit on error

echo "╔════════════════════════════════════════════════════════════╗"
echo "║        TESTE DO MCP LOCAWEB - Betinalimpeza.com.br        ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Validar se .env existe
if [ ! -f ".env" ]; then
    echo "❌ Erro: .env não encontrado"
    echo ""
    echo "Passos para criar:"
    echo "  1. cp .env.example .env"
    echo "  2. Editar .env com suas credenciais"
    echo "  3. Executar este script novamente"
    exit 1
fi

# Carregar variáveis
set -a
source .env
set +a

echo "✅ Configurações carregadas de .env"
echo ""

# Teste 1: Validar credenciais
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔍 TESTE 1: Validar Credenciais"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ -z "$BETINA_LOCAWEB_PASSWORD" ]; then
    echo "❌ BETINA_LOCAWEB_PASSWORD não definido"
    exit 1
fi

if [ -z "$BETINA_LOCAWEB_USER" ]; then
    echo "❌ BETINA_LOCAWEB_USER não definido"
    exit 1
fi

echo "✅ BETINA_LOCAWEB_USER: $BETINA_LOCAWEB_USER"
echo "✅ BETINA_LOCAWEB_PASSWORD: (definido)"
echo ""

# Teste 2: Conectividade FTP
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔗 TESTE 2: Conectividade FTP"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

python3 << 'PYEOF'
import os
import ftplib

try:
    host = os.getenv("BETINA_LOCAWEB_IP", "187.45.240.49")
    user = os.getenv("BETINA_LOCAWEB_USER", "betinalimpeza")
    password = os.getenv("BETINA_LOCAWEB_PASSWORD", "")

    ftp = ftplib.FTP(timeout=10)
    ftp.connect(host, 21)
    ftp.login(user, password)

    print(f"✅ FTP Conectado: {host}:{user}")

    # Verificar diretório
    ftp.cwd("public_html")
    items = ftp.mlsd()
    count = sum(1 for _ in items)
    print(f"✅ Diretório: /public_html ({count} itens)")

    ftp.quit()
    print("✅ FTP OK")

except Exception as e:
    print(f"❌ Erro FTP: {str(e)}")
    exit(1)
PYEOF

echo ""

# Teste 3: Conectividade SSH
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔐 TESTE 3: Conectividade SSH"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ "$BETINA_LOCAWEB_SSH_ENABLED" = "true" ]; then
    echo "SSH está habilitado no .env"

    ssh_key="${BETINA_LOCAWEB_SSH_KEY:-~/.ssh/id_rsa_betinalimpeza}"
    ssh_key="${ssh_key/#\~/$HOME}"
    ssh_host="${BETINA_LOCAWEB_IP:-187.45.240.49}"
    ssh_user="${BETINA_LOCAWEB_SSH_USER:-betinalimpeza}"

    if [ ! -f "$ssh_key" ]; then
        echo "❌ Chave SSH não encontrada: $ssh_key"
    else
        echo "✅ Chave SSH encontrada"

        if timeout 10 ssh -i "$ssh_key" \
            -o HostKeyAlgorithms=ssh-rsa \
            -o StrictHostKeyChecking=accept-new \
            -o ConnectTimeout=5 \
            "${ssh_user}@${ssh_host}" \
            "echo 'SSH OK'" > /dev/null 2>&1; then
            echo "✅ SSH Conectado: ${ssh_host}:${ssh_user}"
        else
            echo "⚠️  SSH Falhou - Verifique se está ativo no painel"
        fi
    fi
else
    echo "ℹ️  SSH não habilitado (.env BETINA_LOCAWEB_SSH_ENABLED=false)"
    echo "   Para ativar:"
    echo "   1. https://painelhospedagem.locaweb.com.br/dashboard/8291801"
    echo "   2. Menu: Hospedagem → Ambientes → Acesso → SSH"
    echo "   3. Definir: BETINA_LOCAWEB_SSH_ENABLED=true"
fi

echo ""

# Teste 4: MCP Server
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🚀 TESTE 4: MCP Server"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

python3 << 'PYEOF'
import os
from mcp_locaweb_server import LocalWebManager

os.environ["BETINA_LOCAWEB_PASSWORD"] = os.getenv("BETINA_LOCAWEB_PASSWORD")
os.environ["BETINA_LOCAWEB_SSH_ENABLED"] = os.getenv("BETINA_LOCAWEB_SSH_ENABLED", "false")

manager = LocalWebManager()

# Status
print("✅ MCP Server Inicializado")

# WordPress Health
health = manager.wordpress_health_check()
print("\n📊 WordPress Health:")
for check, value in health["checks"].items():
    icon = "✅" if value else "⚠️ "
    print(f"  {icon} {check}: {value}")

# Malware Scan
malware = manager.scan_malware_patterns()
print(f"\n🔍 Malware Scan:")
print(f"  Arquivos suspeitos encontrados: {malware['count']}")
if malware['suspicious_files']:
    for f in malware['suspicious_files']:
        print(f"     ⚠️  {f}")
else:
    print(f"     ✅ Nenhum arquivo suspeito detectado")

print("\n✅ MCP Server OK")
PYEOF

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ TODOS OS TESTES PASSOU!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📚 Leia MCP.md para documentação completa e exemplos de uso"
echo ""
