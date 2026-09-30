<?php
/**
 * index.php - Catálogo dinâmico por vendedora (PHP 5.6+ compatível)
 */

define('CATALOG_FILE', __DIR__ . '/me.html');
define('DEFAULT_VENDOR', 'adriana');

require_once __DIR__ . '/vendedoras.php';

// Sanitizar slug
function sanitize_slug($slug) {
    $slug = strtolower(trim($slug));
    $slug = preg_replace('/[^a-z0-9\-_]/', '', $slug);
    return $slug;
}

// Formatar para WhatsApp
function format_whatsapp_number($phone) {
    return preg_replace('/[^0-9]/', '', $phone);
}

// Extrair vendedora da URL
$vendedora_slug = isset($_GET['vendedora']) ? sanitize_slug($_GET['vendedora']) : DEFAULT_VENDOR;

// Buscar dados da vendedora
$vendor_data = get_vendedora($vendedora_slug);
if (!$vendor_data) {
    $vendor_data = get_vendedora(DEFAULT_VENDOR);
}

$phone = $vendor_data['telefone'];
$phone_formatted = format_whatsapp_number($phone);

// Carregar catálogo
if (!file_exists(CATALOG_FILE)) {
    http_response_code(500);
    echo "Erro: Catálogo não encontrado.";
    exit;
}

$html = file_get_contents(CATALOG_FILE);

// Criar script de injeção de WhatsApp
$script = "\n" .
"<script>" . "\n" .
"window.VENDOR_PHONE = '" . $phone_formatted . "';" . "\n" .
"window.sendWhatsApp = function() {" . "\n" .
"  if (typeof cart === 'undefined' || cart.length === 0) {" . "\n" .
"    alert('Carrinho vazio. Adicione produtos primeiro.');" . "\n" .
"    return;" . "\n" .
"  }" . "\n" .
"  var msg = 'Olá! Gostaria de fazer um pedido:\\n\\n';" . "\n" .
"  var total = 0;" . "\n" .
"  for (var i = 0; i < cart.length; i++) {" . "\n" .
"    var item = cart[i];" . "\n" .
"    msg += '• ' + item.nome + ' (' + item.quantidade + 'x)\\n';" . "\n" .
"    total += item.quantidade * item.preco;" . "\n" .
"  }" . "\n" .
"  msg += '\\nTotal: R\\$ ' + total.toFixed(2);" . "\n" .
"  var phone = window.VENDOR_PHONE || '" . $phone_formatted . "';" . "\n" .
"  window.open('https://wa.me/' + phone + '?text=' + encodeURIComponent(msg), '_blank');" . "\n" .
"};" . "\n" .
"</script>" . "\n";

// Inserir script antes de </body>
$html = str_replace('</body>', $script . '</body>', $html);

// Enviar
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
echo $html;
?>
