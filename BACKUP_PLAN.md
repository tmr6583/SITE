# 📋 Plano: Backup Completo do Site no GitHub

**Objetivo**: Clonar 100% do site betinalimpeza.com.br (código + imagens + banco de dados) para GitHub, garantindo restauração completa em outro servidor se Locaweb falhar.

**Data do Plano**: 2026-10-01  
**Urgência**: ALTA — Site está em produção com histórico de reinfecções

---

## 1. 📊 Situação Atual

### 1.1 Repositório GitHub
```
github.com/tmr6583/SITE (repositório PÚBLICO)
├── README.md, Contexto.md, PLANO.md, MCP.md
├── mcp_locaweb_server.py, test_mcp.sh
├── .env.example (template)
├── .gitignore
├── COFRE.md (credenciais locais, não commitado)
└── public_html/    (ESPELHO — apenas arquivos críticos, ~103 KB)
    ├── wp-config.php
    ├── .htaccess
    ├── index.php
    └── wp-content/ (estrutura apenas, sem uploads)
```

### 1.2 Site na Locaweb
```
/home/storage2/b/f0/5b/betinalimpeza/public_html/  (1.2 GB TOTAL)
├── wp-config.php                                  (crítico)
├── .htaccess                                      (crítico)
├── index.php                                      (crítico)
├── wp-content/
│   ├── plugins/                 (7 trojans + 6 legítimos, ~50 MB)
│   ├── themes/                  (Hello Elementor + custom, ~20 MB)
│   └── uploads/                 (IMAGENS — ~200 MB, não está no GitHub)
├── wp-admin/                    (core WP, ~30 MB)
├── wp-includes/                 (core WP, ~60 MB)
├── HTML/                        (aplicação legada, 172 MB, NÃO no GitHub)
│   └── catalogo/                (nossos catálogos dinâmicos)
│       ├── index.php            (V15 — está no GitHub)
│       ├── me.html              (NÃO no GitHub)
│       ├── produtos.js          (NÃO no GitHub)
│       ├── vendedoras.php       (NÃO no GitHub)
│       └── imagens/             (~100 MB de imagens)
├── bkp/                         (110 MB — legado exposto, deve remover)
├── wordpress/                   (35 MB — instalador, deve remover)
└── [outros arquivos de config/backup]

TOTAL: 1.2 GB
  - WordPress core (90 MB) ✓ — Reconstruível via composer/wp-cli
  - Plugins (50 MB) — Parcialmente no GitHub, trojans não devem ir
  - Imagens (300 MB) — NÃO no GitHub (PROBLEMA!)
  - Legado (182 MB: HTML/ + bkp/) — Parcialmente no GitHub
  - Config critica (1 MB) — ✓ Parcialmente no GitHub (.htaccess, algumas PHPs)
```

