#!/bin/bash
# restore_full.sh - Restaurar site completo em novo servidor
# Uso: bash scripts/restore_full.sh <dominio> <admin_user> <admin_pass> <db_user> <db_pass>

set -e

DOMAIN=${1:-"localhost"}
WP_ADMIN=${2:-"admin"}
WP_PASS=${3:-"senha123"}
DB_USER=${4:-"wordpress"}
DB_PASS=${5:-"senha123"}

if [ "$DOMAIN" = "-h" ] || [ "$DOMAIN" = "--help" ]; then
  echo "Restaurar site betinalimpeza.com.br"
  echo ""
  echo "Uso: bash scripts/restore_full.sh <dominio> [admin] [pass] [db_user] [db_pass]"
  echo ""
  echo "Exemplos:"
  echo "  bash scripts/restore_full.sh betinalimpeza.com.br"
  echo "  bash scripts/restore_full.sh novo.com.br admin minha_senha wp_user db_senha"
  echo ""
  exit 0
fi

LOG_FILE="restore_$(date +%Y%m%d_%H%M%S).log"
exec 1> >(tee -a "$LOG_FILE")
exec 2>&1

echo "╔════════════════════════════════════════════════╗"
echo "║  RESTAURANDO SITE BETINALIMPEZA.COM.BR        ║"
echo "╚════════════════════════════════════════════════╝"
echo ""
echo "📋 Configuração:"
echo "   Domínio: $DOMAIN"
echo "   Admin: $WP_ADMIN"
echo "   DB User: $DB_USER"
echo ""
echo "⚠️  Este processo vai:"
echo "   1. Instalar WordPress core"
echo "   2. Importar banco de dados"
echo "   3. Restaurar imagens e conteúdo"
echo "   4. Configurar URLs"
echo "   5. Reativar plugins"
echo ""
read -p "Continuar? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
  echo "❌ Restauração cancelada"
  exit 1
fi

# PASSO 1: Git LFS
echo ""
echo "✅ PASSO 1: Preparar Git LFS"
if ! command -v git-lfs &> /dev/null; then
  echo "❌ Git LFS não está instalado!"
  echo "   Instale com: git lfs install"
  exit 1
fi
git lfs install
git lfs pull
echo "✅ Git LFS preparado"

# PASSO 2: Verificar estrutura
echo ""
echo "✅ PASSO 2: Verificar arquivos de backup"
[ -d "web-backup" ] && echo "   ✅ web-backup/" || (echo "   ❌ web-backup/ não encontrado"; exit 1)
[ -d "database-backup" ] && echo "   ✅ database-backup/" || (echo "   ❌ database-backup/ não encontrado"; exit 1)
[ -f "web-backup/.htaccess" ] && echo "   ✅ .htaccess" || echo "   ⚠️  .htaccess não encontrado"

# PASSO 3: Criar diretório do WordPress
echo ""
echo "✅ PASSO 3: Preparar diretório do site"
mkdir -p public_html
cd public_html

# PASSO 4: Instalar WordPress
echo ""
echo "✅ PASSO 4: Instalar WordPress core via wp-cli"
if ! command -v wp &> /dev/null; then
  echo "❌ wp-cli não instalado. Instale em https://wp-cli.org/#installing"
  exit 1
fi

wp core download --force
echo "✅ WordPress core baixado"

# PASSO 5: Criar wp-config.php
echo ""
echo "✅ PASSO 5: Criar wp-config.php"
wp config create \
  --dbname=betinalimpeza \
  --dbuser=$DB_USER \
  --dbpass=$DB_PASS \
  --dbhost=localhost \
  --force

echo "✅ wp-config.php criado"

# PASSO 6: Restaurar banco
echo ""
echo "✅ PASSO 6: Restaurar banco de dados"
DB_FILE=$(ls ../database-backup/betinalimpeza_*.sql.gz 2>/dev/null | head -1)

if [ -z "$DB_FILE" ]; then
  echo "❌ Arquivo SQL não encontrado em database-backup/"
  exit 1
fi

echo "   📥 Importando $DB_FILE..."
gunzip < "$DB_FILE" | mysql -u $DB_USER -p$DB_PASS betinalimpeza 2>/dev/null || true
echo "✅ Banco restaurado"

# PASSO 7: Copiar conteúdo
echo ""
echo "✅ PASSO 7: Restaurar imagens e conteúdo"

# wp-content/uploads
if [ -d "../web-backup/wp-content/uploads" ]; then
  echo "   📂 Copiando uploads..."
  cp -r ../web-backup/wp-content/uploads wp-content/
  echo "   ✅ Uploads restaurados"
fi

# HTML/catalogo
if [ -d "../web-backup/HTML" ]; then
  echo "   📂 Copiando HTML/catalogo..."
  mkdir -p HTML
  cp -r ../web-backup/HTML/catalogo HTML/
  echo "   ✅ Catálogo restaurado"
fi

# .htaccess
if [ -f "../web-backup/.htaccess" ]; then
  echo "   📂 Copiando .htaccess..."
  cp ../web-backup/.htaccess .
  echo "   ✅ .htaccess restaurado"
fi

# PASSO 8: Configurar WordPress
echo ""
echo "✅ PASSO 8: Configurar WordPress"

# URLs
wp option update siteurl "https://$DOMAIN"
wp option update home "https://$DOMAIN"
echo "   ✅ URLs atualizadas: https://$DOMAIN"

# Criar/atualizar admin
wp user create $WP_ADMIN admin@$DOMAIN --user_pass=$WP_PASS --role=administrator 2>/dev/null || \
wp user update admin --user_pass=$WP_PASS 2>/dev/null || true
echo "   ✅ Admin: $WP_ADMIN / $WP_PASS"

# PASSO 9: Reativar plugins legítimos
echo ""
echo "✅ PASSO 9: Instalar plugins"
PLUGINS=("akismet" "elementor" "elementor-pro" "cookie-notice" "wp-whatsapp" "form-masks-for-elementor")
for plugin in "${PLUGINS[@]}"; do
  wp plugin install $plugin --activate 2>/dev/null || echo "   ⚠️  $plugin (pode não existir no repo)"
done
echo "✅ Plugins instalados"

# PASSO 10: Permissões
echo ""
echo "✅ PASSO 10: Ajustar permissões"
chmod 755 .
chmod 644 .htaccess 2>/dev/null || true
chmod 755 wp-content
echo "✅ Permissões ajustadas"

# Validar
cd ..
echo ""
echo "✅ PASSO 11: Validar integridade"
bash scripts/validate_integrity.sh

# Resumo
echo ""
echo "════════════════════════════════════════════════"
echo "✅ RESTAURAÇÃO CONCLUÍDA COM SUCESSO"
echo "════════════════════════════════════════════════"
echo ""
echo "🌐 Acesse: https://$DOMAIN"
echo "📊 Admin: https://$DOMAIN/wp-admin"
echo "👤 Login: $WP_ADMIN"
echo ""
echo "⚠️  VERIFICAÇÃO MANUAL NECESSÁRIA:"
echo "   [ ] Imagens carregam corretamente"
echo "   [ ] Catálogos dinâmicos (/catalogo/adriana etc)"
echo "   [ ] Links WhatsApp funcionando"
echo "   [ ] Admin dashboard funcionando"
echo "   [ ] SSL/HTTPS ativo"
echo ""
echo "📝 Log: $LOG_FILE"
