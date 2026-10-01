# 🌐 Web Backup — Betinalimpeza.com.br

Backup completo do site para restauração rápida em novo servidor.

## 📋 Conteúdo

### Estrutura
```
web-backup/
├── .htaccess              ← Rewrite rules Apache (catálogos dinâmicos)
├── index.php              ← Entry point WordPress
├── wp-config.php.example  ← Configuração template (sanitizado)
├── wp-content/
│   ├── uploads/           ← Todas as imagens do WordPress (~200 MB)
│   ├── plugins/           ← Plugins legítimos apenas
│   └── themes/            ← Hello Elementor + custom
└── HTML/
    └── catalogo/
        ├── index.php      ← Router dinâmico V15
        ├── vendedoras.php ← Base de dados das vendedoras
        ├── me.html        ← Catálogo estático
        ├── produtos.js    ← Lista de produtos (787 itens)
        └── imagens/       ← Imagens dos produtos (~100 MB)
```

### Tamanho
- **wp-content/uploads**: ~200 MB (100+ imagens)
- **HTML/catalogo/imagens**: ~100 MB (787 imagens de produtos)
- **Código + config**: ~1 MB
- **Total**: ~301 MB (Git LFS)

## 🚀 Restauração Rápida

### Opção 1: Novo Servidor (Recomendado)
```bash
# Clonar repositório
git clone https://github.com/tmr6583/SITE.git
cd SITE && git lfs pull

# Restaurar completamente
bash scripts/restore_full.sh seu-dominio.com admin senha123 db_user db_pass
```

### Opção 2: Manual Passo-a-Passo
```bash
# 1. Fazer pull do Git LFS
git lfs pull

# 2. Copiar conteúdo
cp -r web-backup/wp-content/uploads public_html/wp-content/
cp -r web-backup/HTML/ public_html/
cp web-backup/.htaccess public_html/

# 3. Importar banco
gunzip < database-backup/betinalimpeza_*.sql.gz | mysql -u root -p betinalimpeza

# 4. Configurar URLs no WordPress
wp option update siteurl "https://seu-dominio.com"
wp option update home "https://seu-dominio.com"
```

## 📁 Descrição dos Arquivos

### `.htaccess`
Apache rewrite rules para catálogos dinâmicos:
```apache
RewriteRule ^catalogo/(.+?)/?$ /HTML/catalogo/index.php?vendedora=$1 [QSA,L]
RewriteRule ^catalogo/?$ /HTML/catalogo/index.php [QSA,L]
```

**URLs suportadas**:
- `/catalogo/` → Adriana (padrão)
- `/catalogo/adriana` → Adriana
- `/catalogo/simone` → Simone
- `/catalogo/silvana` → Silvana
- `/catalogo/mariaeduarda` → Maria Eduarda

### `wp-content/uploads/`
WordPress media library com:
- Imagens de posts/pages
- Assets do Elementor
- Logotipos e favicons
- Arquivos diversos

### `HTML/catalogo/`
Catálogo dinâmico (aplicação legada + modernização):

| Arquivo | Tamanho | Propósito |
|---------|---------|----------|
| `index.php` | 12 KB | Router principal (V15) |
| `vendedoras.php` | 2 KB | Base de dados PHP com vendedoras |
| `me.html` | 34 KB | Template HTML estático |
| `produtos.js` | 78 KB | Array com 787 produtos |
| `imagens/` | 100 MB | Imagens de produtos |

**Versão**: V15 (Remove `loading="lazy"` para eliminar race condition)

## 🔄 Sincronização com Servidor

Para manter backup atualizado:

```bash
# 1. Ativar SSH no painel (Locaweb)
# 2. Fazer backup
bash scripts/backup_full.sh

# 3. Commit e push
git add web-backup/ database-backup/
git commit -m "Backup: Conteúdo sincronizado (YYYYMMDD)"
git push origin 3.0
```

**Frequência recomendada**: Semanal ou após mudanças importantes

## 🧪 Validação

Verificar integridade antes de restaurar:

```bash
bash scripts/validate_integrity.sh
```

Verifica:
- ✅ Git LFS configurado
- ✅ Imagens presentes
- ✅ Banco de dados íntegro
- ✅ Arquivos críticos
- ✅ Estrutura do catálogo

## ⚠️ Segurança

### O que NÃO está incluso (propositalmente)
- ❌ Plugins trojans/malware
- ❌ WordPress core (~90 MB — reconstruível)
- ❌ Credenciais reais em wp-config.php
- ❌ Histórico de revisões WordPress

### Proteção
- ✅ Git LFS rastreia imagens com pointers
- ✅ `.gitattributes` previne commits de imagens grandes
- ✅ `wp-config.php.example` é sanitizado
- ✅ `.gitignore` protege `.env` e credenciais

## 📞 Suporte

- 🔴 **Problema**: Imagens não carregam
  - Verificar permissões: `chmod 755 wp-content/uploads`
  - Testar URL direto: `https://dominio.com/wp-content/uploads/arquivo.jpg`

- 🔴 **Problema**: Catálogos dinâmicos retornam 404
  - Verificar `.htaccess` foi copiado
  - Testar: `curl -I https://dominio.com/catalogo/adriana`
  - Ver error_log do Apache

- 🔴 **Problema**: Banco de dados não importa
  - Verificar tamanho: `du -h database-backup/*.sql.gz`
  - Testar decomposição: `gunzip -t database-backup/*.sql.gz`
  - Verificar credenciais MySQL

## 📚 Referência

- [BACKUP_PLAN.md](../BACKUP_PLAN.md) — Planejamento completo
- [scripts/restore_full.sh](../scripts/restore_full.sh) — Restauração automatizada
- [Contexto.md](../Contexto.md) — Documentação geral do projeto

---

**Última atualização**: 2026-10-01  
**Versão**: 1.0 (Backup Completo com Git LFS)  
**Tamanho**: ~301 MB (Git LFS)
