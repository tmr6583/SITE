# Contexto — Betinalimpeza.com.br

Documento de contexto unificado para o projeto betinalimpeza.com.br. Consolida infraestrutura, configurações, memórias de decisões anteriores e status operacional — referência única para qualquer inteligência, desenvolvedor ou pessoa que trabalhe neste projeto.

**Última atualização**: 2026-10-01  
**Status**: ✅ Operacional (catálogos dinâmicos V15 com race condition resolvida, favicon Betina, pendências de segurança em seção 5)

---

## 1. 🎯 Visão Geral do Projeto

| Item | Valor |
|------|-------|
| **Domínio Principal** | betinalimpeza.com.br |
| **Proprietária** | Natalia Espindola (nataliaespindola@gmail.com) |
| **CMS** | WordPress 6.9.4 |
| **Tema Principal** | Hello Elementor (Elementor + Elementor Pro) |
| **Hospedagem** | Locaweb — Plano **Hospedagem I** (compartilhada) |
| **Tipo de Servidor** | Hospedagem Compartilhada (cPanel) |
| **Data de Contratação** | 07/03/2016 |
| **Painel Locaweb** | https://painelhospedagem.locaweb.com.br/dashboard/8291801 |
| **cPanel** | https://betinalimpeza.com.br:2083 |
| **GitHub** | https://github.com/tmr6583/SITE (repositório **público**) |

### Objetivo do Repositório

Manutenção, melhorias, backup e gestão segura do site betinalimpeza.com.br através de:
- MCP Server local com acesso SSH/FTP automatizado
- Documentação centralizada (este arquivo + MCP.md, PLANO.md, README.md)
- Versionamento de código/configurações críticas
- Registro de segurança e remediação

---

## 2. 🌐 Infraestrutura & Conectividade

### 2.1 Dados de Hospedagem

- **IP Compartilhado (painel)**: `187.45.240.49` ⚠️ **dinâmico** (pode mudar periodicamente)
- **Diretório Raiz (painel)**: `/home/betinalimpeza/`
- **Diretório Raiz (real, via SSH)**: `/home/storage2/b/f0/5b/betinalimpeza/public_html/` (symlink)
- **Usuário FTP/SSH**: `betinalimpeza`
- **Porta FTP**: 21 (sempre disponível)
- **Porta SSH**: 22 (requer ativação manual, válido 3h)

### 2.2 Domínios & SSL

| Tipo | Valor |
|------|-------|
| **Domínio Temporário** | betinalimpeza.hospedagemdesites.ws |
| **Endereço SSL Compartilhado** | https://betinalimpeza.websiteseguro.com |
| **Certificado SSL** | Let's Encrypt (compartilhado) |
| **Status SSL (painel)** | ⚠️ **DNS Pendente** |
| **Validade Atual** | 16/07/2026 a 14/10/2026 (~26 dias da data 2026-09-23) |
| **Status DNS (painel)** | ⚠️ **Configurar** |

#### ✅ DNS Corrigido (2026-09-23)
- **Antes (2026-09-18)**: `betinalimpeza.com.br` apontava para `187.45.242.67` (IP errado) e `www` apontava para `bebdinlimpa.com.br` (inativo)
- **Depois (2026-09-23)**: 
  - ✅ Entrada A raiz (`.`) corrigida para `187.45.240.49` (IP compartilhado correto)
  - ✅ CNAME `www` atualizado para `betinalimpeza.com.br` (domínio principal)
  - ✅ Todos os CNAMEs antigos para `bebdinlimpa.com.br` foram migrados
  - ✅ Registros ACME limpezados (lixo de validação SSL)
  - ✅ Banco de dados migrado para novo serviço Locaweb (mysql01)
- **Status**: Aguardando propagação de DNS (1-48h) e confirmação no painel de "DNS: Configurado" ✅ + "SSL: Validado" ✅
- **Detalhes**: Ver DIAGNOSTICO_DNS_POS_EXECUCAO.md e PLANO.md (Fase 6)

### 2.3 Acesso ao Servidor

