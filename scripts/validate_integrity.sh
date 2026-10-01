#!/bin/bash
# validate_integrity.sh - Validar integridade do backup

set -e

echo "🔍 VALIDANDO INTEGRIDADE DO BACKUP"
echo "═════════════════════════════════════════"
echo ""

ERRORS=0
WARNINGS=0

# 1. Verificar Git LFS
echo "✅ Verificação 1: Git LFS"
if git lfs ls-files | head -5 >/dev/null 2>&1; then
  LFS_COUNT=$(git lfs ls-files | wc -l)
  LFS_SIZE=$(git lfs ls-files | awk '{sum+=$3} END {print sum/1024/1024 " MB"}')
  echo "   ✅ Git LFS ativo"
  echo "     - Arquivos: $LFS_COUNT"
  echo "     - Tamanho: $LFS_SIZE"
else
  echo "   ⚠️  Git LFS não configurado"
  ((WARNINGS++))
fi

# 2. Verificar imagens
echo ""
echo "✅ Verificação 2: Imagens"
if [ -d "web-backup/wp-content/uploads" ]; then
  COUNT=$(find web-backup/wp-content/uploads -type f 2>/dev/null | wc -l)
  SIZE=$(du -sh web-backup/wp-content/uploads 2>/dev/null | cut -f1)
  echo "   ✅ wp-content/uploads"
  echo "     - Arquivos: $COUNT"
  echo "     - Tamanho: $SIZE"
else
  echo "   ❌ wp-content/uploads não encontrado"
  ((ERRORS++))
fi

if [ -d "web-backup/HTML/catalogo/imagens" ]; then
  COUNT=$(find web-backup/HTML/catalogo/imagens -type f 2>/dev/null | wc -l)
  SIZE=$(du -sh web-backup/HTML/catalogo/imagens 2>/dev/null | cut -f1)
  echo "   ✅ HTML/catalogo/imagens"
  echo "     - Arquivos: $COUNT"
  echo "     - Tamanho: $SIZE"
else
  echo "   ⚠️  HTML/catalogo/imagens não encontrado (será copiado no backup)"
  ((WARNINGS++))
fi

# 3. Verificar banco de dados
echo ""
echo "✅ Verificação 3: Banco de Dados"
DB_FILES=$(ls database-backup/betinalimpeza_*.sql.gz 2>/dev/null | wc -l)
if [ $DB_FILES -gt 0 ]; then
  LATEST=$(ls -t database-backup/betinalimpeza_*.sql.gz 2>/dev/null | head -1)
  SIZE=$(du -h "$LATEST" | cut -f1)
  echo "   ✅ SQL backup encontrado"
  echo "     - Arquivo: $(basename $LATEST)"
  echo "     - Tamanho: $SIZE"

  # Verificar integridade gzip
  if gunzip -t "$LATEST" 2>/dev/null; then
    echo "     - ✅ Arquivo íntegro (testado com gzip)"
  else
    echo "     - ❌ Arquivo corrompido!"
    ((ERRORS++))
  fi
else
  echo "   ⚠️  Nenhum backup SQL encontrado"
  ((WARNINGS++))
fi

# 4. Verificar arquivos críticos
echo ""
echo "✅ Verificação 4: Arquivos Críticos"
CRITICAL_FILES=(
  "web-backup/.htaccess"
  "web-backup/index.php"
)

for file in "${CRITICAL_FILES[@]}"; do
  if [ -f "$file" ]; then
    SIZE=$(du -h "$file" | cut -f1)
    echo "   ✅ $file ($SIZE)"
  else
    echo "   ⚠️  $file não encontrado"
    ((WARNINGS++))
  fi
done

# 5. Verificar estrutura do catálogo
echo ""
echo "✅ Verificação 5: Catálogo Dinâmico"
CATALOG_FILES=(
  "web-backup/HTML/catalogo/index.php"
  "web-backup/HTML/catalogo/vendedoras.php"
  "web-backup/HTML/catalogo/me.html"
  "web-backup/HTML/catalogo/produtos.js"
)

for file in "${CATALOG_FILES[@]}"; do
  if [ -f "$file" ]; then
    SIZE=$(du -h "$file" | cut -f1)
    echo "   ✅ $(basename $file) ($SIZE)"
  else
    echo "   ⚠️  $(basename $file) não encontrado"
    ((WARNINGS++))
  fi
done

# 6. Verificar scripts
echo ""
echo "✅ Verificação 6: Scripts de Restauração"
SCRIPTS=(
  "scripts/backup_full.sh"
  "scripts/restore_full.sh"
  "scripts/validate_integrity.sh"
)

for script in "${SCRIPTS[@]}"; do
  if [ -f "$script" ]; then
    echo "   ✅ $script"
  else
    echo "   ❌ $script não encontrado"
    ((ERRORS++))
  fi
done

# 7. Verificar documentação
echo ""
echo "✅ Verificação 7: Documentação"
DOC_FILES=(
  "BACKUP_PLAN.md"
  "web-backup/README.md"
  "database-backup/README.md"
)

for doc in "${DOC_FILES[@]}"; do
  if [ -f "$doc" ]; then
    echo "   ✅ $doc"
  else
    echo "   ⚠️  $doc não encontrado"
    ((WARNINGS++))
  fi
done

# 8. Tamanho total
echo ""
echo "✅ Verificação 8: Tamanho Total"
if [ -d "web-backup" ]; then
  WEB_SIZE=$(du -sh web-backup | cut -f1)
  echo "   📦 web-backup/: $WEB_SIZE"
fi

if [ -d "database-backup" ]; then
  DB_SIZE=$(du -sh database-backup | cut -f1)
  echo "   📦 database-backup/: $DB_SIZE"
fi

# 9. Git status
echo ""
echo "✅ Verificação 9: Git Status"
STAGED=$(git diff --cached --name-only 2>/dev/null | wc -l)
UNTRACKED=$(git ls-files --others --exclude-standard 2>/dev/null | wc -l)
echo "   📊 Arquivos staged: $STAGED"
echo "   📊 Não rastreados: $UNTRACKED"

# Resumo
echo ""
echo "════════════════════════════════════════════"
if [ $ERRORS -eq 0 ] && [ $WARNINGS -le 2 ]; then
  echo "✅ VALIDAÇÃO PASSOU"
  echo "   Erros: $ERRORS | Avisos: $WARNINGS"
  EXIT_CODE=0
elif [ $ERRORS -eq 0 ]; then
  echo "⚠️  VALIDAÇÃO COM AVISOS"
  echo "   Erros: $ERRORS | Avisos: $WARNINGS"
  EXIT_CODE=0
else
  echo "❌ VALIDAÇÃO FALHOU"
  echo "   Erros: $ERRORS | Avisos: $WARNINGS"
  EXIT_CODE=1
fi
echo "════════════════════════════════════════════"

exit $EXIT_CODE
