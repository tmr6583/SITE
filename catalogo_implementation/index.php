<?php
/**
 * index.php - Catálogo dinâmico (V10: Corrigir template JavaScript)
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

// ========== CORREÇÃO V10 ==========
// Corrigir os TEMPLATES JavaScript que renderizam as imagens
// ANTES: <img src="${p.imagem}" ...>
// DEPOIS: <img src="${p.imagem.startsWith('http') ? p.imagem : CATALOG_URL_VAR + '/' + p.imagem}" ...>

// Precisamos injetar uma função helper que será usada no template
// E depois modificar o template para usá-la

// Estratégia: Injetar script ANTES de </head> que define CATALOG_URL
// E depois modificar o template inline

// 1. Substituir o script de produtos
$html = str_replace(
    '<script src="produtos.js"></script>',
    '<script>window.CATALOG_URL="' . CATALOG_URL . '";</script><script src="' . CATALOG_URL . '/produtos.js"></script>',
    $html
);

// 2. MAIS IMPORTANTE: Modificar o template de renderização da imagem
// Trocar: src="${p.imagem}"
// Por: src="${p.imagem.startsWith('http')?p.imagem:window.CATALOG_URL+'/'+p.imagem}"

$old_img_template = 'src="${p.imagem}"';
$new_img_template = 'src="${(function(img){return img.startsWith(\'http\')?img:window.CATALOG_URL+\'/\'+img})(p.imagem)}"';
$html = str_replace($old_img_template, $new_img_template, $html);

// 3. Também corrigir o atributo alt que pode ter a mesma estrutura
// (procurar por outras ocorrências de ${p.imagem})
$html = str_replace(
    '${p.imagem}',
    '${(function(img){return img.startsWith(\'http\')?img:window.CATALOG_URL+\'/\'+img})(p.imagem)}',
    $html
);

// 4. Substituir o placeholder externo
$svg_placeholder = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5TZW0gRm90bzwvdGV4dD48L3N2Zz4=';
$old_onerror = "onerror=\"this.src='https://via.placeholder.com/200?text=Sem+Foto'\"";
$new_onerror = "onerror=\"this.src='" . $svg_placeholder . "'\"";
$html = str_replace($old_onerror, $new_onerror, $html);

// 5. Script de injeção de WhatsApp
$script = "\n" .
"<script>" . "\n" .
"window.VENDOR_PHONE = '" . $phone_formatted . "';" . "\n" .
"window.CATALOG_URL = '" . CATALOG_URL . "';" . "\n" .
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