#### SSH (quando ativo)
- ✅ Execução de comandos shell
- ✅ Máxima velocidade
- ✅ Operações avançadas (chown, chmod, etc.)
- ⚠️ **Válido por apenas 3 horas** após ativação manual no painel
- **Como ativar**: https://painelhospedagem.locaweb.com.br/dashboard/8291801 → Hospedagem → Ambientes → Acesso → SSH (checkbox)
- **Após ativar**: Defina `BETINA_LOCAWEB_SSH_ENABLED=true` e execute no terminal: `ssh betinalimpeza "whoami"`
- **Alias configurado**: `ssh betinalimpeza` (recomendado, usa `~/.ssh/config`)

#### FTP (sempre disponível)
- ✅ Leitura/escrita de arquivos
- ✅ Listagem de diretórios
- ✅ Sem limite de tempo (24/7)
- ✅ Usado como fallback automático se SSH indisponível

---

## 3. 📦 MCP Server — Automação de Manutenção

Sistema de automação local (Python) que fornece acesso programático ao site com SSH/FTP fallback.

### 3.1 Arquivos Relacionados

| Arquivo | Propósito | Visibilidade |
|---------|-----------|--------------|
| `mcp_locaweb_server.py` | Servidor MCP (~350 linhas, fully documented) | GitHub ✅ |
| `MCP.md` | Documentação completa (~700 linhas, exemplos) | GitHub ✅ |
| `test_mcp.sh` | Script de testes/validação | GitHub ✅ |
| `.env.example` | Template de configuração (sem credenciais) | GitHub ✅ |
| `.env` | Credenciais reais (LOCAL APENAS) | .gitignore 🔐 |

### 3.2 Classe LocalWebManager

**Localização**: `mcp_locaweb_server.py`

**Operações suportadas**:
- `read_file(path)` — Ler arquivo (SSH ou FTP)
- `write_file(path, content)` — Escrever/criar arquivo (SSH ou FTP)
- `delete_file(path)` — Deletar arquivo (SSH ou FTP)
- `list_files(path)` — Listar diretório (SSH ou FTP)
- `execute_ssh(command)` — Executar comando shell (SSH only)
- `backup_config()` — Backup de arquivos críticos
- `scan_malware_patterns()` — Scan de padrões de malware/ofuscação
- `wordpress_health_check()` — Health check do WordPress
- `get_status()` — Status de conectividade

### 3.3 Características Principais

1. **Fallback Automático**: Tenta SSH primeiro, cai para FTP se indisponível
2. **IP Auto-resolução**: Detecta mudanças de IP via DNS (`nslookup betinalimpeza.com.br`)
3. **Credentials via Env**: Nunca hardcoded, sempre via variáveis de ambiente
4. **Error Handling**: Mensagens claras de erro + recomendações

### 3.4 Como Usar

```bash
# Setup inicial
cp .env.example .env       # preencher com valores reais
source .env                # carregar variáveis

# Teste rápido
bash test_mcp.sh

# Python: uso programático
python << 'EOF'
from mcp_locaweb_server import LocalWebManager
import os

manager = LocalWebManager()
health = manager.wordpress_health_check()
print(health["checks"])
EOF
```

**Documentação completa**: [`MCP.md`](MCP.md) — setup, troubleshooting, exemplos avançados

---

## 4. 🛍️ Catálogos Dinâmicos por Vendedora

**Status**: ✅ Homologado e em Produção  
**Versão**: 1.5 (V15 — Remover `loading="lazy"` para eliminar race condition, implementado 2026-10-01)  
**Testes**: ✅ Investigação completa + solução implementada; race condition de timing resolvida

### Descrição

Sistema elegante onde **um único catálogo da empresa** é servido para todas as vendedoras, mas o botão "encaminhar pedido" direciona para o WhatsApp específico de cada vendedora.

**Conceito**: O catálogo é **idêntico** para todas as URLs; apenas o número de WhatsApp para encaminhamento de pedidos muda dinamicamente.

