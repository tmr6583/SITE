# Plano de Deployment — Catálogos Dinâmicos

**Data**: 2026-09-30  
**Versão**: 1.0  
**Status**: Pronto para Upload  

---

## 📋 Resumo

Implementação de catálogos dinâmicos elegantes para vendedoras com URLs clean:
- URLs: `https://betinalimpeza.com.br/catalogo/{nome_vendedora}`
- WhatsApp links: Dinâmicos e automáticos
- Reutilização de código: Zero duplicação

---

## ✅ Validações Completas

- [x] Lógica PHP testada e validada
- [x] URLs geradas corretamente
- [x] Links WhatsApp testados
- [x] Telefones em formato válido
- [x] Sem dependências externas

---

## 🚀 Arquivos a Fazer Upload

| Arquivo | Destino | Descrição | Tamanho |
|---------|---------|-----------|--------|
| `index.php` | `/HTML/catalogo/index.php` | Router dinâmico principal | ~12 KB |
| `vendedoras.php` | `/HTML/catalogo/vendedoras.php` | Base de dados de vendedoras | ~2 KB |

**Total**: ~14 KB

---

## 🔧 Modificação Necessária

**Arquivo**: `/public_html/.htaccess`  
**Local**: Entre "#### END WordPress" e final do arquivo  
**Linhas a adicionar**:

```apache
#### START Catálogos Dinâmicos

RewriteRule ^catalogo/(.+?)/?$ /HTML/catalogo/index.php?vendedora=$1 [QSA,L]
RewriteRule ^catalogo/?$ /HTML/catalogo/index.php [QSA,L]

#### END Catálogos Dinâmicos
```

**Backup Automático**: O arquivo será backupado ANTES de qualquer modificação.

---

## 📦 Estrutura Final

```
/HTML/catalogo/
├── index.php              ← NOVO (router dinâmico)
├── vendedoras.php         ← NOVO (dados de vendedoras)
├── adriana.html           ← EXISTENTE (será preservado)
└── outros arquivos...     ← EXISTENTES (preservados)
```

---

## 🔄 Processo de Deployment

### Fase 1: Backup
1. Fazer backup do `.htaccess` atual
2. Fazer backup da estrutura `/HTML/catalogo/`
3. Armazenar em repositório Git com commit

### Fase 2: Upload
1. Fazer upload de `index.php` para `/HTML/catalogo/`
2. Fazer upload de `vendedoras.php` para `/HTML/catalogo/`
3. Verificar permissões (644 para PHP)

### Fase 3: Rewrite Rules
1. Adicionar regras ao `.htaccess`
2. Testar e validar

### Fase 4: Testes
1. Testar URL lista: `/catalogo/` → deve listar todas
2. Testar URL individual: `/catalogo/adriana` → página de Adriana
3. Testar 404: `/catalogo/inexistente` → erro 404
4. Testar WhatsApp: Clicar no botão → deve abrir WhatsApp

---

## ↩️ Rollback (Se Necessário)

Se algo der errado, reverter é simples:

```bash
# Opção 1: Deletar arquivos novos
rm /HTML/catalogo/index.php
rm /HTML/catalogo/vendedoras.php

# Opção 2: Restaurar .htaccess (se modificado)
# Restaurar do backup

# Opção 3: Completo
git checkout HEAD~1 .htaccess  # desfazer mudanças
```

As URLs antigas (e.g., `/HTML/catalogo/adriana.html`) continuarão funcionando.

---

## 🧪 Testes Pós-Deployment

Executar após upload:

```bash
# 1. Teste de lista
curl https://betinalimpeza.com.br/catalogo/

# 2. Teste de página individual
curl https://betinalimpeza.com.br/catalogo/adriana

# 3. Teste de 404
curl https://betinalimpeza.com.br/catalogo/inexistente

# 4. Validar links no HTML
# Abrir no navegador e clicar em "Conversar no WhatsApp"
```

---

## 📝 Dados de Vendedoras

Atualmente cadastradas:

```php
[
    'adriana' => '+55 24 98854-1099',
    'mariaeduarda' => '+55 24 98854-1099',
    'simone' => '+55 24 99229-8532',
    'silvana' => '+55 24 98854-1098',
]
```

Para adicionar nova vendedora:
1. Editar `vendedoras.php`
2. Adicionar entrada no array `$vendedoras`
3. Fazer upload
4. URL será automaticamente disponível em `/catalogo/{slug}`

---

## 🎯 Próximos Passos (Fase 2)

- [ ] Integrar catálogo de produtos (dinâmico ou estático)
- [ ] Adicionar imagens por vendedora
- [ ] Sistema de avaliações (opcional)
- [ ] Analytics/tracking de cliques

---

## ⚠️ Notas Importantes

1. **Permissões**: Arquivos `.php` devem ter permissão `644`
2. **Encoding**: Arquivos em UTF-8 sem BOM
3. **LineEndings**: CRLF (Windows) será convertido para LF no servidor
4. **Compatibilidade**: Funciona com PHP 7.0+

---

## 📞 Suporte

Em caso de problemas:
1. Consultar logs: `/var/log/apache2/error.log` (via SSH)
2. Verificar permissões: `ls -la /HTML/catalogo/`
3. Testar rewrite rules: Verificar `.htaccess` está correto
4. Rollback: Deletar novos arquivos e restaurar `.htaccess`
