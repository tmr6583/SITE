# betinalimpeza.com.br — Documentação Técnica

Repositório de manutenção e gestão do site **betinalimpeza.com.br**, hospedado na Locaweb (hospedagem compartilhada), com acesso automatizado via SSH/FTP através de um MCP Server local.

> ⚠️ **Status de segurança**: o site está em processo de remediação após reinfecções por malware. Antes de qualquer operação, leia [`PLANO.md`](PLANO.md) — ele documenta o diagnóstico completo e o plano de ação em execução, incluindo pendências críticas ainda não resolvidas.

---

## 1. Visão Geral

| Item | Valor |
|------|-------|
| Domínio | betinalimpeza.com.br |
| CMS | WordPress 6.9.4 |
| Tema | Hello Elementor (Elementor + Elementor Pro) |
| Hospedagem | Locaweb — Plano **Hospedagem I** (compartilhada) |
| Data de contratação | 07/03/2016 |
| Usuário do servidor | `betinalimpeza` |
| Diretório raiz (painel) | `/home/betinalimpeza/` |
| Diretório raiz (real, via SSH) | `/home/storage2/b/f0/5b/betinalimpeza/public_html` |
| Domínio temporário | betinalimpeza.hospedagemdesites.ws |
| Endereço SSL compartilhado | https://betinalimpeza.websiteseguro.com |
| Painel Locaweb | https://painelhospedagem.locaweb.com.br/dashboard/8291801 |
| cPanel | https://betinalimpeza.com.br:2083 |

---

## 2. Infraestrutura

### 2.1 IP e DNS

- **IP Compartilhado atual (painel Locaweb)**: `187.45.240.49` — este IP **pode mudar periodicamente** (hospedagem compartilhada Locaweb).
- ⚠️ **Divergência conhecida (2026-09-18)**: `betinalimpeza.com.br` resolve hoje (DNS interno e público 8.8.8.8/1.1.1.1) para `187.45.240.67`, diferente do IP compartilhado informado pelo painel. O painel sinaliza **"DNS: Configurar"** para o domínio. O site continua funcionando normalmente nos dois IPs, mas essa pendência tende a **bloquear a renovação automática do certificado SSL**. Detalhes e plano de correção em [`PLANO.md`](PLANO.md) (Fase 6).

### 2.2 SSL

- Certificado atual: **Let's Encrypt**, válido para `betinalimpeza.com.br` e `www.betinalimpeza.com.br`.
- Validade: **16/07/2026 a 14/10/2026**.
- Painel Locaweb reporta **"Certificado SSL: DNS Pendente"** — consequência da pendência de DNS acima. Recomenda-se resolver antes do vencimento em 14/10/2026 para evitar queda de HTTPS.

### 2.3 Acesso ao servidor

| Método | Disponibilidade | Observações |
|--------|------------------|-------------|
| SSH | Requer ativação manual no painel (válido por 3h) | Alias configurado: `ssh betinalimpeza` |
| FTP | Sempre disponível | Usado como fallback automático pelo MCP Server |