**URLs**:
```
https://betinalimpeza.com.br/catalogo/              → Catálogo empresa (pedido → Adriana)
https://betinalimpeza.com.br/catalogo/adriana       → Catálogo empresa (pedido → Adriana)
https://betinalimpeza.com.br/catalogo/simone        → Catálogo empresa (pedido → Simone)
https://betinalimpeza.com.br/catalogo/silvana       → Catálogo empresa (pedido → Silvana)
https://betinalimpeza.com.br/catalogo/mariaeduarda  → Catálogo empresa (pedido → MariaEduarda)
```

### Arquivos

| Arquivo | Local | Propósito | Tamanho |
|---------|-------|-----------|--------|
| `index.php` | `/HTML/catalogo/index.php` | Router dinâmico principal | ~12 KB |
| `vendedoras.php` | `/HTML/catalogo/vendedoras.php` | Base de dados PHP com array | ~2 KB |
| `Vendedoras.md` | Repositório raiz | Cadastro legível de vendedoras | ~1 KB |

### Rewrite Rules (.htaccess)

Adicionadas ao `/public_html/.htaccess`:

```apache
#### START Catálogos Dinâmicos

RewriteRule ^catalogo/(.+?)/?$ /HTML/catalogo/index.php?vendedora=$1 [QSA,L]
RewriteRule ^catalogo/?$ /HTML/catalogo/index.php [QSA,L]

#### END Catálogos Dinâmicos
```

### Vendedoras Cadastradas

Referência: [Vendedoras.md](Vendedoras.md)

| # | Nome | Telefone | URL | Status |
|---|------|----------|-----|--------|
| 1 | Adriana | +55 24 98854-1099 | `/catalogo/adriana` | ✅ Ativa |
| 2 | MariaEduarda | +55 24 98854-1101 | `/catalogo/mariaeduarda` | ✅ Ativa |
| 3 | Simone | +55 24 99229-8532 | `/catalogo/simone` | ✅ Ativa |
| 4 | Silvana | +55 24 98854-1098 | `/catalogo/silvana` | ✅ Ativa |

### Como Funciona (V15 — com Race Condition Resolvida)

1. **URL chega**: `/catalogo/adriana`
2. **Apache reescreve** para: `/HTML/catalogo/index.php?vendedora=adriana`
3. **PHP lê** `me.html` (catálogo completo - 702 linhas, 34.5 KB)
4. **PHP REMOVE `loading="lazy"`** de todas as imagens (V15: elimina race condition)
5. **PHP busca** dados da vendedora em `vendedoras.php`
6. **PHP injeta** script JavaScript que:
   - Define `window.VENDOR_PHONE = '5524988541099'`
   - Define `window.CATALOG_URL = 'https://betinalimpeza.com.br/HTML/catalogo'`
   - Define `window.CACHE_BUST = '?v=YmdH'` (cache-busting por hora)
   - **Faz `encodeURIComponent()` em TODOS os nomes de arquivo** antes de construir URLs finais
   - Corrige imagens com caracteres especiais (Ç, Á, É, etc) automaticamente
   - Sobrescreve função `sendWhatsApp()` para usar número da vendedora
7. **Página renderizada**: Idêntica para todas as vendedoras; apenas WhatsApp muda

**Histórico de soluções**:
- **V11**: URL encoding para caracteres especiais (Ç, Á, É)
- **V12**: V11 + Favicon Betina em catálogos
- **V13**: V12 + Cache-busting (?v=YmdH) para evitar cache de 404s
- **V14**: ❌ Rollback (bloqueio de imagens quebrou cabeçalho)
- **V15**: ✅ **V13 + Remover `loading="lazy"`** para eliminar race condition de timing

**Problema resolvido em V15**: 
- Causa: `loading="lazy"` fazia imagens carregar ANTES de `fixImagePaths()` corrigir os paths relativos
- Solução: Carregar imagem imediatamente, garantindo tempo para correção de path antes do navegador tentar acessar

### Adicionar Nova Vendedora

1. Editar `/HTML/catalogo/vendedoras.php`
2. Adicionar entrada ao array `$vendedoras`:
   ```php
   'novavendedora' => [
       'nome' => 'Nome Completo',
       'telefone' => '5524XXXXXXXXX',
   ]
   ```
