# CLAUDE.md - Betinalimpeza.com.br

## 🎯 Contexto do Projeto

**Site**: betinalimpeza.com.br  
**CMS**: WordPress 6.9.4 (Elementor)  
**Hospedagem**: Locaweb (Hospedagem Compartilhada)  
**IP**: 187.45.240.49 (⚠️ **Dinâmico - Pode Mudar**)  
**Acesso**: SSH (3h) + FTP (24/7) com fallback automático  
**Status**: ✅ Operacional (SSH + FTP + MCP testados)
**Último Update**: 2026-09-17

---

## 🎯 Objetivo

Manutenção, melhorias e gestão segura do site betinalimpeza.com.br através de MCP Server com acesso SSH/FTP na Locaweb.

---

## 📁 Arquivos Principais

| Arquivo | Propósito | Visibilidade |
|---------|-----------|--------------|
| `mcp_locaweb_server.py` | Servidor MCP (SSH+FTP+fallback) | GitHub ✅ |
| `MCP.md` | Documentação completa (650 linhas) | GitHub ✅ |
| `.env.example` | Template de configuração segura | GitHub ✅ |
| `.env` | Credenciais reais (LOCAL APENAS) | .gitignore 🔐 |
| `COFRE.md` | Backup de credenciais (LOCAL ONLY) | .gitignore 🔐 |
| `test_mcp.sh` | Script de testes/validação | GitHub ✅ |
| `CLAUDE.md` | Este arquivo (instruções projeto) | GitHub ✅ |
| `public_html/` | Arquivos críticos WordPress | GitHub ✅ |

---

## 🚀 Setup Rápido

### 1. Preparar Ambiente

```bash
cd /c/GitHubLocal/SITE

# Copiar template (já feito)
# cp .env.example .env

# Carregar credenciais
source .env

# Verificar SSH ativo no painel (3 horas)
# https://painelhospedagem.locaweb.com.br/dashboard/8291801
```

### 2. Testar MCP Server

```bash
# Executar testes
bash test_mcp.sh

# OU testar direto
python mcp_locaweb_server.py
```

### 3. Usar em Scripts

```python
from mcp_locaweb_server import LocalWebManager
import os

os.environ["BETINA_LOCAWEB_PASSWORD"] = os.getenv("BETINA_LOCAWEB_PASSWORD")
manager = LocalWebManager()

# Operações disponíveis
manager.read_file("public_html/wp-config.php")
manager.write_file("public_html/teste.php", "<?php phpinfo(); ?>")
manager.delete_file("public_html/arquivo.php")
manager.list_files("public_html")
manager.execute_ssh("whoami")  # Só se SSH ativo
manager.backup_config()
manager.scan_malware_patterns()
manager.wordpress_health_check()
```

---

## 📦 Funcionalidades do MCP Server

### Acesso SSH (Quando Ativo)
- ✅ Execução de comandos
- ✅ Máxima velocidade
- ✅ Operações avançadas
- ⚠️ Válido por 3 horas (requer ativação manual no painel)

### Acesso FTP (Sempre Disponível)
- ✅ Leitura/escrita de arquivos
- ✅ Listagem de diretórios
- ✅ Sem limite de tempo
- ✅ Fallback automático se SSH indisponível

### Funcionalidades Complementares
- ✅ Backups automáticos
- ✅ Scans de malware
- ✅ WordPress health checks
- ✅ Detecção de IP dinâmico via DNS
- ✅ Integração com Claude Code

---

## 🔐 Credenciais e Segurança

### Localização das Credenciais

```
.env              ← Arquivo local (NÃO commitar)
COFRE.md          ← Cópia local (NÃO commitar)
.env.example      ← Template seguro (PÚBLICO no GitHub)
```

### Como Usar Credenciais

```bash
# 1. Verificar COFRE.md para credenciais
cat COFRE.md

# 2. Usar com variáveis de ambiente
export BETINA_LOCAWEB_PASSWORD="..."
export BETINA_LOCAWEB_SSH_ENABLED=true

# 3. OU carregar do .env
source .env
```

