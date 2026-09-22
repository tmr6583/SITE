# PLANO.md - Revisão Geral e Remediação de Segurança

**Data**: 2026-09-17 (atualizado em 2026-09-18)  
**Status**: ✅ Diagnóstico Completo — Em Execução  
**Severidade**: 🚨 CRÍTICA

---

## Resumo Executivo

O site **betinalimpeza.com.br** foi **REINFECTADO em 16/09 às 21:51** (apenas 1 dia após a limpeza de 15/09) com uma nova onda de backdoors. A investigação via SSH revelou a **causa raiz: 3 contas administrativas fantasmas no WordPress** que permitem ao invasor logar a qualquer tempo e replantar malware.

**Enquanto essas contas existirem, qualquer limpeza de arquivos é TEMPORÁRIA.** A remediação exige, como primeiro passo obrigatório, a eliminação dessa persistência de acesso.

**Atualização 2026-09-18**: auditoria somente-leitura (sem alterações no site) confirmou que a Fase 1 removeu de fato as contas fantasmas no banco (`wp_users` hoje tem apenas o `admin` legítimo), mas identificou um **segundo vetor de causa raiz possível**: a senha de FTP/SSH de `betinalimpeza` foi commitada em texto plano no `MCP.md`, em um **repositório GitHub público** (`github.com/tmr6583/SITE`). Enquanto essa credencial não for trocada (e tratada como comprometida), qualquer limpeza de malware também é temporária — reforça-se por isso uma nova fase de remediação abaixo (Fase 1B). Também foi identificada uma pendência de configuração de **DNS/SSL no painel Locaweb** (Fase 6), sinalizada na tela "Informações de domínio" do painel.

---

## Diagnóstico Resumido

### 🚨 Crítico — Causa Raiz #1 (Contas Fantasmas) — ✅ Eliminada

**3 contas administrativas maliciosas no banco de dados** (`ahmetkaya`, `ubeadmin`, `tnzadmin`):
- Todas com permissões de `administrator` no WordPress
- Permitem login em `/wp-admin` a qualquer momento
- Sem elas eliminadas, reinfecção é inevitável em dias/semanas
- **Verificado em 2026-09-18 via consulta somente-leitura ao banco**: `wp_users` contém hoje apenas 1 registro (`admin`, e-mail legítimo `nataliaespindola@gmail.com`) — as 3 contas fantasmas confirmadamente não existem mais.

### 🚨 Crítico — Causa Raiz #2 (Credencial Exposta) — ⏳ NÃO Remediada

**A senha de FTP/SSH da conta `betinalimpeza` está em texto plano no histórico do Git de um repositório GitHub PÚBLICO** (`github.com/tmr6583/SITE`, commit `c0aef3a` do arquivo `MCP.md`).
- Enquanto essa senha não for trocada, qualquer pessoa com acesso ao histórico do repositório pode acessar o site via FTP/SSH e replantar malware — **este é um vetor de reinfecção tão crítico quanto as contas fantasmas do WordPress**.
- O texto em texto plano já foi removido da versão atual do `MCP.md` em 2026-09-18, mas **permanece no histórico de commits** até uma reescrita de histórico ser executada.
- Ver Fase 1B abaixo.

### 🚨 Crítico — Malware Ativo — ⏳ Confirmado ainda presente (2026-09-18)

1. **`wp-cron-swxh.php`** (raiz do site) — webshell ofuscado que aceita POST com comando arbitrário e **grava qualquer arquivo em qualquer local do site**
2. **7 plugins trojanizados** em `wp-content/plugins/` — cópias do plugin "Protect Uploads" com payload ofuscado injetado
3. Ambos foram recriados em 16/09 21:51 após limpeza anterior
4. **Verificado em 2026-09-18** (via SSH, somente leitura): todos os 7 diretórios de plugin trojan (`euvasmw`, `fzqpmlv`, `jnxokmg`, `npboopf`, `qmqmiaq`, `smpwrja`, `wbkftks`) e o `wp-cron-swxh.php` continuam presentes no servidor — Fase 2 ainda não foi executada.
5. **`admin_shell.php`, `info.php`, `teste.php` estão publicamente acessíveis agora** (testado com GET simples, sem payload): retornam HTTP 200. `admin_shell.php` (residual das nossas próprias sessões) é o de maior risco — é uma web shell funcional e pública.