3. Fazer upload
4. URLs automáticas disponíveis:
   - `/catalogo/novavendedora` → Catálogo com pedidos para esta vendedora

### Botão "Catálogo" da Homepage

**Status**: ✅ Implementado com Must-Use Plugin (2026-09-30)

O botão/link "Catálogo" da página inicial do betinalimpeza.com.br (criado em Elementor) agora aponta para `/catalogo`:
- Arquivo: `/wp-content/mu-plugins/redirect-catalog-link.php`
- Método: JavaScript na tag `wp_footer` que altera `href` no navegador
- Usuário vê URL como: `https://betinalimpeza.com.br/catalogo`

Quando clicado, o catálogo é exibido com pedidos encaminhados para a vendedora **Adriana** (padrão).

### Alterar Catálogo Inteiro

Mudanças no catálogo (produtos, descrições, layout):
1. Editar `/HTML/catalogo/me.html` diretamente
2. Upload
3. **Reflete automaticamente em TODAS as URLs** (`/catalogo/`, `/catalogo/adriana`, etc.)

### Investigação de Imagens "Sem Foto" (2026-10-01)

**Problema relatado**: Alguns produtos mostravam "Sem Foto" aleatoriamente em diferentes navegadores e catálogos

**Investigação realizada**:
1. ✅ Analisou estrutura de me.html (template literals JavaScript)
2. ✅ Testou 30+ requisições ao catálogo (reprodução de problema)
3. ✅ Verificou console/logs (timing de execução de scripts)

**Causa raiz identificada**: **Race condition de timing**
- `me.html` renderiza imagens via `render()` com paths relativos: `<img src="imagens/produto.jpg">`
- `loading="lazy"` faz imagem carregar QUANDO FICA VISÍVEL
- Se fica visível ANTES de `fixImagePaths()` corrigir o path → GET `/catalogo/imagens/...` → **404**
- Se fica visível DEPOIS → GET `/HTML/catalogo/imagens/...` → **200** ✅
- Timing varia por: velocidade da rede, CPU, cache do navegador

**Evidências**:
- ✅ Todas as 787 imagens referenciadas existem no servidor
- ✅ Servidor sempre retorna HTTP 200 quando testado
- ❌ 0/10 requisições tinham paths corretos no HTML renderizado (normal, é JavaScript)
- ✅ Scripts de correção presentes e executando

**Solução V15**: Remover `loading="lazy"` força carregamento imediato, eliminando a janela de timing

**Documentação técnica**: Ver [catalogo_implementation/](catalogo_implementation/) para detalhes de todas as versões (V2-V15)

### Documentação

Completa em: [catalogo_implementation/README.md](catalogo_implementation/README.md)  
Plano de deployment: [catalogo_implementation/DEPLOYMENT_PLAN.md](catalogo_implementation/DEPLOYMENT_PLAN.md)  
Histórico de versões: [catalogo_implementation/index_corrigido_v*.php](catalogo_implementation/) (V2-V15)

---

## 5. 📊 WordPress & Aplicação

### 5.1 Configuração Atual

| Item | Status | Detalhes |
|------|--------|----------|
| **Versão WordPress** | ✅ 6.9.4 | Atualizado |
| **Tema Principal** | ✅ Elementor | Hello Elementor |
| **Editor de Blocos** | ✅ Elementor | Editor drag-and-drop |
| **Plugins Legítimos** | 6 | akismet, analyticspro, cookie-notice, elementor, elementor-pro, form-masks-for-elementor, wp-whatsapp |
| **DISALLOW_FILE_EDIT** | ✅ Ativo (true) | Edição de código via wp-admin bloqueada |
| **Banco de Dados** | ✅ Funcional | MySQL via Locaweb |
| **wp_users** | ✅ OK | Apenas 1 admin legítimo (`admin`, email: nataliaespindola@gmail.com) |

### 4.2 Usuário Administrativo

- **Login**: `admin`
- **Email**: nataliaespindola@gmail.com
- **Status**: Legítimo, única conta com role `administrator`

