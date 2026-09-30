# Cadastro de Vendedoras — Betinalimpeza.com.br

**Última atualização**: 2026-09-30  
**Responsável**: Natalia Espindola

---

## Vendedoras Ativas

| # | Nome | Telefone | Slug URL | Status |
|---|------|----------|----------|--------|
| 1 | Adriana | +55 24 98854-1099 | `/catalogo/adriana` | ✅ Ativa |
| 2 | MariaEduarda | +55 24 98854-1101 | `/catalogo/mariaeduarda` | ✅ Ativa |
| 3 | Simone | +55 24 99229-8532 | `/catalogo/simone` | ✅ Ativa |
| 4 | Silvana | +55 24 98854-1098 | `/catalogo/silvana` | ✅ Ativa |

---

## Notas

- **Telefone**: Formato completo com código país (+55) e DDD
- **Slug**: Nome em minúsculas, sem espaços, usado na URL
- Cada vendedora terá seu catálogo em `/catalogo/{slug}`
- Links de WhatsApp serão construídos automaticamente como: `https://wa.me/55{numero_sem_caracteres_especiais}`

---

## Como Adicionar Nova Vendedora

1. Adicione uma linha na tabela acima
2. Atualize o arquivo `vendedoras.php` no servidor (em `/HTML/catalogo/`)
3. Teste a URL: `https://betinalimpeza.com.br/catalogo/{slug}`