<?php
/**
 * index.php - V13: Cache-busting para evitar cache de erros de imagem
 */

define('CATALOG_FILE', __DIR__ . '/me.html');
define('DEFAULT_VENDOR', 'adriana');
define('CATALOG_URL', 'https://betinalimpeza.com.br/HTML/catalogo');
define('FAVICON_URL', 'https://betinalimpeza.com.br/wp-content/uploads/favicon.jpg');
define('CACHE_BUST', '?v=' . date('YmdH')); // Invalida cache a cada hora

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

// Adicionar favicon no <head>
$favicon_tag = '<link rel="icon" type="image/jpeg" href="' . FAVICON_URL . '">';
$html = str_replace('</head>', $favicon_tag . "\n</head>", $html);

// Substituir paths de recursos
$html = str_replace('<script src="produtos.js"></script>',
    '<script src="' . CATALOG_URL . '/produtos.js' . CACHE_BUST . '"></script>', $html);
$html = str_replace('src="imagens/', 'src="' . CATALOG_URL . '/imagens/', $html);
$html = str_replace('href="imagens/', 'href="' . CATALOG_URL . '/imagens/', $html);
$html = str_replace("url('imagens/", "url('" . CATALOG_URL . "/imagens/", $html);
$html = str_replace('url("imagens/', 'url("' . CATALOG_URL . '/imagens/', $html);

// SVG placeholder para "Sem Foto"
$svg_placeholder = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5TZW0gRm90bzwvdGV4dD48L3N2Zz4=';

$old_onerror = "onerror=\"this.src='https://via.placeholder.com/200?text=Sem+Foto'\"";
$new_onerror = "onerror=\"this.src='" . $svg_placeholder . "'\"";
$html = str_replace($old_onerror, $new_onerror, $html);

// Script com URL encoding e cache-busting
$script = "\n" .
"<script>" . "\n" .
"window.VENDOR_PHONE = '" . $phone_formatted . "';" . "\n" .
"window.CATALOG_URL = '" . CATALOG_URL . "';" . "\n" .
"window.CACHE_BUST = '" . CACHE_BUST . "';" . "\n" .
"" . "\n" .
"function encodeImagePath(path) {" . "\n" .
"  if (!path || path.indexOf('http') === 0) return path;" . "\n" .
"  var parts = path.split('/');" . "\n" .
"  var filename = parts[parts.length - 1];" . "\n" .
"  var dir = parts.slice(0, -1).join('/');" . "\n" .
"  var encoded = encodeURIComponent(filename);" . "\n" .
"  return dir ? dir + '/' + encoded : encoded;" . "\n" .
"}" . "\n" .
"" . "\n" .
"function fixImagePaths() {" . "\n" .
"  if (typeof allProducts !== 'undefined' && Array.isArray(allProducts)) {" . "\n" .
"    allProducts.forEach(function(p) {" . "\n" .
"      if (p.imagem && p.imagem.indexOf('http') !== 0) {" . "\n" .
"        if (p.imagem.indexOf('imagens/') === 0) {" . "\n" .
"          var filename = p.imagem.substring('imagens/'.length);" . "\n" .
"          p.imagem = window.CATALOG_URL + '/imagens/' + encodeURIComponent(filename) + window.CACHE_BUST;" . "\n" .
"        }" . "\n" .
"      }" . "\n" .
"    });" . "\n" .
"  }" . "\n" .
"}" . "\n" .
"" . "\n" .
"var checkInterval = setInterval(function() {" . "\n" .
"  if (typeof allProducts !== 'undefined') {" . "\n" .
"    fixImagePaths();" . "\n" .
"    clearInterval(checkInterval);" . "\n" .
"  }" . "\n" .
"}, 100);" . "\n" .
"" . "\n" .
"var originalOnload = window.onload;" . "\n" .
"window.onload = function() {" . "\n" .
"  if (originalOnload) {" . "\n" .
"    originalOnload.call(this);" . "\n" .
"  }" . "\n" .
"  fixImagePaths();" . "\n" .
"  " . "\n" .
"  setTimeout(function() {" . "\n" .
"    var imgs = document.querySelectorAll('img[src^=\"imagens/\"]');" . "\n" .
"    imgs.forEach(function(img) {" . "\n" .
"      var src = img.getAttribute('src');" . "\n" .
"      if (src.indexOf('http') !== 0) {" . "\n" .
"        var filename = src.replace('imagens/', '');" . "\n" .
"        img.setAttribute('src', window.CATALOG_URL + '/imagens/' + encodeURIComponent(filename) + window.CACHE_BUST);" . "\n" .
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
header('Pragma: no-cache');
header('Expires: 0');
echo $html;
?>
