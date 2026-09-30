# Implementação: Catálogos Dinâmicos para Vendedoras

**Versão**: 1.0  
**Data**: 2026-09-30  
**Status**: ✅ Pronto para Deployment

---

## 📋 O Que É

Sistema de catálogos dinâmicos que permite cada vendedora ter uma URL elegante e exclusiva com links automáticos de WhatsApp.

**URLs geradas**:
```
https://betinalimpeza.com.br/catalogo/              (lista todas)
https://betinalimpeza.com.br/catalogo/adriana       (catálogo de Adriana)
https://betinalimpeza.com.br/catalogo/simone        (catálogo de Simone)
https://betinalimpeza.com.br/catalogo/silvana       (catálogo de Silvana)
https://betinalimpeza.com.br/catalogo/mariaeduarda  (catálogo de MariaEduarda)
```

---

## 🎯 Benefícios

- ✅ **URLs limpas e elegantes** — `/catalogo/{nome}` em vez de `/HTML/catalogo/{nome}.html`
- ✅ **Zero duplicação** — Um único `index.php` renderiza todos os catálogos
- ✅ **WhatsApp automático** — Links dinâmicos sem editar cada página
- ✅ **Fácil manutenção** — Adicionar vendedora é editar um arquivo PHP
- ✅ **Rollback seguro** — Possibilidade de reverter em segundos

---

## 📁 Arquivos

| Arquivo | Propósito | Localização |
|---------|-----------|-------------|
| `index.php` | Router dinâmico principal | `/HTML/catalogo/index.php` |
| `vendedoras.php` | Base de dados de vendedoras | `/HTML/catalogo/vendedoras.php` |
| `deploy.py` | Script automático de deployment | (local, executa no seu PC) |
| `DEPLOYMENT_PLAN.md` | Plano detalhado | (referência) |
| `test_catalogo.php` | Testes de validação | (local, referência) |

---

## 🚀 Como Fazer Deployment

### Pré-requisitos

1. ✅ **SSH ativado no painel Locaweb**
   - Acesse: https://painelhospedagem.locaweb.com.br/dashboard/8291801
   - Menu: **Hospedagem** → **Ambientes** → **Acesso**
   - Ativar: **SSH** (checkbox)
   - Aguarde ~1 minuto

2. ✅ **Arquivo `.env` configurado** (você já tem)
   - Contém credenciais Locaweb
   - Não precisa fazer nada adicional

### Executar Deployment

**Opção A: Automático (Recomendado)**

```bash
cd C:\GitHubLocal\SITE\catalogo_implementation
python3 deploy.py
```

Você será solicitado a confirmar. Digite `s` para continuar.

**Opção B: Manual**

Se preferir fazer manualmente (não recomendado):

```bash
# Não disponível no Windows sem ferramentas adicionais
# Use a Opção A (automática)
```

---

## 🧪 Testes Pós-Deployment

Após o deployment, testar no navegador:

### Teste 1: Lista de Vendedoras
```
https://betinalimpeza.com.br/catalogo/
```
✅ Deve mostrar grid com 4 vendedoras: Adriana, MariaEduarda, Simone, Silvana

### Teste 2: Catálogo Individual
```
https://betinalimpeza.com.br/catalogo/adriana
```
✅ Deve mostrar:
- Nome: Adriana
- Telefone: +55 24 98854-1099
- Botão: "Conversar no WhatsApp"

### Teste 3: Link WhatsApp
Clicar no botão "Conversar no WhatsApp"
✅ Deve abrir: `https://wa.me/5524988541099`

### Teste 4: Página Inexistente (404)
```
https://betinalimpeza.com.br/catalogo/vendedora_falsa
```
✅ Deve mostrar página 404 elegante

### Teste 5: Todas as Vendedoras
Testar cada uma:
- `/catalogo/adriana` → WhatsApp: +55 24 98854-1099
- `/catalogo/mariaeduarda` → WhatsApp: +55 24 98854-1099
- `/catalogo/simone` → WhatsApp: +55 24 99229-8532
- `/catalogo/silvana` → WhatsApp: +55 24 98854-1098

---

## ➕ Adicionar Nova Vendedora

Para adicionar nova vendedora, editar `/HTML/catalogo/vendedoras.php`:

```php
$vendedoras = [
    // ... vendedoras existentes ...
    
    'novavendedora' => [
        'nome' => 'Nova Vendedora',
        'telefone' => '5524988886666',
        'descricao' => 'Vendedora Nova Vendedora',
    ],
];
```

Depois fazer upload do arquivo atualizado. Pronto! A URL `/catalogo/novavendedora` estará disponível automaticamente.

---

## ↩️ Rollback (Se Necessário)

Se algo der errado, reverter é simples:

### Opção 1: Remover Catálogo Dinâmico
```bash
# Via SSH/FTP, deletar:
/HTML/catalogo/index.php
/HTML/catalogo/vendedoras.php
```

