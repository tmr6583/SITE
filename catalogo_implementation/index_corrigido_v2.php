<?php
/**
 * Arquivo: index.php (HTML/catalogo/index.php) — VERSÃO CORRIGIDA V2
 * Descrição: Servidor dinâmico de catálogos por vendedora
 *
 * Conceito: Um único catálogo (me.html) é servido para todas as vendedoras.
 * O número de WhatsApp para "encaminhar pedido" é dinâmico por vendedora.
 */

define('CATALOG_FILE', __DIR__ . '/me.html');
define('SITE_URL', 'https://betinalimpeza.com.br');
define('DEFAULT_VENDOR', 'adriana');

require_once __DIR__ . '/vendedoras.php';

// Extrair parâmetro de vendedora
$vendedora_slug = isset($_GET['vendedora']) ? sanitize_slug($_GET['vendedora']) : DEFAULT_VENDOR;

function sanitize_slug($slug) {
    $slug = strtolower(trim($slug));
    return preg_replace('/[^a-z0-9\-_]/', '', $slug);
}

function format_whatsapp_number($phone) {
    return preg_replace('/[^0-9]/', '', $phone);
}

// Validar vendedora
$vendor = get_vendedora($vendedora_slug);
$phone = $vendor ? $vendor['telefone'] : get_vendedora(DEFAULT_VENDOR)['telefone'];
$phone_formatted = format_whatsapp_number($phone);

// Carregar catálogo
if (!file_exists(CATALOG_FILE)) {
    http_response_code(500);
    echo "Erro: Catálogo não encontrado.";
    exit;
}

$html = file_get_contents(CATALOG_FILE);

// Injetar script de WhatsApp dinâmico
$script = "
<script>
window.VENDOR_PHONE = '" . $phone_formatted . "';
var originalSendWhatsApp = window.sendWhatsApp;
window.sendWhatsApp = function() {
    if (typeof cart === 'undefined' || cart.length === 0) {
        alert('Carrinho vazio. Adicione produtos primeiro.');
        return;
    }
    var msg = 'Olá! Gostaria de fazer um pedido:\\n\\n';
    var total = 0;
    cart.forEach(function(item) {
        msg += '• ' + item.nome + ' (' + item.quantidade + 'x)\\n';
        total += item.quantidade * item.preco;
    });
    msg += '\\nTotal: R\\$ ' + total.toFixed(2);
    var phone = window.VENDOR_PHONE || '" . $phone_formatted . "';
    window.open('https://wa.me/' + phone + '?text=' + encodeURIComponent(msg), '_blank');
};
</script>
";

// Inserir script antes de </body>
$html = str_replace('</body>', $script . '</body>', $html);

// Enviar resposta
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
echo $html;
?>
