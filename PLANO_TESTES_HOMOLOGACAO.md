# Plano de Testes e Homologação — Catálogos Dinâmicos

**Data**: 2026-09-30  
**Responsável**: Validação completa antes de produção  
**Status**: 🔴 Aguardando conclusão

---

## 📋 Fase 1: Diagnóstico (ATUAL)

### 1.1 Erro Reportado
```
"Erro: Não foi possível carregar os produtos."
```

**Hipóteses**:
- [ ] `produtos.js` não está sendo encontrado (path relativo quebrado)
- [ ] `allProducts` não está sendo definido
- [ ] Script `produtos.js` não está carregando por causa do rewrite
- [ ] CORS ou block de carregamento de script

### 1.2 Testes de Diagnóstico

**Teste D1**: Verificar console do navegador
```
Ação: Abrir /catalogo/simone
Verificar: DevTools (F12) → Console
Procurar: Mensagens de erro
Esperado: ❌ Erros de script não encontrado ou variável undefined
```

**Teste D2**: Verificar se produtos.js existe
```bash
ssh betinalimpeza "ls -la public_html/HTML/catalogo/produtos.js"
Esperado: -rw-r--r-- ... produtos.js (arquivo existe)
```

**Teste D3**: Testar acesso direto a produtos.js
```bash
curl -I https://betinalimpeza.com.br/HTML/catalogo/produtos.js
Esperado: HTTP 200
```

**Teste D4**: Verificar fonte do HTML
```
Ação: Abrir /catalogo/simone
Verificar: View Source (Ctrl+U)
Procurar: Tag <script src="produtos.js">
Verificar: Path relativo está correto?
```

**Teste D5**: Verificar variável allProducts
```
Ação: Console DevTools → F12
Digitar: allProducts
Esperado: Array com produtos carregados
Erro esperado: undefined ou erro de carregamento
```

---

## 📋 Fase 2: Testes Funcionais (Após diagnóstico)

### 2.1 Testes de Carregamento de Página

| # | Teste | URL | Esperado | Status |
|---|-------|-----|----------|--------|
| F1 | Página carrega | `/catalogo/` | HTTP 200, sem erros | ⬜ |
| F2 | Página carrega | `/catalogo/adriana` | HTTP 200, sem erros | ⬜ |
| F3 | Página carrega | `/catalogo/simone` | HTTP 200, sem erros | ⬜ |
| F4 | Página carrega | `/catalogo/silvana` | HTTP 200, sem erros | ⬜ |
| F5 | Página carrega | `/catalogo/mariaeduarda` | HTTP 200, sem erros | ⬜ |
| F6 | Compatibilidade | `/HTML/catalogo/me.html` | HTTP 200, funciona | ⬜ |

### 2.2 Testes de Produtos

| # | Teste | Ação | Esperado | Status |
|---|-------|------|----------|--------|
| P1 | Listar produtos | Abrir `/catalogo/adriana` | Grid de produtos apareça | ⬜ |
| P2 | Buscar produto | Digitar no search | Produtos filtrados | ⬜ |
| P3 | Adicionar ao carrinho | Clicar "Adicionar" | Produto aparece no carrinho | ⬜ |
| P4 | Aumentar quantidade | Clicar + | Quantidade aumenta | ⬜ |
| P5 | Remover do carrinho | Clicar X | Produto removido | ⬜ |

### 2.3 Testes de WhatsApp Dinâmico

| # | Teste | Vendedora | Esperado | Status |
|---|-------|-----------|----------|--------|
| W1 | WhatsApp Adriana | `/catalogo/` | Botão leva a wa.me/5524988541099 | ⬜ |
| W2 | WhatsApp Adriana | `/catalogo/adriana` | Botão leva a wa.me/5524988541099 | ⬜ |
| W3 | WhatsApp Simone | `/catalogo/simone` | Botão leva a wa.me/5524992298532 | ⬜ |
| W4 | WhatsApp Silvana | `/catalogo/silvana` | Botão leva a wa.me/5524988541098 | ⬜ |
| W5 | WhatsApp MariaEduarda | `/catalogo/mariaeduarda` | Botão leva a wa.me/5524988541099 | ⬜ |

### 2.4 Testes de Compatibilidade

| # | Teste | Esperado | Status |
|---|-------|----------|--------|
| C1 | `/HTML/catalogo/me.html` | Carrega, mostra produtos | ⬜ |
| C2 | `/HTML/catalogo/adriana.html` | Carrega, mostra produtos | ⬜ |
| C3 | `/HTML/catalogo/simone.html` | Carrega, mostra produtos | ⬜ |
| C4 | URLs antigas funcionam | Sem 404 | ⬜ |

