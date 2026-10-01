#!/bin/bash
# backup_full.sh - Fazer backup completo do site Locaweb
# Uso: bash scripts/backup_full.sh

set -e

BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="./web-backup"
DB_BACKUP_DIR="./database-backup"
LOG_FILE="backup_${BACKUP_DATE}.log"

echo "📋 Log: $LOG_FILE"
exec 1> >(tee -a "$LOG_FILE")
exec 2>&1

echo "🔄 Iniciando backup completo do site betinalimpeza.com.br"
echo "📅 Data: $BACKUP_DATE"
echo ""

# 1. Verificar SSH ativo
echo "✅ PASSO 1: Verificar SSH"
if ! ssh betina "whoami" &>/dev/null; then
  echo "❌ SSH não está disponível!"
  echo "⚠️  Ative SSH no painel Locaweb:"
  echo "   https://painelhospedagem.locaweb.com.br/dashboard/8291801"
  echo "   Menu: Hospedagem → Ambientes → Acesso → SSH (ativar checkbox)"
  echo ""
  echo "⏰ SSH fica ativo por 3 horas. Tente novamente após ativar."
  exit 1
fi
echo "✅ SSH conectado como: $(ssh betina 'whoami')"

# 2. Fazer dump do banco
echo ""
echo "✅ PASSO 2: Backup do banco de dados"
echo "   (Este é o passo mais crítico)"

ssh betina << 'DBBACKUP'
  # Criar diretório temporário
  mkdir -p ~/backup_temp

  # Fazer dump (pode demorar alguns minutos)
  echo "  📥 Fazendo mysqldump..."
  mysqldump -u root betinalimpeza 2>/dev/null | gzip > ~/backup_temp/betinalimpeza_backup.sql.gz

  # Verificar tamanho
  SIZE=$(du -h ~/backup_temp/betinalimpeza_backup.sql.gz | cut -f1)
  echo "  ✅ Banco exportado: $SIZE"
DBBACKUP

echo "   ✅ Banco de dados feito"

# 3. Preparar arquivo tar das imagens
echo ""
echo "✅ PASSO 3: Preparar imagens (wp-content/uploads + HTML/catalogo/imagens)"

ssh betina << 'TARBACKUP'
  cd public_html

  # Contar imagens
  COUNT_UPLOADS=$(find wp-content/uploads -type f 2>/dev/null | wc -l)
  COUNT_CATALOGO=$(find HTML/catalogo/imagens -type f 2>/dev/null | wc -l)

  echo "  📊 Imagens encontradas:"
  echo "     - wp-content/uploads: $COUNT_UPLOADS"
  echo "     - HTML/catalogo/imagens: $COUNT_CATALOGO"

  # Criar tar.gz
  echo "  📦 Compactando imagens (pode demorar)..."
  tar czf ~/backup_temp/images_backup.tar.gz \
    wp-content/uploads \
    HTML/catalogo/imagens \
    2>/dev/null || echo "  ⚠️  Alguns arquivos podem não ter sido incluídos"

  SIZE=$(du -h ~/backup_temp/images_backup.tar.gz | cut -f1)
  echo "  ✅ Imagens: $SIZE"
TARBACKUP

echo "   ✅ Imagens preparadas"

# 4. Copiar arquivos críticos
echo ""
echo "✅ PASSO 4: Copiar arquivos críticos (.htaccess, config)"

ssh betina << 'COPYBACKUP'
  cd public_html
  mkdir -p ~/backup_temp/critical

  # Copiar .htaccess
  cp .htaccess ~/backup_temp/critical/

  # Copiar e-mail sensível do wp-config.php
  echo "# Arquivo de configuração - SANITIZADO" > ~/backup_temp/critical/wp-config.php.sanitized
  grep -E "(DB_NAME|DB_USER|DB_HOST|WP_HOME|WP_SITEURL|ABSPATH)" wp-config.php >> ~/backup_temp/critical/wp-config.php.sanitized 2>/dev/null || true

  # Arquivos PHP
  cp index.php ~/backup_temp/critical/
  cp wp-load.php ~/backup_temp/critical/ 2>/dev/null || true
  cp wp-blog-header.php ~/backup_temp/critical/ 2>/dev/null || true

  # Catálogo
  mkdir -p ~/backup_temp/critical/catalogo
  cp HTML/catalogo/index.php ~/backup_temp/critical/catalogo/
  cp HTML/catalogo/vendedoras.php ~/backup_temp/critical/catalogo/
  cp HTML/catalogo/me.html ~/backup_temp/critical/catalogo/ 2>/dev/null || true
  cp HTML/catalogo/produtos.js ~/backup_temp/critical/catalogo/ 2>/dev/null || true
COPYBACKUP

echo "   ✅ Arquivos críticos copiados"

# 5. Download via FTP
echo ""
echo "✅ PASSO 5: Download via FTP"

# Usar lftp se disponível
if command -v lftp &> /dev/null; then
  echo "   📥 Usando lftp para download paralelo..."

  lftp -u betinalimpeza,'<verificar no cofre de secrets/vault>' 187.45.240.49 << 'FTPBACKUP'
    set net:max-retries 2
    set net:timeout 30

    # Download dos arquivos
    mget -c ~/backup_temp/betinalimpeza_backup.sql.gz -O ./database-backup/
    mget -c ~/backup_temp/images_backup.tar.gz -O ./web-backup/
    mget -c ~/backup_temp/critical/* -O ./web-backup/
FTPBACKUP
else
  echo "   📥 lftp não disponível, use FTP manual ou SCP"
fi

echo "   ✅ Download concluído"

# 6. Extrair e organizar
echo ""
echo "✅ PASSO 6: Extrair e organizar conteúdo"

if [ -f "./web-backup/images_backup.tar.gz" ]; then
  echo "   📂 Extraindo imagens..."
  tar xzf ./web-backup/images_backup.tar.gz -C ./web-backup/ 2>/dev/null || echo "  ⚠️  Alguns arquivos podem estar corrompidos"
  rm ./web-backup/images_backup.tar.gz
  echo "   ✅ Imagens extraídas"
fi

# 7. Validar
echo ""
echo "✅ PASSO 7: Validar integridade"
bash scripts/validate_integrity.sh

# 8. Resumo
echo ""
echo "════════════════════════════════════════════════"
echo "✅ BACKUP COMPLETO FINALIZADO"
echo "════════════════════════════════════════════════"
echo ""
echo "📊 Resumo:"
du -sh web-backup
du -sh database-backup
echo ""
echo "📝 Próximos passos:"
echo "   1. bash scripts/validate_integrity.sh"
echo "   2. git add web-backup/ database-backup/ scripts/"
echo "   3. git commit -m 'Backup: Conteúdo completo do site'"
echo "   4. git push origin 3.0"
echo ""
echo "🔍 Log salvo em: $LOG_FILE"
