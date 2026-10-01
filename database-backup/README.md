# 🗄️ Database Backup — Betinalimpeza.com.br

Dumps SQL comprimidos do banco MySQL `betinalimpeza`.

## 📋 Conteúdo

### Estrutura
```
database-backup/
├── README.md (este arquivo)
└── betinalimpeza_YYYYMMDD.sql.gz  ← Backup SQL comprimido
```

### Informações do Banco

| Item | Valor |
|------|-------|
| **Nome** | betinalimpeza |
| **Servidor** | Locaweb (MySQL DBA) |
| **Tabelas** | ~50+ (WordPress + custom) |
| **Tamanho (comprimido)** | ~20 MB |
| **Tamanho (descompactado)** | ~200 MB |

### Conteúdo Incluído

✅ **WordPress Core**
- wp_users (usuários administrativos)
- wp_posts (artigos, páginas, posts custom)
- wp_postmeta (metadados de posts)
- wp_options (configurações do site)
- wp_links, wp_comments, etc.

✅ **Plugins**
- wp_elementor_* (dados do Elementor)
- wp_akismet_* (spam filter)
- wp_form_masks (dados de formulários)
- wp_whatsapp_* (configurações WhatsApp)

✅ **Custom**
- Tabelas de catálogos (se houver)
- Relacionamentos de produtos

## 🚀 Restauração

### Opção 1: Via Script Automatizado
```bash
bash scripts/restore_full.sh seu-dominio.com
```

### Opção 2: Manual
```bash
# 1. Identificar arquivo mais recente
ls -t database-backup/betinalimpeza_*.sql.gz | head -1

# 2. Descomprimir e importar
gunzip < database-backup/betinalimpeza_YYYYMMDD.sql.gz | mysql -u root -p betinalimpeza

# 3. Verificar importação
mysql -u root -p -e "SELECT COUNT(*) FROM betinalimpeza.wp_posts;"
```

### Opção 3: Novo Servidor (Completo)
```bash
# 1. Clonar repositório com Git LFS
git clone https://github.com/tmr6583/SITE.git
cd SITE && git lfs pull

# 2. Usar script de restauração
bash scripts/restore_full.sh novo-dominio.com admin senha123 wp_user wp_pass
```

## 🔄 Criar Novo Backup

### Via Script Automatizado
```bash
# 1. Ativar SSH no painel Locaweb (3 horas)
# 2. Executar backup
bash scripts/backup_full.sh

# 3. Commit e push
git add database-backup/
git commit -m "Backup: Banco de dados ($(date +%Y%m%d))"
git push origin 3.0
```

### Manual via SSH
```bash
ssh betina << 'EOF'
  # Fazer dump
  mysqldump -u root betinalimpeza | gzip > ~/betinalimpeza_backup.sql.gz
  
  # Copiar para máquina local (via FTP depois)
  ls -lh ~/betinalimpeza_backup.sql.gz
EOF

# Depois, baixar via FTP
ftp 187.45.240.49
> get betinalimpeza_backup.sql.gz database-backup/
```

## 🧪 Validação

### Verificar Integridade
```bash
# 1. Testar compressão
gunzip -t database-backup/betinalimpeza_*.sql.gz
# ✅ Saída: arquivo OK (ou aviso de erro)

# 2. Verificar tamanho
du -h database-backup/betinalimpeza_*.sql.gz
# ✅ Espera-se: ~20-30 MB

# 3. Contar tabelas (preview)
gunzip -c database-backup/betinalimpeza_*.sql.gz | grep "CREATE TABLE" | wc -l
# ✅ Espera-se: 40+ tabelas

# 4. Validação completa
bash scripts/validate_integrity.sh
```

### Restaurar para Teste
```bash
# 1. Criar banco teste
mysql -u root -p -e "CREATE DATABASE betinalimpeza_test;"

# 2. Importar
gunzip < database-backup/betinalimpeza_*.sql.gz | mysql -u root -p betinalimpeza_test

# 3. Verificar
mysql -u root -p -e "SELECT option_name, option_value FROM betinalimpeza_test.wp_options WHERE option_name='siteurl' OR option_name='home';"

# 4. Limpar
mysql -u root -p -e "DROP DATABASE betinalimpeza_test;"
```

## 📊 Histórico de Backups

| Data | Arquivo | Tamanho | Status |
|------|---------|---------|--------|
| 2026-10-01 | betinalimpeza_20261001.sql.gz | 22 MB | ✅ Testado |

## ⚠️ Considerações Importantes

### Antes de Restaurar
- [ ] Verificar espaço em disco (200 MB descompactado)
- [ ] Ter acesso ao MySQL/MariaDB
- [ ] Backup do banco antigo (se houver)
- [ ] Credenciais MySQL disponíveis

### Dados Sensíveis
- ✅ Senhas de usuários WordPress (salted hash bcrypt)
- ⚠️ Emails de usuários (visíveis)
- ⚠️ URLs internas (pode precisar atualizar)
- ⚠️ Credenciais de plugins (pode precisar reconfigurar)

### Após Restaurar
- [ ] Atualizar URLs do WordPress (siteurl, home)
- [ ] Testar login do admin
- [ ] Verificar plugins ativados
- [ ] Sincronizar imagens (wp-content/uploads)
- [ ] Testar catálogos dinâmicos
- [ ] Rodar health check: `wp cli wp-cli --info`

## 🔐 Segurança

### Proteção do Backup
- ✅ Arquivo gzip comprimido (~1/10 do tamanho)
- ✅ Stored em repositório Git (histórico + versionamento)
- ✅ Git LFS rastreia arquivo (não compromete repo)
- ✅ `.gitignore` protege credenciais em `.env`

### Exposição de Dados
- ⚠️ Repositório é **público** (considerar tornar **privado**)
- ⚠️ Banco SQL contém emails de usuários
- ⚠️ Senhas são hashed mas ainda sensíveis

**Recomendação**: Tornar repositório **PRIVADO** antes de commitar backup do banco com dados reais.

## 📞 Troubleshooting

### Erro: "Access denied for user 'root'@'localhost'"
```bash
# Verificar credenciais
mysql -u root -p -e "SELECT USER();"

# Usar wp-cli se disponível
wp db cli
```

### Erro: "The used command is not allowed with this MySQL version"
```bash
# SQL é muito novo para servidor antigo
# Solução: Edituar dump manualmente ou usar migração incremental
```

### Erro: "Disk space exhausted"
```bash
# Descompactar em etapas
gunzip -c database-backup/*.sql.gz | head -100000 | mysql -u root -p betinalimpeza
```

### Erro: "MySQL has gone away"
```bash
# Timeout durante importação longa
# Aumentar timeout e reintentar
mysql -u root -p --max_allowed_packet=1024M --net_read_timeout=3600 betinalimpeza < dump.sql
```

## 📚 Referência

- [BACKUP_PLAN.md](../BACKUP_PLAN.md) — Planejamento completo
- [web-backup/README.md](../web-backup/README.md) — Backup de arquivos
- [scripts/restore_full.sh](../scripts/restore_full.sh) — Restauração automatizada
- [Contexto.md](../Contexto.md) — Documentação geral

---

**Última atualização**: 2026-10-01  
**Versão**: 1.0 (Backup Completo)  
**Frequência recomendada**: Semanal  
**Próximo backup**: 2026-10-08
