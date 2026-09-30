<?php
/**
 * index.php - Catálogo dinâmico por vendedora (V8: Corrigir paths de imagens antes da renderização)
 */

define('CATALOG_FILE', __DIR__ . '/me.html');
define('DEFAULT_VENDOR', 'adriana');
define('CATALOG_URL', 'https://betinalimpeza.com.br/HTML/catalogo');

require_once __DIR__ . '/vendedoras.php';

function sanitize_slug($slug) {
    $slug = strtolower(trim($slug));
    return preg_replace('/[^a-z0-9\-_]/', '', $slug);
}

function format_whatsapp_number($phone) {
    return preg_replace('/[^0-9]/', '', $phone);
}

$vendedora_slug = isset($_GET['vendedora']) ? sanitize_slug($_GET['vendedora']) : DEFAULT_VENDOR;

$vendor_data = get_vendedora($vendedora_slug);
if (!$vendor_data) {
    $vendor_data = get_vendedora(DEFAULT_VENDOR);
}

$phone = $vendor_data['telefone'];
$phone_formatted = format_whatsapp_number($phone);

if (!file_exists(CATALOG_FILE)) {
    http_response_code(500);
    echo "Erro: Catálogo não encontrado.";
    exit;
}

$html = file_get_contents(CATALOG_FILE);

// Substituir paths de recursos
$html = str_replace('<script src="produtos.js"></script>',
    '<script src="' . CATALOG_URL . '/produtos.js"></script>', $html);
$html = str_replace('src="imagens/', 'src="' . CATALOG_URL . '/imagens/', $html);
$html = str_replace('href="imagens/', 'href="' . CATALOG_URL . '/imagens/', $html);
$html = str_replace("url('imagens/", "url('" . CATALOG_URL . "/imagens/", $html);
$html = str_replace('url("imagens/', 'url("' . CATALOG_URL . '/imagens/', $html);

// Script que corrige paths de imagens ANTES e DEPOIS da renderização
$script = "\n" .
"<script>" . "\n" .
"window.VENDOR_PHONE = '" . $phone_formatted . "';" . "\n" .
"window.CATALOG_URL = '" . CATALOG_URL . "';" . "\n" .
"" . "\n" .
"// Função para corrigir paths de imagens em allProducts" . "\n" .
"function fixImagePaths() {" . "\n" .
"  if (typeof allProducts !== 'undefined' && Array.isArray(allProducts)) {" . "\n" .
"    allProducts.forEach(function(p) {" . "\n" .
"      if (p.imagem && p.imagem.indexOf('http') !== 0) {" . "\n" .
"        if (p.imagem.indexOf('imagens/') === 0) {" . "\n" .
"          p.imagem = window.CATALOG_URL + '/' + p.imagem;" . "\n" .
"        }" . "\n" .
"      }" . "\n" .
"    });" . "\n" .
"  }" . "\n" .
"}" . "\n" .
"" . "\n" .
"// Executar assim que allProducts estiver disponível" . "\n" .
"var checkInterval = setInterval(function() {" . "\n" .
"  if (typeof allProducts !== 'undefined') {" . "\n" .
"    fixImagePaths();" . "\n" .
"    clearInterval(checkInterval);" . "\n" .
"  }" . "\n" .
"}, 100);" . "\n" .
"" . "\n" .
"// Também corrigir no onload" . "\n" .
"var originalOnload = window.onload;" . "\n" .
"window.onload = function() {" . "\n" .
"  if (originalOnload) {" . "\n" .
"    originalOnload.call(this);" . "\n" .
"  }" . "\n" .
"  fixImagePaths();" . "\n" .
"  " . "\n" .
"  // Corrigir também todas as imagens renderizadas no DOM" . "\n" .
"  setTimeout(function() {" . "\n" .
"    var imgs = document.querySelectorAll('img[src^=\"imagens/\"]');" . "\n" .
"    imgs.forEach(function(img) {" . "\n" .
"      var src = img.getAttribute('src');" . "\n" .
"      if (src.indexOf('http') !== 0) {" . "\n" .
"        img.setAttribute('src', window.CATALOG_URL + '/' + src);" . "\n" .
"      }" . "\n" .
"    });" . "\n" .
"  }, 500);" . "\n" .
"};" . "\n" .
"" . "\n" .
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

$html = str_replace('</body>', $script . '</body>', $html);

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
echo $html;
?>