Credenciais **nunca** ficam neste repositório — ver seção [5. Credenciais](#5-credenciais).

---

## 3. WordPress

### 3.1 Configuração

- Versão: **6.9.4**
- `DISALLOW_FILE_EDIT`: **ativado** (`true`) — edição de temas/plugins pelo `wp-admin` bloqueada
- Prefixo de tabelas: `wp_`

### 3.2 Plugins legítimos (ativos)

- `akismet`
- `analyticspro`
- `cookie-notice`
- `elementor` / `elementor-pro`
- `form-masks-for-elementor`
- `wp-whatsapp`

### 3.3 Itens pendentes de remoção (malware / legado)

> Ver [`PLANO.md`](PLANO.md) para o plano de remediação completo. **Nada foi removido ainda** — as informações abaixo refletem o estado real do servidor verificado em 2026-09-18.

- 7 diretórios de plugin trojanizados em `wp-content/plugins/`: `euvasmw`, `fzqpmlv`, `jnxokmg`, `npboopf`, `qmqmiaq`, `smpwrja`, `wbkftks`
- `really-simple-ssl_old` (plugin legado desativado)
- Webshell `wp-cron-swxh.php` na raiz do site
- Resíduos de sessões anteriores de manutenção: `admin_shell.php`, `delete_plugins.php` (⚠️ **`admin_shell.php` está publicamente acessível agora** — maior risco residual)
- Exposições públicas: `info.php`, `teste.php` (phpinfo), `bkp/` (110 MB, inclui phpMyAdmin exposto), `wp-config.php.bak_db_fix`
- Diretórios/arquivos de limpeza geral: `wordpress/` (instalador, 35 MB), `HTML/` (172 MB), `index-root.html`, `.htaccess.backup`, `php.ini_old`

---

## 4. Estrutura do Repositório

```
SITE/
├── README.md                  # Este arquivo — documentação técnica geral
├── CLAUDE.md                  # Contexto e instruções operacionais do projeto
├── PLANO.md                   # Diagnóstico de segurança e plano de remediação em execução
├── MCP.md                     # Documentação completa do MCP Server (setup, uso, troubleshooting)
├── mcp_locaweb_server.py      # MCP Server (acesso SSH+FTP com fallback automático)
├── test_mcp.sh                # Script de testes/validação do MCP Server
├── .env.example                # Template público de configuração (sem credenciais reais)
├── .env                        # Credenciais reais — LOCAL APENAS (.gitignore)
├── COFRE.md                    # Backup local de credenciais — LOCAL APENAS (.gitignore)
├── .gitignore
└── public_html/                # Arquivos críticos do WordPress (espelho do servidor)
    ├── wp-config.php
    ├── .htaccess
    ├── index.php / wp-load.php / wp-settings.php / ...
    ├── wp-content/
    ├── wp-admin/
    └── wp-includes/
```

---

## 5. Credenciais

**Nenhuma credencial real deve existir em arquivos versionados neste repositório.**

- Configuração local: copiar `.env.example` para `.env` e preencher os valores reais (nunca commitar `.env`)
- Backup local de referência: `COFRE.md` (nunca commitar)
- Em exemplos de código/documentação, usar sempre o placeholder `<verificar no cofre de secrets/vault>`

> 🚨 **Nota de segurança (2026-09-18)**: identificou-se que uma versão anterior do `MCP.md`, já corrigida, chegou a conter a senha de FTP/SSH em texto plano, commitada neste repositório **público** no GitHub. A credencial deve ser tratada como comprometida. Ver Fase 1B em [`PLANO.md`](PLANO.md) para o plano de remediação (troca de senha e avaliação de reescrita do histórico do Git).

---

## 6. MCP Server (automação de manutenção)

O `mcp_locaweb_server.py` fornece acesso programático ao site via SSH (quando ativo) com fallback automático para FTP — leitura/escrita/listagem de arquivos, execução de comandos SSH, backups, scan de padrões de malware e health check do WordPress.

Documentação completa de setup, uso e troubleshooting: [`MCP.md`](MCP.md).

Resumo rápido:

```bash
cp .env.example .env      # preencher com valores reais, nunca commitar
source .env
bash test_mcp.sh          # valida a configuração
python mcp_locaweb_server.py
```

---

## 7. Operação e Contexto do Projeto

Instruções operacionais, checklist antes/depois de manutenções, e convenções do projeto: ver [`CLAUDE.md`](CLAUDE.md).

Para o estado atual de segurança, fases já concluídas e pendências: ver [`PLANO.md`](PLANO.md) — é o documento vivo que reflete o progresso real da remediação em andamento.

---

## 8. Referências

| Item | Link |
|------|------|
| Painel Locaweb | https://painelhospedagem.locaweb.com.br/dashboard/8291801 |
| cPanel | https://betinalimpeza.com.br:2083 |
| Site | https://betinalimpeza.com.br |
| Repositório GitHub | https://github.com/tmr6583/SITE |
| Suporte Locaweb | https://www.locaweb.com.br/painel/support |

---

**Última atualização**: 2026-09-30

---

## 9. Catálogos Dinâmicos por Vendedora

### Visão Geral

Sistema implementado que permite **exibir o mesmo catálogo da empresa para múltiplas vendedoras**, mas com **encaminhamento de pedidos específico para o WhatsApp de cada uma**.

**URLs disponíveis:**
- `https://betinalimpeza.com.br/catalogo/` — Catálogo (pedidos → Adriana)
- `https://betinalimpeza.com.br/catalogo/adriana` — Catálogo (pedidos → Adriana)
- `https://betinalimpeza.com.br/catalogo/simone` — Catálogo (pedidos → Simone)
- `https://betinalimpeza.com.br/catalogo/silvana` — Catálogo (pedidos → Silvana)
- `https://betinalimpeza.com.br/catalogo/mariaeduarda` — Catálogo (pedidos → MariaEduarda)

### Componentes Técnicos

| Arquivo | Localização | Propósito |
|---------|-----------|-----------|
| `index.php` V11 | `/HTML/catalogo/index.php` | Router dinâmico com URL encoding |
| `vendedoras.php` | `/HTML/catalogo/vendedoras.php` | Base de dados PHP com array de vendedoras |
| `me.html` | `/HTML/catalogo/me.html` | Catálogo HTML completo (estático) |
| `produtos.js` | `/HTML/catalogo/produtos.js` | Dados de produtos em JSON |
| `Vendedoras.md` | Raiz do repositório | Documentação de vendedoras (referência legível) |
| `.htaccess` (rewrite rules) | `/public_html/.htaccess` | Apache rewrite: `/catalogo/X` → `/HTML/catalogo/index.php?vendedora=X` |
| `redirect-catalog-link.php` | `/wp-content/mu-plugins/` | Must-Use Plugin que altera link "Catálogo" da homepage |

### Versão Atual: V11 (2026-09-30)

**Problema resolvido**: Imagens com caracteres especiais (Ç, Á, É, etc) não carregavam porque o servidor esperava URLs com encoding.

**Solução**: JavaScript automático que faz `encodeURIComponent()` em TODOS os nomes de arquivo:
- Exemplo: `AÇÚCAR_UNIÃO_PREMIUM.jpg` → `A%C3%87%DCAR_UNI%C3%83O_PREMIUM.jpg`
- Resultado: Imagens agora carregam corretamente em todos os catálogos de vendedora

**Histórico de versões:**
- **V7**: Corrigir paths de imagens para absolutos
- **V8-V9**: Adicionar SVG base64 para placeholder "Sem Foto" (substituir via.placeholder.com)
- **V10**: ❌ Quebrou o site (rollback para V7)
- **V11**: ✅ Adicionar URL encoding para caracteres especiais (CURRENT)

### Como Usar

#### Acessar catálogo de vendedora
1. Clique no botão "Catálogo" na homepage de betinalimpeza.com.br
2. Ou navegue direto para: `https://betinalimpeza.com.br/catalogo/[nome-vendedora]`
3. Ao encaminhar um pedido via WhatsApp, a mensagem vai para o número da vendedora

#### Adicionar nova vendedora
1. Abra `/HTML/catalogo/vendedoras.php`
2. Adicione entrada ao array `$vendedoras`:
   ```php
   'novavendedora' => [
       'nome' => 'Nome Completo',
       'telefone' => '5524XXXXXXXXX'
   ]
   ```
3. Upload do arquivo
4. URL automática disponível: `/catalogo/novavendedora`

#### Alterar catálogo inteiro (produtos, descrições, layout)
1. Edite `/HTML/catalogo/me.html` ou `/HTML/catalogo/produtos.js`
2. Upload
3. Mudança reflete **automaticamente em TODAS as URLs** (`/catalogo/`, `/catalogo/adriana`, etc.)

### Documentação Relacionada

- [`Vendedoras.md`](Vendedoras.md) — Cadastro de vendedoras (tabela legível)
- [`catalogo_implementation/README.md`](catalogo_implementation/README.md) — Documentação técnica completa
- [`Contexto.md`](Contexto.md) — Seção 4 (detalhes arquiteturais)

---

**Última atualização**: 2026-09-30