### 🔴 Crítico — Exposições Públicas — ⏳ Confirmado ainda presente (2026-09-18)

- **`public_html/bkp/phpmyadmin/`** — phpMyAdmin acessível publicamente (confirmado presente, 110 MB na pasta `bkp/`; a URL retornou HTTP 500 no teste, mas o diretório continua no servidor)
- **`public_html/bkp/`** — aplicação/framework legado inteiro exposto
- **`wp-config.php.bak_db_fix`** — backup da config com credenciais expostas (confirmado presente)
- **`info.php`, `teste.php`** — phpinfo() público (confirmado presente e **publicamente acessível**, HTTP 200)

### 🟠 Alto — Resíduos de Nossas Sessões Anteriores — ⏳ Confirmado ainda presente (2026-09-18)

- **`admin_shell.php`** — web shell que criamos (maior risco residual; confirmado presente e **publicamente acessível**, HTTP 200)
- **`delete_plugins.php`** — script auxiliar sem uso (confirmado presente)

### 🟡 Médio — Limpeza Geral — ⏳ Confirmado ainda presente (2026-09-18)

- 1,2 GB no total hoje (`du -sh public_html`); `bkp/` = 110 MB, `HTML/` = 172 MB, `wordpress/` (instalador) = 35 MB
- Pastas `HTML/`, `wordpress/`, `index-root.html` confirmadas presentes; `really-simple-ssl_old` também confirmado presente em `wp-content/plugins/`
- Arquivos `.htaccess.backup`, `php.ini_old` confirmados presentes na raiz

### 🟠 Alto — DNS/SSL do domínio (novo achado, painel Locaweb — 2026-09-18)