**Nota**: Contas fantasmas maliciosas (`ahmetkaya`, `ubeadmin`, `tnzadmin`) foram removidas em 2026-09-18 — Fase 1 do PLANO.md.

### 4.3 Diretórios Críticos

```
public_html/
├── wp-config.php           (config crítica)
├── .htaccess               (rewrite rules)
├── index.php               (entry point)
├── wp-content/
│   ├── plugins/            (plugins WordPress + trojans pendentes de remoção)
│   ├── themes/             (temas)
│   └── uploads/            (mídia)
├── wp-admin/               (dashboard)
└── wp-includes/            (core WordPress)
```

---

## 6. 🚨 Segurança & Status de Remediação

### 5.1 Reinfecções por Malware (contexto histórico)

O site foi **reinfectado em 16/09/2026** (apenas 1 dia após limpeza de 15/09), causando investigação de segurança que identificou **duas causas raiz**:

#### Causa Raiz #1: Contas Administrativas Fantasmas ✅ REMEDIADA
- Três contas maliciosas no banco (`ahmetkaya`, `ubeadmin`, `tnzadmin`) permitiam login remoto
- **Status**: ✅ Removidas em 2026-09-18 (Fase 1, verificado via query ao banco)
- **Evidência**: wp_users contém hoje apenas o admin legítimo

#### Causa Raiz #2: Credencial Exposta em Repositório Público 🚨 CRÍTICA, NÃO REMEDIADA
- A senha de FTP/SSH foi commitada em texto plano no `MCP.md` (repositório GitHub **público**)
- **Status**: ❌ Pendente — senha deve ser tratada como **comprometida** enquanto não for trocada
- **Impacto**: Qualquer pessoa com acesso ao histórico do Git pode acessar o site via FTP/SSH
- **Ação recomendada**: Trocar senha FTP/SSH (Fase 1B em PLANO.md) + considerar reescrita do histórico do Git
- **Urgência**: MÁXIMA — esta é uma via de reinfecção tão válida quanto as contas fantasmas

### 5.2 Malware Presente (verificado 2026-09-18, ainda não removido)

> ⚠️ **Nada foi alterado no site** — apenas auditoria somente-leitura realizada

#### Webshells & Backdoors
- `wp-cron-swxh.php` (raiz) — webshell ofuscado, aceita POST com comando arbitrário
- `admin_shell.php` (raiz) — **residual de nossas sessões**, funcional e **publicamente acessível** (HTTP 200)
- `delete_plugins.php` (raiz) — script auxiliar, **publicamente acessível**

#### Plugins Trojanizados
- 7 diretórios em `wp-content/plugins/`: `euvasmw`, `fzqpmlv`, `jnxokmg`, `npboopf`, `qmqmiaq`, `smpwrja`, `wbkftks`
- Todas cópias do plugin "Protect Uploads" com payload ofuscado injetado

#### Exposições Públicas
- `bkp/` (110 MB) — aplicação/framework legado completamente exposto
- `bkp/phpmyadmin/` — phpMyAdmin acessível (retorna HTTP 500 por versão PHP, mas arquivo presente)
- `wp-config.php.bak_db_fix` — backup da config com credenciais expostas
- `info.php`, `teste.php` — phpinfo() público (HTTP 200)

**Status da limpeza**: Fase 2-4 do PLANO.md ainda **não executadas** — malware continua no servidor

### 5.3 Plano de Remediação em Execução

Documento: [`PLANO.md`](PLANO.md) — Diagnóstico completo + 6 fases de remediação

| Fase | Descrição | Status |
|------|-----------|--------|
| **Fase 1** | Conter o invasor (contas fantasmas) | ✅ Concluída |
| **Fase 1B** | Credencial exposta no Git público | 🚨 **NÃO REMEDIADA** — PRIORIDADE MÁXIMA |
| **Fase 2** | Remover malware (webshells, plugins trojans) | ⏳ Pendente |
| **Fase 3** | Fechar exposições públicas (bkp/, backups, info.php) | ⏳ Pendente |
| **Fase 4** | Limpeza geral (remover instalador WP, HTML/, etc.) | ⏳ Pendente |
| **Fase 5** | Hardening (plugins segurança, auditoria) | Parcial (DISALLOW_FILE_EDIT ✅) |
| **Fase 6** | Corrigir DNS/SSL do painel Locaweb | ⏳ Pendente |