### ⚠️ Segurança Crítica

- ❌ NUNCA commitar `.env` ou `COFRE.md`
- ❌ NUNCA compartilhar credenciais em chat/email
- ❌ NUNCA usar SSH em redes públicas
- ✅ SEMPRE usar .gitignore
- ✅ SEMPRE guardar em local seguro

---

## ⚡ Operações Comuns

### Verificar Saúde do WordPress

```bash
python << 'PYEOF'
from mcp_locaweb_server import LocalWebManager
import os

os.environ["BETINA_LOCAWEB_PASSWORD"] = os.getenv("BETINA_LOCAWEB_PASSWORD")
manager = LocalWebManager()

health = manager.wordpress_health_check()
print(health["checks"])
PYEOF
```

### Fazer Backup Rápido

```bash
python << 'PYEOF'
from mcp_locaweb_server import LocalWebManager
import os

os.environ["BETINA_LOCAWEB_PASSWORD"] = os.getenv("BETINA_LOCAWEB_PASSWORD")
manager = LocalWebManager()

backup = manager.backup_config()
print(f"Backup: {backup['backup_path']}")
PYEOF
```

### Escanear Malware

```bash
python << 'PYEOF'
from mcp_locaweb_server import LocalWebManager
import os

os.environ["BETINA_LOCAWEB_PASSWORD"] = os.getenv("BETINA_LOCAWEB_PASSWORD")
manager = LocalWebManager()

malware = manager.scan_malware_patterns()
print(f"Arquivos suspeitos: {malware['count']}")
for f in malware['suspicious_files']:
    print(f"  - {f}")
PYEOF
```

### Ler/Editar Arquivo

```bash
python << 'PYEOF'
from mcp_locaweb_server import LocalWebManager
import os

os.environ["BETINA_LOCAWEB_PASSWORD"] = os.getenv("BETINA_LOCAWEB_PASSWORD")
manager = LocalWebManager()

# Ler
result = manager.read_file("public_html/wp-config.php")
print(result["content"][:500])

# Editar
new_content = "<?php // Novo conteúdo ?>"
manager.write_file("public_html/teste.php", new_content)

# Deletar
manager.delete_file("public_html/arquivo.php")
PYEOF
```

### SSH Direto (se ativo)

**⚠️ IMPORTANTE: SSH precisa estar ativado no painel (válido 3 horas)**

```bash
# FORMA RECOMENDADA (usa alias do ~/.ssh/config)
ssh betinalimpeza "whoami"

# OU forma completa
ssh -i ~/.ssh/id_rsa_betinalimpeza \
    -o HostKeyAlgorithms=ssh-rsa \
    betinalimpeza@187.45.240.49 \
    "whoami"

# OU via MCP Server
python << 'PYEOF'
from mcp_locaweb_server import LocalWebManager
import os

os.environ["BETINA_LOCAWEB_SSH_ENABLED"] = "true"
manager = LocalWebManager()
result = manager.execute_ssh("ls -la public_html")
print(result["stdout"])
PYEOF
```

**Qual método usar?**
- `ssh betinalimpeza` = Recomendado (mais simples)
- Forma completa = Se alias não funcionar
- MCP Server = Para automação/scripts

---

## 📊 Status do WordPress

| Item | Status | Detalhes |
|------|--------|----------|
| **Versão** | ✅ 6.9.4 | Atualizado |
| **Tema** | ✅ Elementor | Hello Elementor |
| **Plugins** | 17 | Verificar em wp-content/plugins |
| **Debug** | ⚠️ Legado | PHP 5.2.17 (antigo) |
| **Segurança** | ✅ OK | DISALLOW_FILE_EDIT ativado |
| **Config** | ✅ OK | wp-config.php funcional |

---

## 🔧 Estrutura do Repositório

