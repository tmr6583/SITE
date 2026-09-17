# CLAUDE.md - Betinalimpeza.com.br

## 🎯 Contexto do Projeto

**Site**: betinalimpeza.com.br  
**CMS**: WordPress 6.9.4 (Elementor)  
**Hospedagem**: Locaweb (Hospedagem Compartilhada)  
**IP**: 187.45.240.49 (⚠️ **Dinâmico - Pode Mudar**)  
**Acesso**: SSH + FTP (fallback automático)  
**Status**: ✅ Operacional (SSH + FTP testados)

---

## 📦 Ferramentas Disponíveis

### 1. **MCP Server - Gerenciamento Completo** ⭐ LEIA [MCP.md](MCP.md)

```bash
# Setup Inicial
cp .env.example .env
# Editar .env com suas credenciais
source .env

# Testar tudo
python mcp_locaweb_server.py
```

**Funcionalidades Principais:**
- ✅ SSH quando habilitado no painel (~3 horas por ativação)
- ✅ FTP como fallback automático (sempre disponível)
- ✅ Leitura/escrita de arquivos (SSH → FTP)
- ✅ Execução de comandos via SSH
- ✅ Backups e restauração automática
- ✅ Scans de malware
- ✅ Health checks WordPress
- ✅ Detecção automática de IP dinâmico via DNS

### 2. **Acesso SSH Direto** (quando habilitado)

```bash
# 1. Habilitar SSH no painel por 3 horas:
#    https://painelhospedagem.locaweb.com.br/dashboard/8291801
#    Menu: Hospedagem → Ambientes → Acesso → SSH (ativar)

# 2. Conectar direto
ssh -i ~/.ssh/id_rsa_betinalimpeza \
    -o HostKeyAlgorithms=ssh-rsa \
    betinalimpeza@187.45.240.49 \
    "whoami"

# OU usar alias (configurado em ~/.ssh/config):
ssh betinalimpeza "whoami"
```

### 3. **Acesso FTP** (sempre disponível)

```bash
python << 'PYEOF'
from mcp_locaweb_server import LocalWebManager
import os

os.environ["BETINA_LOCAWEB_PASSWORD"] = "Esquilo08!!!!!"
manager = LocalWebManager()

# Listar arquivos
result = manager.list_files("public_html")
for f in result.get("files", []):
    print(f['name'])

# Ler arquivo
result = manager.read_file("public_html/wp-config.php")
print(result["content"][:200])

# Escrever arquivo
manager.write_file("public_html/teste.php", "<?php phpinfo(); ?>")

# Via SSH (se ativo)
manager.ssh_enabled = True
result = manager.execute_ssh("ls -la public_html")
print(result["stdout"])
PYEOF
```

---

## ⚠️ ACHADOS CRÍTICOS

### Arquivos Suspeitos Encontrados (6)
```
fdqaczzn.php       (19.5 KB - Sep 6)
innrlfms.php       (7.3 KB - Sep 2)
memmlpch.php       (12.7 KB - Sep 10)
qdgvmhua.php       (10.5 KB - Sep 8)
qdksfqyc.php       (19.5 KB - Sep 12)
znhtzrxn.php       (21 KB - Sep 11)
```

**Ação recomendada**: 
- [ ] Verificar conteúdo desses arquivos
- [ ] Se forem malware, deletar
- [ ] Atualizar WordPress

---

## 📋 Informações do Site

| Item | Status |
|------|--------|
| **WordPress** | Instalado (versão desconhecida) |
| **Tema Ativo** | Elementor / Hello Elementor |
| **Plugins** | 17 instalados |
| **Debug Mode** | ATIVADO (⚠️ desativar em produção) |
| **File Edit** | Desabilitado ✅ |
| **Estrutura** | /public_html (padrão) |

---

## 🔧 Procedimentos Comuns

### Ler Arquivo
```python
# Via MCP Server ou manual
python -c "
from mcp_betinalimpeza_server import get_client
client = get_client()
print(client.read_file('wp-config.php'))
"
```

### Editar Arquivo PHP
```bash
# 1. Baixar via FTP
# 2. Editar localmente
# 3. Fazer upload

# Ou via MCP Server em uma linha
```

### Limpar Cache WordPress
```bash
# Via FTP: deletar wp-content/cache
# Ou: chamar wp-cli se disponível
```

---

## 🔐 Credenciais

```
FTP Host: 187.45.240.49
FTP User: betinalimpeza
FTP Pass: verificar no cofre
```

**Nota**: Nunca commitar credenciais. Usar variáveis de ambiente:
```bash
export BETINA_FTP_PASSWORD="xxx"
```

---

## 📞 Suporte

- **Painel Locaweb**: https://painelhospedagem.locaweb.com.br/dashboard/8291801
- **cPanel**: https://betinalimpeza.com.br:2083
- **Diretório Raiz**: /home/betinalimpeza/public_html/

---

## ✅ Checklist Inicial

- [ ] MCP Server testado
- [ ] Auditoria executada
- [ ] Arquivos suspeitos analisados
- [ ] Backup criado antes de edições
- [ ] SSH/FTP configurado para automação