Após isso, as URLs `/catalogo/*` não funcionarão, mas os arquivos estáticos antigos (e.g., `/HTML/catalogo/adriana.html`) continuarão acessíveis.

### Opção 2: Restaurar .htaccess
Se as rewrite rules forem problema, remover as linhas adicionadas:

```apache
#### START Catálogos Dinâmicos
...
#### END Catálogos Dinâmicos
```

---

## 🔍 Verificação de Deployment

Para verificar se está tudo OK após upload:

```bash
# Via SSH (quando ativado)
ssh betinalimpeza

# Verificar arquivos
ls -la /home/storage2/b/f0/5b/betinalimpeza/public_html/HTML/catalogo/

# Deve listar:
# -rw-r--r-- index.php
# -rw-r--r-- vendedoras.php
# ...outros arquivos
```

---

## 📊 Estrutura de Dados

Cada vendedora é um dicionário PHP:

```php
'slug' => [
    'nome' => 'Nome Completo',          // Exibido no catálogo
    'telefone' => '5524988541099',      // Para WhatsApp
    'descricao' => 'Breve descrição',   // Opcional, na lista
]
```

**Regras**:
- `slug`: Sempre minúsculas, sem espaços (e.g., `mariaeduarda`)
- `telefone`: Formato internacional sem formatação (e.g., `5524988541099`)
- `nome`: Pode ter espaços e maiúsculas

---

## 🛡️ Segurança

- ✅ **Sem SQL Injection** — Não usa banco de dados
- ✅ **Sem XSS** — HTML escapado com `htmlspecialchars()`
- ✅ **Sem File Inclusion** — Slugs validados e sanitizados
- ✅ **Permissões** — Arquivos com permissão `644` (leitura pública, sem execução)

---

## 📝 Arquivos do Deployment

### Arquivo: `index.php` (~12 KB)
- Router dinâmico que captura slug da URL
- Renderiza templates HTML
- Gera links WhatsApp automaticamente
- Suporta lista de todas as vendedoras

### Arquivo: `vendedoras.php` (~2 KB)
- Base de dados com array PHP
- Função `get_vendedora($slug)` para buscar dados
- Função `whatsapp_link($telefone)` para gerar URLs WhatsApp

### Modificação: `.htaccess`
- Adiciona 2 rewrite rules
- Mapeia `/catalogo/{slug}` → `/HTML/catalogo/index.php?vendedora={slug}`

---

## 📞 Troubleshooting

### Problema: URLs retornam 404

**Solução**:
1. Verificar se `.htaccess` foi atualizado
2. Verificar se `index.php` existe em `/HTML/catalogo/`
3. Testar diretamente: `https://betinalimpeza.com.br/HTML/catalogo/index.php?vendedora=adriana`

### Problema: WhatsApp link não abre

**Solução**:
1. Verificar número de telefone no `vendedoras.php`
2. Testar link manualmente: `https://wa.me/5524988541099`

### Problema: Estilos CSS não funcionando

**Solução**:
1. CSS está inline em `index.php`, não há dependências externas
2. Se o problema persistir, pode ser cache do navegador — limpar cache

---

## 🎓 Como Funciona Internamente

1. **URL chega**: `/catalogo/adriana`
2. **Apache reescreve**: Para `/HTML/catalogo/index.php?vendedora=adriana`
3. **PHP carrega**: `vendedoras.php` com dados
4. **PHP busca**: `get_vendedora('adriana')`
5. **PHP renderiza**: Template HTML com dados dela
6. **Browser recebe**: HTML com links de WhatsApp

Tudo acontece em ~50ms por request, sem banco de dados ou dependências.

---

## 📈 Próximos Passos (Futuro)

- [ ] Integrar catálogo de produtos
- [ ] Imagens por vendedora
- [ ] Sistema de avaliações
- [ ] Analytics/tracking de cliques
- [ ] Dashboard para vendedoras gerenciarem suas páginas

---

## 📄 Documentação Relacionada

- [DEPLOYMENT_PLAN.md](DEPLOYMENT_PLAN.md) — Plano detalhado de deployment
- [../Contexto.md](../Contexto.md) — Contexto completo do projeto
- [../Vendedoras.md](../Vendedoras.md) — Cadastro de vendedoras

---

## ✅ Checklist Final

Antes de fazer deployment, confirme:

- [ ] SSH ativado no painel Locaweb
- [ ] Arquivo `.env` com credenciais corretas
- [ ] Python 3.7+ instalado no PC
- [ ] Testes locais passaram (veja acima)
- [ ] Backup automático será feito
- [ ] Tem plano de rollback (simples: deletar 2 arquivos)

---

**Pronto?** Execute: `python3 deploy.py`

---

*Última atualização: 2026-09-30*