```
C:\GitHubLocal\SITE\
├── mcp_locaweb_server.py      (Servidor MCP)
├── MCP.md                      (Docs MCP - 650 linhas)
├── CLAUDE.md                   (Este arquivo)
├── test_mcp.sh                 (Testes)
├── .env.example                (Template)
├── .env                        (Credenciais - local)
├── COFRE.md                    (Backup credenciais - local)
├── .gitignore                  (Proteção .env/.ssh)
├── .git/                       (Repositório Git)
└── public_html/                (Arquivos WordPress)
    ├── wp-config.php
    ├── .htaccess
    ├── index.php
    ├── wp-load.php
    ├── wp-settings.php
    ├── wp-mail.php
    ├── wp-signup.php
    ├── wp-activate.php
    ├── wp-blog-header.php
    ├── wp-content/             (estrutura)
    ├── wp-admin/               (estrutura)
    └── wp-includes/            (estrutura)
```

---

## 📱 Como Usar Este Repositório

### Para Novos Desenvolvedores

1. **Clonar repositório**
   ```bash
   git clone https://github.com/tmr6583/SITE.git
   cd SITE
   ```

2. **Copiar template de credenciais**
   ```bash
   cp .env.example .env
   ```

3. **Preencher credenciais** (solicitar ao proprietário)
   ```bash
   # Editar .env com os valores reais
   # FTP password e SSH key path
   ```

4. **Testar acesso**
   ```bash
   source .env
   bash test_mcp.sh
   ```

5. **Usar MCP Server**
   ```bash
   python mcp_locaweb_server.py
   ```

### Para CI/CD (GitHub Actions, etc)

1. **Usar GitHub Secrets**
   ```yaml
   env:
     BETINA_LOCAWEB_PASSWORD: ${{ secrets.BETINA_LOCAWEB_PASSWORD }}
     BETINA_LOCAWEB_SSH_KEY: ${{ secrets.BETINA_LOCAWEB_SSH_KEY }}
   ```

2. **Nunca commitar .env**
   ```bash
   # Já protegido pelo .gitignore
   # Verificar antes de fazer push
   git status | grep .env  # Deve estar vazio
   ```

---

## 🚨 Troubleshooting

### SSH Não Funciona
- Verificar se está ativado no painel (3 horas)
- FTP funcionará como fallback automático
- Verificar IP: `nslookup betinalimpeza.com.br`

### FTP Não Funciona
- Verificar credenciais em COFRE.md
- Testar porta 21 aberta
- Usar MCP Server que trata erros

### IP Mudou
- MCP Server detecta automaticamente via DNS
- Se problema persistir: `export BETINA_LOCAWEB_IP=<novo_ip>`

---

## 📞 Referências

| Item | Link |
|------|------|
| **Painel Locaweb** | https://painelhospedagem.locaweb.com.br/dashboard/8291801 |
| **cPanel** | https://betinalimpeza.com.br:2083 |
| **Site** | https://betinalimpeza.com.br |
| **GitHub** | https://github.com/tmr6583/SITE |
| **Suporte** | https://www.locaweb.com.br/painel/support |

---

## ✅ Checklist Operacional

**Antes de começar:**
- [ ] .env criado com credenciais
- [ ] SSH ativado no painel (se necessário)
- [ ] MCP Server testado (`python mcp_locaweb_server.py`)

**Para manutenção:**
- [ ] Backup realizado (`manager.backup_config()`)
- [ ] Malware scaneado (`manager.scan_malware_patterns()`)
- [ ] Health check executado (`manager.wordpress_health_check()`)
- [ ] Mudanças testadas localmente

**Antes de fazer push:**
- [ ] .env NÃO está staged (`git status`)
- [ ] COFRE.md NÃO está staged
- [ ] SSH keys NÃO estão staged
- [ ] Commit message descritiva
- [ ] Push para GitHub

---

## 📚 Documentação

| Arquivo | Conteúdo |
|---------|----------|
| **MCP.md** | Setup, operações, troubleshooting do MCP Server |
| **CLAUDE.md** | Este arquivo (contexto do projeto) |
| **COFRE.md** | Referência segura de credenciais (local) |
| **.env.example** | Template público de configuração |

---

**Versão**: 2.0  
**Última Atualização**: 2026-09-17  
**Status**: ✅ Operacional com MCP Server completo

