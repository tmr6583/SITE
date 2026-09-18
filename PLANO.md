# PLANO.md - Revisão Geral e Remediação de Segurança

**Data**: 2026-09-17  
**Status**: ✅ Diagnóstico Completo — Em Execução  
**Severidade**: 🚨 CRÍTICA

---

## Resumo Executivo

O site **betinalimpeza.com.br** foi **REINFECTADO em 16/09 às 21:51** (apenas 1 dia após a limpeza de 15/09) com uma nova onda de backdoors. A investigação via SSH revelou a **causa raiz: 3 contas administrativas fantasmas no WordPress** que permitem ao invasor logar a qualquer tempo e replantar malware.

**Enquanto essas contas existirem, qualquer limpeza de arquivos é TEMPORÁRIA.** A remediação exige, como primeiro passo obrigatório, a eliminação dessa persistência de acesso.

---

## Diagnóstico Resumido

### 🚨 Crítico — Causa Raiz

**3 contas administrativas maliciosas no banco de dados** (`ahmetkaya`, `ubeadmin`, `tnzadmin`):
- Todas com permissões de `administrator` no WordPress
- Permitem login em `/wp-admin` a qualquer momento
- Sem elas eliminadas, reinfecção é inevitável em dias/semanas

### 🚨 Crítico — Malware Ativo

1. **`wp-cron-swxh.php`** (raiz do site) — webshell ofuscado que aceita POST com comando arbitrário e **grava qualquer arquivo em qualquer local do site**
2. **7 plugins trojanizados** em `wp-content/plugins/` — cópias do plugin "Protect Uploads" com payload ofuscado injetado
3. Ambos foram recriados em 16/09 21:51 após limpeza anterior

### 🔴 Crítico — Exposições Públicas

- **`public_html/bkp/phpmyadmin/`** — phpMyAdmin acessível publicamente
- **`public_html/bkp/`** — aplicação/framework legado inteiro exposto
- **`wp-config.php.bak_db_fix`** — backup da config com credenciais expostas
- **`info.php`, `teste.php`** — phpinfo() público

### 🟠 Alto — Resíduos de Nossas Sessões Anteriores

- **`admin_shell.php`** — web shell que criamos (maior risco residual)
- **`delete_plugins.php`** — script auxiliar sem uso

### 🟡 Médio — Limpeza Geral

- 574 MB no total; 36 MB apenas do instalador WordPress
- Pastas `HTML/`, `wordpress/`, `index.html`/`index-root.html` duplicados
- Arquivos `.htaccess*`, `php.ini_old` antigos

---

## Plano de Ação (5 Fases)

### **FASE 1 — Conter o Invasor** ⚠️ OBRIGATÓRIA PRIMEIRO

1. ✅ Remover contas `ahmetkaya`, `ubeadmin`, `tnzadmin` via SQL — **CONCLUÍDO**
2. ⏳ Trocar senha do `admin` legítimo
3. ⏳ Regenerar chaves de segurança em `wp-config.php` (invalida todas as sessões)
4. ⏳ Trocar senha do banco de dados
5. ⏳ Trocar senha SSH/FTP de `betinalimpeza`
6. ⏳ Atualizar `.env` e `COFRE.md` locais

### **FASE 2 — Remover Malware**

- ⏳ Deletar `wp-cron-swxh.php`
- ⏳ Deletar 7 diretórios de plugin: `euvasmw`, `fzqpmlv`, `jnxokmg`, `npboopf`, `qmqmiaq`, `smpwrja`, `wbkftks`
- ⏳ Deletar `admin_shell.php`, `delete_plugins.php`, `info.php`, `teste.php`
- ⏳ Re-scan de padrões ofuscados (`eval`, `base64_decode`, etc.)

### **FASE 3 — Fechar Exposições**

- ⏳ Remover/mover `public_html/bkp/` para fora da raiz web
- ⏳ Remover `wp-config.php.bak_db_fix`
- ⏳ Remover `.htaccess2`, `.htaccess55`, `.htaccess_old`, `php.ini_old`

### **FASE 4 — Limpeza Geral**

- ⏳ Remover `public_html/wordpress/` (36 MB do instalador)
- ⏳ Avaliar remoção de `public_html/HTML/` (backup antigo)
- ⏳ Remover `index.html` / `index-root.html` duplicados
- ⏳ Remover plugin `really-simple-ssl_old` (legado desativado)

### **FASE 5 — Hardening**

- ⏳ Confirmar `DISALLOW_FILE_EDIT` ativo em `wp-config.php`
- ⏳ Auditar plugins legítimos (Elementor, Akismet, etc.) quanto a atualizações
- ⏳ Recomendar instalação de plugin de segurança (ex: Wordfence)
- ⏳ Monitorar `wp_users` nos próximos dias

---

## Verificação Pós-Execução

- ⏳ Fase 1: Query em `wp_users` retorna apenas `admin` legítimo  
- ⏳ Fase 2: `ls wp-content/plugins/` mostra apenas plugins conhecidos; re-scan sem resultados  
- ⏳ Fase 3: URLs de `bkp/` e backup retornam 404  
- ⏳ Fase 4: `du -sh public_html` cai significativamente  
- ⏳ Final: Rodar `manager.wordpress_health_check()` + `manager.scan_malware_patterns()` via MCP  
- ⏳ Monitorar em 48-72h para confirmar ausência de nova reinfecção  

---

## Credenciais Comprometidas

⚠️ **TRATAR COMO COMPROMETIDAS** após execução da Fase 1:
- Senha FTP de `betinalimpeza` → trocar em Fase 1
- `DB_PASSWORD` → trocar em Fase 1
- Qualquer outra conta/chave que possa ter sido exposta

---

## Documentação Relacionada

- **Diagnóstico Detalhado**: `C:\Users\tmrossi\.claude\plans\rippling-juggling-boole.md` (análise completa)
- **MCP Server**: `mcp_locaweb_server.py` — usar `manager.scan_malware_patterns()` e `manager.wordpress_health_check()` para validar fases
- **COFRE.md**: Backup local de credenciais — atualizar após Fase 1
- **CLAUDE.md**: Instruções do projeto — referência na frase "Última Atualização" após remediação completa

---

**Progresso**: 🟡 FASE 1A CONCLUÍDA (contas maliciosas deletadas) — Prosseguindo com Fase 1B