### 1.3 O Que Falta
- ❌ **Imagens** (`uploads/` + `HTML/catalogo/imagens/`) — ~300 MB
- ❌ **me.html** (catálogo) — não versionado
- ❌ **produtos.js** (produtos) — não versionado
- ❌ **Banco de dados** — apenas em produção (MySQL)
- ⚠️ **HTML/ legado** — parcialmente documentado
- ⚠️ **Trojans em plugins/** — não devem ser salvos

---

## 2. 🎯 Estratégia de Backup

### 2.1 Estrutura Proposta do Repositório (Fases)

```
SITE/
├── [Docs existentes]
├── [MCP + automação]
│
├── web-backup/                      ← NOVO: Backup completo do site
│   ├── wp-content/
│   │   ├── uploads/                 (Git LFS — 200 MB de imagens)
│   │   ├── plugins/                 (Limpo: apenas legítimos)
│   │   └── themes/
│   ├── HTML/
│   │   └── catalogo/
│   │       ├── index.php            (já existe no GitHub, duplicar aqui)
│   │       ├── me.html              (NOVO)
│   │       ├── produtos.js          (NOVO)
│   │       ├── vendedoras.php       (NOVO)
│   │       └── imagens/             (Git LFS — 100 MB)
│   ├── wp-config.php.example        (versão sanitizada)
│   ├── .htaccess                    (já existe, duplicar)
│   └── [outros arquivos críticos]
│
├── database-backup/                 ← NOVO: Backup MySQL
│   ├── betinalimpeza_YYYYMMDD.sql   (dump SQL comprimido)
│   └── SCHEMA.md                    (documentação schema)
│
├── scripts/                         ← NOVO: Scripts restauração
│   ├── backup_full.sh               (Fazer backup full via MCP)
│   ├── restore_full.sh              (Restaurar site completo)
│   ├── validate_integrity.sh        (Verificar integridade)
│   └── migrate_to_new_host.sh       (Migrate para novo servidor)
│
├── BACKUP_PLAN.md                   ← NOVO: Este documento (versão final)
└── .gitattributes                   ← NOVO: Configurar LFS
```

### 2.2 Decisões de Design

| Componente | Estratégia | Justificativa |
|-----------|-----------|---|
| **Imagens** | Git LFS (Large File Storage) | 300 MB é grande para Git normal; LFS track apenas pointers (texto) |
| **WordPress Core** | NÃO versionar | Reconstruível via composer/wp-cli; economiza espaço |
| **Plugins Trojanizados** | NÃO versionar | Segurança; usar `wp-cli` para reinstalar versões limpas na restauração |
| **Banco de Dados** | SQL dump comprimido + versionado | Texto, compressível; pequeno comparado com imagens |
| **HTML/ Legado** | Versionar (documentar uso) | Pode ter dependências; melhor ter cópia |
| **.htaccess** | Versionar (duplicado) | Crítico para rewrite rules |
| **wp-config.php** | Versionar como `.example` | Nunca commitir credenciais reais; usuário preenche na restauração |

### 2.3 Tamanho Estimado Final

```
Antes (Locaweb):             1.2 GB
  - WordPress core (90 MB)   — Remover (reconstruível)
  - Plugins trojans (20 MB)  — Remover (inseguro)
  - HTML/ legado (172 MB)    — Versionar
  - Imagens (300 MB)         — Versionar com LFS (pointers = ~50 KB)

GitHub (com LFS):            ~150 MB visível + 300 MB LFS
  - Código + docs: ~5 MB
  - HTML/ legado: ~50 MB
  - Imagens pointers: ~50 KB
  - [LFS armazenado separadamente]: 300 MB

Repositório limpo:           ~55 MB
Armazenamento LFS:           ~300 MB (contabilizado separadamente)
TOTAL GitHub: ~355 MB (muito melhor que 1.2 GB)
```

---

## 3. 📋 Fases de Implementação

### Fase 1: Preparação (Dia 1 — sem fazer push)

**Objetivo**: Estruturar repositório local, sem publicar ainda

#### 1.1 Configurar Git LFS
```bash
# Instalar Git LFS
git lfs install

# Criar .gitattributes
cat > .gitattributes << 'EOF'
# Imagens
*.jpg filter=lfs diff=lfs merge=lfs -text
*.jpeg filter=lfs diff=lfs merge=lfs -text
*.png filter=lfs diff=lfs merge=lfs -text
*.gif filter=lfs diff=lfs merge=lfs -text
*.webp filter=lfs diff=lfs merge=lfs -text
*.svg filter=lfs diff=lfs merge=lfs -text

# Arquivos grandes
*.zip filter=lfs diff=lfs merge=lfs -text
*.tar.gz filter=lfs diff=lfs merge=lfs -text
*.sql.gz filter=lfs diff=lfs merge=lfs -text
EOF

# Adicionar ao Git
git add .gitattributes
git commit -m "Setup: Configurar Git LFS para imagens e arquivos grandes"
```

#### 1.2 Criar Estrutura de Diretórios
```bash
mkdir -p web-backup/wp-content/{uploads,plugins,themes}
mkdir -p web-backup/HTML/catalogo/imagens
mkdir -p database-backup
mkdir -p scripts
```

#### 1.3 Fazer Download via MCP
```bash
# Usar MCP Server para fazer backup
ssh betina << 'EOF'
  # Listar arquivos a fazer backup
  ls -lah public_html/wp-content/uploads | wc -l
  du -sh public_html/HTML/catalogo/imagens
  ls -lah public_html/wp-config.php
  # ... etc
EOF
```

---

### Fase 2: Coleta de Conteúdo (Dias 1-2 — local)

**Objetivo**: Baixar tudo do servidor para máquina local

#### 2.1 Copiar Imagens via FTP/SSH
```bash
# Via MCP ou rsync (paralelo):
# - wp-content/uploads/ → web-backup/wp-content/uploads/
# - HTML/catalogo/imagens/ → web-backup/HTML/catalogo/imagens/
# - wp-content/plugins/ → web-backup/wp-content/plugins/ (somente legítimos)

# Comando via MCP (exemplo pseudocódigo):
manager.read_file("public_html/wp-content/uploads/...")
manager.write_file("./web-backup/wp-content/uploads/...")
```

#### 2.2 Copiar Arquivos Críticos
```bash
# Arquivos a copiar:
copy public_html/wp-config.php → web-backup/wp-config.php.example (sanitizar credenciais)
copy public_html/.htaccess → web-backup/.htaccess
copy public_html/index.php → web-backup/index.php
copy public_html/wp-load.php → web-backup/wp-load.php
copy public_html/wp-settings.php → web-backup/wp-settings.php
copy public_html/wp-blog-header.php → web-backup/wp-blog-header.php

# HTML/ Legado (mantém estrutura)
copy public_html/HTML/ → web-backup/HTML/ (completo)
```

#### 2.3 Fazer Dump do Banco de Dados
```bash
# Via SSH (quando ativo)
ssh betina << 'EOF'
  # Fazer dump completo
  mysqldump -u [user] -p[pass] betinalimpeza | gzip > ~/betinalimpeza_$(date +%Y%m%d).sql.gz
  # Copiar para máquina local via FTP
EOF

# Resultado: database-backup/betinalimpeza_20261001.sql.gz (~20 MB comprimido)
```

#### 2.4 Capturar Estrutura de Diretórios (Documentação)
```bash
# Criar mapa da estrutura do servidor
tree public_html/ > STRUCTURE_LOCAWEB.txt
du -h public_html/* | sort -h > DISK_USAGE.txt
find public_html -name "*.php" | wc -l > FILE_COUNTS.txt
```

---

### Fase 3: Limpeza e Validação (Dia 2 — local)

**Objetivo**: Remover arquivos desnecessários, sanitizar credenciais

#### 3.1 Remover Trojan Plugins
```bash
# NÃO copiar para GitHub:
rm web-backup/wp-content/plugins/euvasmw/
rm web-backup/wp-content/plugins/fzqpmlv/
rm web-backup/wp-content/plugins/jnxokmg/
rm web-backup/wp-content/plugins/npboopf/
rm web-backup/wp-content/plugins/qmqmiaq/
rm web-backup/wp-content/plugins/smpwrja/
rm web-backup/wp-content/plugins/wbkftks/

# Manter apenas legítimos (akismet, elementor, etc)
```

#### 3.2 Sanitizar Credenciais
```php
// web-backup/wp-config.php.example
<?php
// EXEMPLO — SUBSTITUIR COM VALORES REAIS NA RESTAURAÇÃO
define('DB_NAME', 'SEU_BANCO_DE_DADOS');
define('DB_USER', 'SEU_USUARIO_MYSQL');
define('DB_PASSWORD', '<verificar no cofre de secrets/vault>');
define('DB_HOST', 'localhost');
// ... resto da config
?>
```

#### 3.3 Validar Integridade de Imagens
```bash
# Verificar quantidade de arquivos
find web-backup/wp-content/uploads -type f | wc -l
find web-backup/HTML/catalogo/imagens -type f | wc -l

# Espera-se: ~787 arquivos (produtos.js contém 787 referências)

# Verificar corrupção
find web-backup -name "*.jpg" -o -name "*.png" | while read f; do
  file "$f" | grep -q "image" || echo "CORROMPIDO: $f"
done
```

#### 3.4 Criar MANIFEST.md
```markdown
# Backup Manifest

**Data**: 2026-10-01
**Servidor**: Locaweb (187.45.240.49)
**Banco**: betinalimpeza

## Conteúdo Incluído
- ✅ WordPress core structure
- ✅ Plugins legítimos (6 arquivos)
- ✅ Temas (Hello Elementor + custom)
- ✅ Uploads (~787 imagens, 200 MB)
- ✅ HTML/catalogo (aplicação legada)
- ✅ .htaccess (rewrite rules)
- ✅ wp-config.php.example
- ✅ Banco de dados (SQL dump)

## Conteúdo Excluído (Segurança)
- ❌ 7 plugins trojanizados (malware)
- ❌ WordPress core (~90 MB — reconstruível)
- ❌ wp-admin/ (reconstruível)
- ❌ wp-includes/ (reconstruível)
- ❌ Credenciais reais em wp-config.php

## Checklist Restauração
- [ ] Clonar repositório
- [ ] Instalar Git LFS (`git lfs install`)
- [ ] Fazer pull (`git lfs pull`)
- [ ] Criar wp-config.php com credenciais
- [ ] Importar database-backup/*.sql.gz
- [ ] Instalar WordPress core via wp-cli
- [ ] Instalar plugins legítimos via wp-cli
- [ ] Testar URLs de catálogos
- [ ] Verificar imagens carregam
```

---

### Fase 4: Estrutura de Scripts de Restauração (Dia 2)

**Objetivo**: Criar scripts que automatizam a restauração num novo servidor

#### 4.1 `scripts/backup_full.sh` — Fazer Backup

```bash
#!/bin/bash
# Fazer backup completo do site Locaweb e atualizar GitHub

set -e

# Config
BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="./web-backup"
DB_BACKUP_DIR="./database-backup"

echo "🔄 Iniciando backup completo..."

# 1. Ativar SSH (manual no painel — lembrança)
echo "⚠️  Certifique-se de ativar SSH no painel Locaweb (Dashboard > Acesso > SSH)"
echo "   Pressione Enter quando ativado..."
read

# 2. Fazer SSH para Locaweb
echo "📥 Baixando imagens via SSH..."
ssh betina << 'EOF'
  # Fazer dump do banco
  mysqldump -u root betinalimpeza | gzip > ~/betinalimpeza_backup.sql.gz
  
  # Preparar arquivo tar das imagens
  cd public_html
  tar czf ~/images_backup.tar.gz wp-content/uploads HTML/catalogo/imagens
EOF

# 3. Baixar via FTP
echo "📥 Copiando via FTP..."
# Script FTP aqui (lftp ou similar)

# 4. Extrair
tar xzf ~/images_backup.tar.gz -C $BACKUP_DIR

# 5. Git commit
git add web-backup/ database-backup/
git commit -m "Backup: Imagens e banco de dados ($BACKUP_DATE)

- Imagens: wp-content/uploads + HTML/catalogo/imagens
- Banco: betinalimpeza_$BACKUP_DATE.sql.gz
- Total: ~300 MB de imagens via Git LFS"

echo "✅ Backup feito. Agora: git push"
```

#### 4.2 `scripts/restore_full.sh` — Restaurar Site

```bash
#!/bin/bash
# Restaurar site completo num novo servidor

set -e

DOMAIN=$1
WP_ADMIN=$2
WP_PASS=$3
DB_USER=$4
DB_PASS=$5

if [ -z "$DOMAIN" ]; then
  echo "Uso: $0 <dominio> <admin_user> <admin_pass> <db_user> <db_pass>"
  exit 1
fi

echo "🔄 Restaurando site para $DOMAIN..."

# 1. Clonar repositório
git clone https://github.com/tmr6583/SITE.git site_restore
cd site_restore
git lfs install
git lfs pull

# 2. Instalar WordPress via wp-cli
wp core download --path=public_html/
wp config create --dbname=betinalimpeza --dbuser=$DB_USER --dbpass=$DB_PASS

# 3. Importar banco de dados
gunzip < database-backup/betinalimpeza_*.sql.gz | mysql -u $DB_USER -p$DB_PASS betinalimpeza

# 4. Copiar conteúdo
cp -r web-backup/wp-content/uploads/ public_html/wp-content/
cp -r web-backup/HTML/ public_html/
cp web-backup/.htaccess public_html/

# 5. Ajustar permissões
chmod 755 public_html
chmod 644 public_html/.htaccess
chmod 755 public_html/wp-content

# 6. Atualizar config WordPress
wp option update siteurl "https://$DOMAIN"
wp option update home "https://$DOMAIN"

# 7. Reativar plugins
wp plugin install akismet elementor elementor-pro --activate

# 8. Verificar integridade
bash scripts/validate_integrity.sh

echo "✅ Site restaurado para $DOMAIN"
echo "⚠️  TODO: Verificar imagens, testar catálogos, fazer testes E2E"
```

#### 4.3 `scripts/validate_integrity.sh` — Validar

```bash
#!/bin/bash
# Verificar integridade do backup/restauração

echo "🔍 Validando integridade..."

# 1. Contar imagens
IMAGES_COUNT=$(find web-backup -name "*.jpg" -o -name "*.png" | wc -l)
echo "  Imagens no backup: $IMAGES_COUNT"
[ "$IMAGES_COUNT" -gt 700 ] && echo "  ✅ OK" || echo "  ❌ ERRO: Faltam imagens!"

# 2. Verificar banco
if [ -f "database-backup/betinalimpeza_"*.sql.gz ]; then
  SIZE=$(du -h database-backup/betinalimpeza_*.sql.gz | cut -f1)
  echo "  Banco de dados: $SIZE"
  [ $(du -b < database-backup/betinalimpeza_*.sql.gz) -gt 1000000 ] && echo "  ✅ OK" || echo "  ❌ ERRO: Banco pequeno demais!"
fi

# 3. Verificar .htaccess
[ -f "web-backup/.htaccess" ] && echo "  ✅ .htaccess presente" || echo "  ❌ .htaccess faltando"

# 4. Verificar estrutura
[ -d "web-backup/HTML/catalogo" ] && echo "  ✅ HTML/catalogo presente" || echo "  ❌ HTML/catalogo faltando"

# 5. Testar Git LFS
echo "  Git LFS status:"
git lfs ls-files | wc -l | xargs echo "    Arquivos rastreados:"

echo "✅ Validação concluída"
```

---

### Fase 5: Documentação (Dia 2)

**Objetivo**: Criar guias de uso e restauração

#### 5.1 `BACKUP_PLAN.md` (versão final, versionada)
- Copiar plano completo para GitHub
- Incluir checklist de restauração
- Documentar cenários de falha

#### 5.2 `web-backup/README.md`
```markdown
# Web Backup — Betinalimpeza.com.br

Backup completo do site para restauração rápida em novo servidor.

## Conteúdo
- `wp-content/uploads/` — Todas as imagens do WordPress (~200 MB, Git LFS)
- `HTML/catalogo/` — Aplicação legada + imagens de produtos (~100 MB, Git LFS)
- `wp-config.php.example` — Configuração template (sanitizado)
- `.htaccess` — Rewrite rules Apache
- Arquivo de plugins legítimos

## Restauração Rápida
```bash
git clone https://github.com/tmr6583/SITE.git
cd SITE && git lfs pull
bash scripts/restore_full.sh seudominio.com admin senha db_user db_pass
```

Ver `BACKUP_PLAN.md` para detalhes.
```

#### 5.3 `database-backup/README.md`
```markdown
# Database Backup

Dumps SQL comprimidos do banco MySQL `betinalimpeza`.

## Arquivos
- `betinalimpeza_YYYYMMDD.sql.gz` — Backup SQL completo (gzipped)

## Restauração
```bash
gunzip < betinalimpeza_YYYYMMDD.sql.gz | mysql -u root -p betinalimpeza
```

## Atualizar
A cada 30 dias fazer novo dump:
```bash
mysqldump -u root -psenha betinalimpeza | gzip > database-backup/betinalimpeza_$(date +%Y%m%d).sql.gz
git add database-backup/
git commit -m "Backup: Banco de dados (YYYYMMDD)"
```
```

---

### Fase 6: Testes (Dia 3 — em sandbox)

**Objetivo**: Validar que restauração funciona

#### 6.1 Teste Local (Máquina Desenvolvimento)
```bash
# 1. Limpar diretório
rm -rf /tmp/test_restore

# 2. Clonar e restaurar
git clone file:///C/GitHubLocal/SITE /tmp/test_restore
cd /tmp/test_restore
git lfs pull

# 3. Rodar script de validação
bash scripts/validate_integrity.sh

# 4. Verificar Git LFS funcionando
git lfs ls-files | head -20

# 5. Contar arquivos
find web-backup -type f | wc -l
# Esperado: ~1000+ arquivos (imagens + config)
```

#### 6.2 Teste Servidor Staging (Opcional)
```bash
# Se tiver servidor staging (Digital Ocean, Linode, etc):
# 1. Provisionar droplet vazio
# 2. Instalar WordPress + MySQL
# 3. Correr restore_full.sh
# 4. Acessar https://staging.betinalimpeza.com.br
# 5. Testar:
#    - Carregamento de imagens
#    - Catálogos dinâmicos (/catalogo/adriana)
#    - WhatsApp links funcionando
#    - Admin login
#    - Banco de dados intacto
```

#### 6.3 Teste de Tamanho e Performance
```bash
# Verificar tamanho do repositório
du -sh .git/
git lfs ls-files | awk '{sum+=$3} END {print "LFS total:", sum/1024/1024, "MB"}'

# Espera-se:
#   .git/: ~100-150 MB
#   LFS: ~300 MB
#   Checkout: ~355 MB total
```

---

### Fase 7: Publicação (Dia 3)

**Objetivo**: Fazer push para GitHub e confirmar

#### 7.1 Verificar Antes de Push
```bash
# Checklist
[ -f .gitattributes ] && echo "✅ .gitattributes presente"
[ -d web-backup ] && echo "✅ web-backup/ presente"
[ -d database-backup ] && echo "✅ database-backup/ presente"
[ -d scripts ] && echo "✅ scripts/ presente"
git lfs ls-files | wc -l | xargs echo "✅ Arquivos LFS:"
```

#### 7.2 Push
```bash
git push origin 3.0
# Aguardar upload de ~300 MB (LFS)
# Pode demorar 10-30 min dependendo da conexão
```

#### 7.3 Verificar no GitHub
- Acessar https://github.com/tmr6583/SITE
- Verificar aba "Files" → web-backup/ visível
- Clicar em imagem → deve ver preview (LFS ativo)
- Verificar tamanho do repositório no topo

---

## 4. 🔒 Considerações de Segurança

| Item | Risco | Mitigação |
|------|-------|-----------|
| **Credenciais no backup** | Senhas/API keys expostas | Sanitizar wp-config.php → .example; usar `.env` na restauração |
| **Trojans no git history** | Infectar novo servidor | Nunca versionar plugins trojans; manter histórico limpo |
| **LFS tokens expirados** | Perda de acesso às imagens | GitHub LFS gratuito suficiente (~1 GB/mês); monitorar uso |
| **Repositório público** | Site code visível a todos | ✅ OK — site público mesmo; documentação é pública |
| **Banco SQL público** | Dados de usuários expostos | Considerar fazer repositório PRIVADO se houver dados sensíveis |

**Recomendação**: Fazer repositório PRIVADO antes de commitar banco de dados (wp_users + posts podem ter info sensível).

---

## 5. 📅 Cronograma Proposto

| Dia | Fase | Responsável | Duração |
|-----|------|-------------|---------|
| **2026-10-02** | 1. Preparação (Git LFS, estrutura) | IA | 1h |
| **2026-10-02** | 2. Coleta (Baixar files via SSH/FTP) | SSH ativo + script | 2-3h |
| **2026-10-03** | 3. Limpeza (Remover trojans, sanitizar) | IA | 1h |
| **2026-10-03** | 4. Scripts de restauração | IA | 1-2h |
| **2026-10-03** | 5. Documentação final | IA | 1h |
| **2026-10-04** | 6. Testes (Local + validação) | Manual/IA | 1-2h |
| **2026-10-04** | 7. Push para GitHub | IA | 30 min + upload |

**Total**: ~8-10 horas de trabalho + ~30 min upload LFS

---

## 6. 🚨 Pré-requisitos

### 6.1 Antes de Começar
- [ ] SSH ativado no painel Locaweb (válido 3 horas)
- [ ] Git LFS instalado: `git lfs install`
- [ ] Repositório PRIVADO (considerar mudança de público para privado)
- [ ] 500 MB livres no disco local
- [ ] Acesso FTP de backup (usuário `betinalimpeza`)

### 6.2 Verificações
```bash
# Verificar espaço
df -h | grep -E "/$"

# Verificar Git LFS
git lfs version

# Verificar acesso SSH
ssh betina "whoami"

# Verificar acesso FTP
ftp 187.45.240.49
```

---

## 7. 📊 Matriz de Mudanças

### Mudanças no Repositório GitHub

```
ANTES:                          DEPOIS:
├── README.md                   ├── README.md (atualizado)
├── Contexto.md                 ├── Contexto.md
├── MCP.md                      ├── MCP.md (referência LFS)
├── PLANO.md                    ├── PLANO.md
├── mcp_locaweb_server.py       ├── mcp_locaweb_server.py
├── test_mcp.sh                 ├── test_mcp.sh
├── .gitignore                  ├── .gitignore (atualizado)
├── .env.example                ├── .env.example
├── COFRE.md (LOCAL ONLY)       ├── COFRE.md (LOCAL ONLY)
├── public_html/                ├── public_html/ (espelho)
│   └── ... (103 KB)            │   └── ... (103 KB — sem mudança)
│                               │
│                               ├── .gitattributes (NOVO)
│                               ├── BACKUP_PLAN.md (NOVO)
│                               │
│                               ├── web-backup/ (NOVO)
│                               │   ├── README.md
│                               │   ├── wp-config.php.example
│                               │   ├── .htaccess
│                               │   ├── index.php
│                               │   ├── wp-content/
│                               │   │   ├── uploads/ (200 MB, LFS)
│                               │   │   ├── plugins/ (legítimos)
│                               │   │   └── themes/
│                               │   ├── HTML/
│                               │   │   └── catalogo/
│                               │   │       ├── me.html (NOVO)
│                               │   │       ├── produtos.js (NOVO)
│                               │   │       ├── vendedoras.php (NOVO)
│                               │   │       └── imagens/ (100 MB, LFS)
│                               │
│                               ├── database-backup/ (NOVO)
│                               │   ├── README.md
│                               │   └── betinalimpeza_20261001.sql.gz (~20 MB)
│                               │
│                               └── scripts/ (NOVO)
│                                   ├── backup_full.sh
│                                   ├── restore_full.sh
│                                   └── validate_integrity.sh
```

---

## 8. ✅ Checklist Final

### Antes de Começar
- [ ] Ler este plano inteiro
- [ ] Confirmar SSH disponível no painel
- [ ] Confirmar Git LFS instalado
- [ ] Backup local do `.env` (credenciais reais)

### Execução
- [ ] Fase 1 — Git LFS + estrutura
- [ ] Fase 2 — Download de conteúdo
- [ ] Fase 3 — Limpeza + sanitização
- [ ] Fase 4 — Scripts de restauração
- [ ] Fase 5 — Documentação
- [ ] Fase 6 — Testes locais
- [ ] Fase 7 — Push para GitHub

### Pós-Implementação
- [ ] Verificar repositório no GitHub (web-backup/ visível)
- [ ] Rodar `git lfs ls-files` — deve mostrar 300+ MB
- [ ] Testar clonar em máquina limpa
- [ ] Rodar validate_integrity.sh
- [ ] Documentar em Contexto.md que backup completo disponível

---

## 9. 🎯 Próximos Passos (Após Aprovação)

1. **Aprovação do Plano** — Você aprova este plano
2. **Iniciar Fase 1** — Preparar repositório (Git LFS, diretórios)
3. **Sincronização Manual** — Dados baixados do servidor
4. **Testes** — Validar em sandbox
5. **Documentação** — Atualizar Contexto.md com ref. a backup completo
6. **Alertas** — Considerar fazer repositório PRIVADO

---

**Plano Criado**: 2026-10-01  
**Status**: PLANEJAMENTO (aguardando aprovação)  
**Próxima Ação**: Usuário aprova → Iniciar Fase 1
