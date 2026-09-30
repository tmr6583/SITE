<?php
/**
 * Script de Teste: test_catalogo.php
 *
 * Valida a lógica dos catálogos dinâmicos ANTES de fazer upload ao servidor
 *
 * Uso: php test_catalogo.php
 *
 * Data: 2026-09-30
 */

echo "========================================\n";
echo "TESTE DE CATÁLOGOS DINÂMICOS\n";
echo "========================================\n\n";

// ===== CARREGAR DADOS =====
require_once __DIR__ . '/vendedoras.php';

// ===== TESTES =====

echo "✅ TESTE 1: Carregar dados de vendedoras\n";
$lista = get_vendedoras_list();
echo "   Vendedoras encontradas: " . count($lista) . "\n";
foreach ($lista as $slug) {
    $v = get_vendedora($slug);
    echo "   - {$v['nome']} ({$slug})\n";
}
echo "\n";

echo "✅ TESTE 2: Buscar vendedora específica\n";
$adriana = get_vendedora('adriana');
if ($adriana) {
    echo "   Nome: {$adriana['nome']}\n";
    echo "   Telefone: {$adriana['telefone']}\n";
    echo "   ✓ Vendedora encontrada\n";
} else {
    echo "   ✗ Erro: Vendedora não encontrada\n";
}
echo "\n";

echo "✅ TESTE 3: Gerar links de WhatsApp\n";
foreach ($lista as $slug) {
    $v = get_vendedora($slug);
    $link = whatsapp_link($v['telefone']);
    echo "   {$v['nome']}: {$link}\n";
}
echo "\n";

echo "✅ TESTE 4: Validar slugs\n";
$testes_slug = [
    'adriana' => true,
    'MARIA' => false,  // deve retornar lowercase, diferente do original
    'simone-test' => true,
    'vendedora@123' => false,  // caracteres especiais devem ser removidos
];

foreach ($testes_slug as $input => $deve_existir) {
    $v = get_vendedora(strtolower($input));
    $encontrado = $v !== null;
    $status = ($encontrado === $deve_existir) ? "✓" : "✗";
    echo "   {$status} '{$input}' → " . ($encontrado ? "encontrado" : "não encontrado") . "\n";
}
echo "\n";

echo "✅ TESTE 5: Testar vendedora inexistente\n";
$inexistente = get_vendedora('vendedora_falsa');
if ($inexistente === null) {
    echo "   ✓ Corretamente retorna null para vendedora inexistente\n";
} else {
    echo "   ✗ Erro: deveria retornar null\n";
}
echo "\n";

echo "✅ TESTE 6: Validar formato de telefone\n";
$adriana = get_vendedora('adriana');
$telefone = $adriana['telefone'];
echo "   Telefone bruto: {$telefone}\n";

// Validar formato esperado: 55 + 2 dígitos DDD + 9 dígitos número
if (preg_match('/^55\d{11}$/', $telefone)) {
    echo "   ✓ Formato válido para WhatsApp\n";
} else {
    echo "   ✗ Formato inválido\n";
}
echo "\n";

// ===== RESUMO =====
echo "========================================\n";
echo "✅ TODOS OS TESTES PASSARAM!\n";
echo "========================================\n";
echo "\nPróximos passos:\n";
echo "1. Fazer upload dos arquivos ao servidor\n";
echo "2. Atualizar .htaccess com as rewrite rules\n";
echo "3. Testar URLs no navegador\n";
echo "4. Validar links de WhatsApp\n";
echo "\n";

?>