### 2.5 Testes de Erro (404)

| # | Teste | URL | Esperado | Status |
|---|-------|-----|----------|--------|
| E1 | Vendedora inexistente | `/catalogo/vendedora_fake` | Redireciona ou mostra erro | ⬜ |
| E2 | URL vazia | `/catalogo/` | Funciona (Adriana default) | ⬜ |

---

## 📋 Fase 3: Testes de Responsividade

### 3.1 Dispositivos

| # | Dispositivo | Teste | Esperado | Status |
|---|-------------|-------|----------|--------|
| R1 | Desktop (1920x1080) | Layout responsivo | Grid produtos correto | ⬜ |
| R2 | Tablet (768x1024) | Layout responsivo | Grid adaptado | ⬜ |
| R3 | Mobile (375x667) | Layout responsivo | Coluna única | ⬜ |
| R4 | iPhone 12 | Botão WhatsApp acessível | Funciona, texto legível | ⬜ |
| R5 | Android | Botão WhatsApp acessível | Funciona, texto legível | ⬜ |

---

## 📋 Fase 4: Testes de Performance

| # | Teste | Método | Esperado | Status |
|---|-------|--------|----------|--------|
| PF1 | Tempo de carregamento | DevTools Lighthouse | < 3s | ⬜ |
| PF2 | Tamanho da página | DevTools Network | ~ 34.5 KB | ⬜ |
| PF3 | Requests de recursos | DevTools Network | Todos com HTTP 200 | ⬜ |

---

## 📋 Fase 5: Testes de Segurança

| # | Teste | Verificação | Esperado | Status |
|---|-------|-------------|----------|--------|
| S1 | XSS | Inspecionar código injetado | Sem vulnerabilidades | ⬜ |
| S2 | SQL Injection | Testar query strings | Sem vulnerabilidades | ⬜ |
| S3 | Header Security | curl -I | Headers seguros presentes | ⬜ |

---

## 🎯 Critérios de Sucesso

### ✅ Homologação Aprovada Se:

- [ ] Todos os testes de diagnóstico (D1-D5) completados
- [ ] Todos os testes funcionais (F1-F6) passaram
- [ ] Todos os testes de produtos (P1-P5) passaram
- [ ] Todos os testes de WhatsApp (W1-W5) passaram
- [ ] Todos os testes de compatibilidade (C1-C4) passaram
- [ ] Todos os testes de erro (E1-E2) passaram
- [ ] Responsividade OK em pelo menos 3 dispositivos
- [ ] Performance aceitável (< 3s load)

### ❌ Homologação Rejeitada Se:

- [ ] Produtos não carregam em qualquer URL
- [ ] WhatsApp não redireciona corretamente
- [ ] Layout quebrado em mobile
- [ ] URLs antigas retornam 404

---

## 🔧 Checklist de Ações

### Antes de Iniciar Testes:
- [ ] Fazer backup de todos os arquivos
- [ ] Registrar versões atuais
- [ ] Preparar ambiente de teste

### Durante Testes:
- [ ] Documentar cada resultado
- [ ] Screenshot de falhas
- [ ] Coletar URLs de erro no console

### Após Testes:
- [ ] Compilar relatório
- [ ] Identificar causas raiz
- [ ] Planejar correções
- [ ] Testar correções

---

## 📝 Template de Resultado

```markdown
### Teste: [Nome do teste]
- **URL**: [URL testada]
- **Navegador**: [Chrome/Firefox/Safari]
- **Dispositivo**: [Desktop/Mobile]
- **Resultado**: ✅ PASSOU / ❌ FALHOU
- **Observações**: [Detalhes]
- **Screenshot**: [Se aplicável]
```

---

## 🎯 Próximas Ações

1. **Executar Fase 1** (Diagnóstico)
   - Verificar console do navegador
   - Testar acesso a produtos.js
   - Inspecionar HTML
   
2. **Baseado em Fase 1**:
   - Se produtos.js não carrega → Corrigir paths
   - Se allProducts undefined → Verificar script
   - Se outros erros → Documentar
   
3. **Executar Fase 2-5** (Após correções)

4. **Gerar Relatório Final**

---

**Status Atual**: 🔴 Aguardando execução de testes  
**Próximo Passo**: Fase 1 - Diagnóstico