**Recomendação**: Prioridade máxima para Fase 1B (trocar credencial) antes ou junto com Fase 2 — caso contrário, qualquer limpeza de malware será temporária.

---

## 7. 📁 Estrutura do Repositório

```
SITE/ (github.com/tmr6583/SITE — repositório PÚBLICO)
├── README.md                    # Documentação técnica geral
├── Contexto.md                  # Este arquivo (referência unificada)
├── CLAUDE.md                    # ⚠️ DEPRECATED — renomeado para Contexto.md
├── MCP.md                       # Documentação MCP Server (~700 linhas)
├── PLANO.md                     # Plano de diagnóstico e remediação de segurança
│
├── mcp_locaweb_server.py        # MCP Server (~350 linhas)
├── test_mcp.sh                  # Script testes/validação
│
├── .env.example                 # Template de configuração (público)
├── .env                         # Credenciais reais (LOCAL APENAS, .gitignore)
├── COFRE.md                     # Backup local de credenciais (LOCAL ONLY, .gitignore)
│
├── .gitignore                   # Proteção: .env, COFRE.md, chaves SSH, __pycache__
├── .git/                        # Repositório Git
│
└── public_html/                 # Espelho dos arquivos críticos do site
    ├── wp-config.php
    ├── .htaccess
    ├── index.php
    ├── wp-load.php
    ├── wp-settings.php
    ├── wp-mail.php
    ├── wp-signup.php
    ├── wp-activate.php
    ├── wp-blog-header.php
    ├── wp-content/              (estrutura de plugins/temas/uploads)
    ├── wp-admin/                (estrutura do painel)
    └── wp-includes/             (estrutura do core WordPress)
```

### Tamanho Disco (verificado 2026-09-18)
- Total `public_html/`: **1,2 GB**
  - `bkp/`: 110 MB (aplicação legado exposta)
  - `HTML/`: 172 MB (backup antigo)
  - `wordpress/` (instalador): 35 MB
  - Resto: ~900 MB (WordPress core, uploads, plugins)

---

## 8. 🔐 Credenciais & Segurança

### 7.1 Onde as Credenciais Ficam

| Local | Arquivo | Visibilidade | Ação |
|-------|---------|--------------|------|
| Local da máquina | `.env` | **NÃO commitar** (.gitignore) | Preencher manualmente |
| Local da máquina | `COFRE.md` | **NÃO commitar** (.gitignore) | Referência backup |
| GitHub (público) | `.env.example` | ✅ GitHub | Apenas template, sem valores reais |

### 7.2 Como Usar Credenciais

```bash
# Ler credenciais locais
cat COFRE.md

# Carregar em variáveis de ambiente
source .env

# Usar com MCP Server
python mcp_locaweb_server.py

# Ou em scripts
export BETINA_LOCAWEB_PASSWORD="<valor>"
export BETINA_LOCAWEB_SSH_ENABLED=true
```

### 7.3 Regras de Segurança (CRÍTICO)

- ❌ **NUNCA** commitar `.env` ou `COFRE.md`
- ❌ **NUNCA** compartilhar credenciais em chat/email/documentação
- ❌ **NUNCA** usar SSH em redes públicas sem VPN
- ❌ **NUNCA** escrever credenciais reais em exemplos de código
- ✅ **SEMPRE** usar `.gitignore` para arquivos sensíveis
- ✅ **SEMPRE** usar placeholders como `<verificar no cofre de secrets/vault>` em exemplos
- ✅ **SEMPRE** usar variáveis de ambiente (`os.getenv()`, `$ENV`) para carregar segredos em runtime

### 7.4 🚨 Achado de Segurança Crítico (2026-09-18)