- O painel Locaweb ("Informações de domínio") sinaliza **DNS: Configurar** e **Certificado SSL: DNS Pendente**
- O **IP Compartilhado atual informado pelo painel é `187.45.240.49`**, mas `betinalimpeza.com.br` resolve hoje (confirmado via DNS interno e via 8.8.8.8/1.1.1.1) para **`187.45.240.67`**
- O site funciona normalmente hoje (HTTP 200, certificado Let's Encrypt válido servido em ambos os IPs, `notAfter=2026-10-14`), mas o painel não reconhece a configuração de DNS como concluída — isso tende a **bloquear a renovação automática do certificado SSL** quando o atual expirar (~26 dias a partir de 2026-09-18)
- Ver Fase 6 abaixo

---

## Plano de Ação (5 Fases)

### **FASE 1 — Conter o Invasor** ✅ CONCLUÍDA (3 de 5 passos automáticos)

1. ✅ Remover contas `ahmetkaya`, `ubeadmin`, `tnzadmin` via SQL — **CONCLUÍDO em 2026-09-18**
2. ✅ Trocar senha do `admin` legítimo — **CONCLUÍDO (hash MD5 atualizado)**
3. ✅ Regenerar chaves de segurança em `wp-config.php` (invalida todas as sessões) — **CONCLUÍDO (8 chaves novas)**
4. ⏳ Trocar senha do banco de dados — **PENDENTE (requer ação manual no painel Locaweb)**
5. ⏳ Trocar senha SSH/FTP de `betinalimpeza` — **PENDENTE (requer ação manual no painel Locaweb)**
6. ⏳ Atualizar `.env` e `COFRE.md` locais — **Aguardando conclusão de 4-5**

### **FASE 1B — Credencial Exposta em Repositório Público** 🚨 NOVA — PRIORIDADE MÁXIMA

> Sem esta fase, a Fase 1 está incompleta: a senha exposta é uma via de acesso tão válida quanto as contas fantasmas removidas.

1. ⏳ Confirmar se a senha FTP/SSH de `betinalimpeza` já foi trocada (Fase 1, passo 5, ainda pendente no painel) — se não, **tratar como prioridade máxima**, à frente até da Fase 2
2. ⏳ Trocar a senha FTP/SSH assim que confirmado (ação manual no painel Locaweb)
3. ✅ Remover o texto em claro do `MCP.md` na versão atual (feito em 2026-09-18 — substituído por referência ao cofre/vault)
4. ⏳ Decidir, com o proprietário do repositório, se será feita reescrita do histórico do Git (`git filter-repo` ou BFG Repo-Cleaner) para eliminar a senha antiga do histórico público — **ação destrutiva que reescreve commits e exige força no push; só executar mediante confirmação explícita**
5. ⏳ Auditar `git log` em busca de outras credenciais eventualmente commitadas (chaves SSH, tokens de API, etc.)
6. ⏳ Adicionar ao processo de revisão: nenhum arquivo versionado (`*.md`, código) deve conter segredos reais — usar sempre `.env`/`COFRE.md` (fora do controle de versão) ou placeholders

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

- ✅ Confirmar `DISALLOW_FILE_EDIT` ativo em `wp-config.php` — **verificado em 2026-09-18, ativo (`true`)**
- ⏳ Auditar plugins legítimos (Elementor, Akismet, etc.) quanto a atualizações
- ⏳ Recomendar instalação de plugin de segurança (ex: Wordfence)
- ⏳ Monitorar `wp_users` nos próximos dias

### **FASE 6 — Corrigir DNS e Renovação de SSL** 🆕 NOVA (achado do painel Locaweb, 2026-09-18)

> Objetivo: fazer o painel Locaweb reconhecer a configuração de DNS como concluída, garantindo a renovação automática do certificado SSL (atual expira em 2026-10-14) e alinhando o DNS público com o IP compartilhado atual do painel (`187.45.240.49`).

1. ⏳ No painel Locaweb, abrir a opção **"DNS: Configurar"** e seguir o assistente — provavelmente é necessário apontar os **NS (nameservers)** do domínio (no registro.br ou onde o domínio foi registrado) para os nameservers da Locaweb, ou ajustar o registro **A** para o IP compartilhado atual (`187.45.240.49`)
2. ⏳ Verificar onde o domínio `betinalimpeza.com.br` está registrado (registro.br ou outro) e onde a zona de DNS está de fato hospedada hoje — isso explica por que o domínio resolve para `187.45.240.67` e não para `187.45.240.49`
3. ⏳ Após corrigir o DNS, aguardar propagação e confirmar no painel que o status de **Certificado SSL** deixa de exibir "DNS Pendente"
4. ⏳ Confirmar renovação (manual ou automática) do certificado antes de 2026-10-14 para evitar queda de HTTPS
5. ⏳ Após corrigido, revalidar `mcp_locaweb_server.py`: a função `resolve_ip()` deve voltar a resolver para o IP compartilhado correto antes de confiar nela como fallback automático

---

## Verificação Pós-Execução

- ✅ Fase 1: Query em `wp_users` retorna apenas `admin` legítimo — **confirmado em 2026-09-18**
- ⏳ Fase 1B: senha FTP/SSH trocada e histórico do Git tratado
- ⏳ Fase 2: `ls wp-content/plugins/` mostra apenas plugins conhecidos; re-scan sem resultados — **ainda não, malware confirmado presente em 2026-09-18**
- ⏳ Fase 3: URLs de `bkp/` e backup retornam 404 — **ainda não, `admin_shell.php`/`info.php`/`teste.php` retornam HTTP 200 publicamente**
- ⏳ Fase 4: `du -sh public_html` cai significativamente — **ainda não, 1,2 GB em 2026-09-18**
- ⏳ Fase 6: painel Locaweb deixa de exibir "DNS: Configurar" / "SSL: DNS Pendente"
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

**Progresso** (atualizado 2026-09-18, após auditoria somente-leitura):
- 🟢 FASE 1 — Conter o Invasor (contas fantasmas): **CONCLUÍDA e verificada no banco**
- 🚨 FASE 1B — Credencial exposta no Git público: **NOVA, PRIORIDADE MÁXIMA, pendente**
- 🔴 FASE 2 — Remover Malware: **pendente** (webshells e 7 plugins trojan confirmados ainda no servidor)
- 🔴 FASE 3 — Fechar Exposições: **pendente** (`admin_shell.php`, `info.php`, `teste.php` publicamente acessíveis agora)
- 🟡 FASE 4 — Limpeza Geral: **pendente** (1,2 GB, `bkp/`, `HTML/`, `wordpress/` ainda presentes)
- 🟢 FASE 5 — Hardening: parcialmente concluída (`DISALLOW_FILE_EDIT` confirmado ativo)
- 🆕 FASE 6 — DNS/SSL: **nova**, a partir do achado do painel Locaweb (DNS não configurado / SSL pendente)
- ➡️ **Próxima Etapa recomendada**: confirmar/trocar a senha FTP/SSH (Fase 1B) antes ou junto com a Fase 2, já que a credencial exposta permite reinfecção mesmo após limpar os arquivos