- **Problema**: MCP.md (arquivo **público** no GitHub) continha a senha de FTP/SSH em texto plano
- **Commit**: c0aef3a (histórico público)
- **Status**: Texto já foi removido da versão atual (2026-09-18), mas permanece no histórico do Git
- **Impacto**: Credencial deve ser tratada como **comprometida** — requer imediata troca de senha
- **Ação**: Ver Fase 1B em PLANO.md (trocar senha + avaliar `git filter-repo` para limpar histórico)

---

## 8. 📱 Como Trabalhar neste Projeto

### 8.1 Para Novos Desenvolvedores / IAs

1. **Clonar o repositório**
   ```bash
   git clone https://github.com/tmr6583/SITE.git
   cd SITE
   ```

2. **Ler documentação de contexto** (você está aqui!)
   - Este arquivo: visão geral completa
   - [`README.md`](README.md): documentação técnica mais compacta
   - [`PLANO.md`](PLANO.md): status de segurança e remediação
   - [`MCP.md`](MCP.md): detalhe técnico do MCP Server

3. **Configurar ambiente local**
   ```bash
   cp .env.example .env
   # Editar .env com valores reais (solicitar ao proprietário)
   # ⚠️ NUNCA commitar .env
   ```

4. **Testar acesso**
   ```bash
   source .env
   bash test_mcp.sh
   ```

5. **Para operações no site**
   - Ativar SSH no painel (válido 3h): https://painelhospedagem.locaweb.com.br/dashboard/8291801
   - Executar `export BETINA_LOCAWEB_SSH_ENABLED=true`
   - Usar MCP Server ou SSH direto: `ssh betinalimpeza "whoami"`

### 8.2 Para CI/CD (GitHub Actions, etc.)

```yaml
env:
  BETINA_LOCAWEB_PASSWORD: ${{ secrets.BETINA_LOCAWEB_PASSWORD }}
  BETINA_LOCAWEB_SSH_KEY: ${{ secrets.BETINA_LOCAWEB_SSH_KEY }}
```

**Nunca**: commitar `.env`, chaves SSH privadas, ou tokens em repositório

### 8.3 Antes de Fazer Push

```bash
git status              # Verificar se .env está staged (❌ não deve estar)
git diff --cached       # Ver mudanças a commitar
git log -1              # Última commit

# Se algo sensível vazar por acidente
git reset HEAD <arquivo>  # remover do staging
git checkout <arquivo>    # descartar mudanças locais
```

---

## 9. 📚 Documentação Relacionada

| Arquivo | Propósito | Leitura Recomendada |
|---------|-----------|---------------------|
| **Contexto.md** | **Este arquivo** — Referência unificada do projeto | ✅ Leitura essencial |
| **README.md** | Documentação técnica compacta do site | ✅ Leitura essencial |
| **MCP.md** | Documentação completa do MCP Server (setup, exemplos, troubleshooting) | ✅ Se trabalhar com automação |
| **PLANO.md** | Diagnóstico de segurança + plano de remediação em execução | ✅ CRÍTICO — ler antes de qualquer operação |
| **CLAUDE.md** | ⚠️ DEPRECATED — foi renomeado para `Contexto.md` | Ignorar |
| `.env.example` | Template de configuração (público) | ✅ Para setup inicial |
| `COFRE.md` | Backup local de credenciais (não commitar) | Local apenas |

---

## 10. ✅ Checklist Operacional

### Antes de Começar Qualquer Tarefa

- [ ] Ler [`PLANO.md`](PLANO.md) para entender status de segurança
- [ ] Verificar se Fase 1B (credencial) já foi resolvida
- [ ] Confirmar SSH ativado no painel (se necessário)
- [ ] MCP Server testado (`bash test_mcp.sh`)

### Para Manutenção/Melhorias

- [ ] Fazer backup dos arquivos críticos (`manager.backup_config()`)
- [ ] Scan de malware antes e depois (`manager.scan_malware_patterns()`)
- [ ] Health check do WordPress (`manager.wordpress_health_check()`)
- [ ] Testar mudanças localmente em desenvolvimento
- [ ] Documentar em git commit + comentar em PLANO.md se necessário

### Antes de Fazer Push para GitHub

- [ ] `.env` **NÃO** está staged (`git status`)
- [ ] `COFRE.md` **NÃO** está staged
- [ ] Chaves SSH **NÃO** estão staged
- [ ] Nenhuma credencial ou token em código
- [ ] Commit message descritiva (qual fase? qual problema resolvido?)
- [ ] Atualizar PLANO.md se status de fase mudou
- [ ] Push para branch correto

---

## 11. 📞 Referências & Links

| Item | Link |
|------|------|
| **Painel Locaweb** | https://painelhospedagem.locaweb.com.br/dashboard/8291801 |
| **cPanel do Site** | https://betinalimpeza.com.br:2083 |
| **Site (Live)** | https://betinalimpeza.com.br |
| **Repositório GitHub** | https://github.com/tmr6583/SITE |
| **Suporte Locaweb** | https://www.locaweb.com.br/painel/support |
| **Domínio Temporário** | https://betinalimpeza.hospedagemdesites.ws |
| **SSL Compartilhado** | https://betinalimpeza.websiteseguro.com |

---

## 12. 📋 Memórias Consolidadas

Este documento consolida as seguintes memórias de sessões anteriores:

### Infraestrutura Locaweb
- IP compartilhado: 187.45.240.49 (dinâmico)
- Diretório raiz: /home/betinalimpeza/
- Plano de hospedagem: Hospedagem I (desde 07/03/2016)
- Domínios: betinalimpeza.com.br, betinalimpeza.hospedagemdesites.ws
- SSL status: DNS Pendente (Fase 6 em PLANO.md)

### MCP Server Implementation
- Arquitetura: SSH (3h) + FTP (24/7) com fallback automático
- Componente principal: classe LocalWebManager em mcp_locaweb_server.py
- Suporta: leitura/escrita/delete de arquivos, listagem, exec SSH, backups, malware scan, health checks
- Constraints: SSH requer ativação manual no painel (3 horas), IP dinâmico (resolve via DNS)
- Design rationale: robustez (fallback), segurança (env vars), flexibilidade (SSH + FTP), automação

---

## 13. 🔄 Histórico de Atualizações

| Data | O Que Mudou | Quem |
|------|-------------|------|
| 2026-09-17 | Criação de CLAUDE.md, MCP.md, setup inicial do MCP Server | Claude (anterior) |
| 2026-09-18 | Diagnóstico de segurança: credencial exposta (Fase 1B) + DNS/SSL pendente (Fase 6) | Claude (Sonnet 5) |
| 2026-09-18 | Criação de README.md, atualização de MCP.md (remoção de senha em texto plano) | Claude (Sonnet 5) |
| 2026-09-23 | Consolidação em Contexto.md (renomear CLAUDE.md, agregar memórias) | Claude (Haiku 4.5) |
| 2026-09-30 | Implementar catálogos dinâmicos V11-V12 (favicon, cache-busting) | Claude (Haiku 4.5) |
| 2026-10-01 | Investigação profunda de race condition em imagens; implementar V15 (remover loading="lazy") | Claude (Haiku 4.5) |

---

## 14. 🚀 Próximas Prioridades

**CRÍTICA (fazer já)**
1. Trocar senha FTP/SSH (Fase 1B) — qualquer outra limpeza é temporária enquanto isso não fizer
2. Decidir sobre limpeza de histórico do Git (`git filter-repo`) — senha ainda está visível no histórico

**ALTA (fases 2-4, depois)**
1. Remover malware: webshells, plugins trojans (Fase 2)
2. Fechar exposições: bkp/, info.php, admin_shell.php (Fase 3)
3. Limpeza geral: remover wordpress/, HTML/ (Fase 4)

**MÉDIA (Fase 6, antes do 14/10/2026)**
1. Corrigir DNS para apontar para IP compartilhado correto
2. Confirmar SSL renovação antes de expirar (14/10/2026)

---

**Leia também**: [`PLANO.md`](PLANO.md) para detalhes completos de cada fase da remediação.

**Última atualização deste arquivo**: 2026-09-23
