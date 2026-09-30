<?php
/**
 * Arquivo: index.php (HTML/catalogo/index.php) — VERSÃO CORRIGIDA
 * Descrição: Servidor dinâmico de catálogos por vendedora
 *
 * Conceito: Um único catálogo (me.html) é servido para todas as vendedoras.
 * O número de WhatsApp para "encaminhar pedido" é dinâmico por vendedora.
 *
 * URLs suportadas:
 *   - /catalogo/                 → catálogo da empresa (whatsapp da Adriana)
 *   - /catalogo/adriana          → catálogo da empresa (whatsapp da Adriana)
 *   - /catalogo/simone           → catálogo da empresa (whatsapp da Simone)
 *   - /catalogo/{vendedora}      → catálogo da empresa (whatsapp da {vendedora})
 *
 * Última atualização: 2026-09-30 (CORREÇÃO)
 */

// ===== CONFIGURAÇÃO =====
define('CATALOG_FILE', __DIR__ . '/me.html');
define('SITE_URL', 'https://betinalimpeza.com.br');
define('DEFAULT_VENDOR', 'adriana');

// ===== CARREGAR DADOS DE VENDEDORAS =====
require_once __DIR__ . '/vendedoras.php';

// ===== EXTRAIR PARÂMETRO DE VENDEDORA =====
$vendedora_slug = isset($_GET['vendedora']) ? sanitize_slug($_GET['vendedora']) : DEFAULT_VENDOR;

// ===== FUNÇÕES AUXILIARES =====

/**
 * Sanitiza slug (apenas letras, números, hífen, underscore)
 */
function sanitize_slug($slug) {
    $slug = strtolower(trim($slug));
    $slug = preg_replace('/[^a-z0-9\-_]/', '', $slug);
    return $slug;
}

/**
 * Busca dados da vendedora, retorna null se não encontrada
 */
function get_vendor_phone($slug) {
    $vendor = get_vendedora($slug);
    return $vendor ? $vendor['telefone'] : null;
}

/**
 * Converte telefone para formato WhatsApp (apenas números)
 * Entrada: '5524988541099' ou '55 24 98854-1099'
 * Saída: '5524988541099'
 */
function format_whatsapp_number($phone) {
    return preg_replace('/[^0-9]/', '', $phone);
}

// ===== LÓGICA PRINCIPAL =====

// Validar se vendedora existe
$phone = get_vendor_phone($vendedora_slug);

if (!$phone) {
    // Vendedora não encontrada - redirecionar para default (Adriana)
    if ($vendedora_slug !== DEFAULT_VENDOR) {
        header("HTTP/1.1 302 Found");
        header("Location: " . SITE_URL . "/catalogo/");
        exit;
    }
}

// Se phone ainda é null (vendedora default não encontrada), usar valor fallback
if (!$phone) {
    $phone = '5524988541099'; // Fallback para Adriana
}

$phone_formatted = format_whatsapp_number($phone);

// ===== CARREGAR E MODIFICAR CATÁLOGO =====

if (!file_exists(CATALOG_FILE)) {
    http_response_code(500);
    echo "Erro: Catálogo não encontrado no servidor.";
    exit;
}

$html = file_get_contents(CATALOG_FILE);

// ===== INJETAR NÚMERO DE WHATSAPP DINÂMICO =====
//
// Estratégia: Adicionar script que sobrescreve a função sendWhatsApp()
// para usar o número da vendedora em vez do hardcoded no HTML
//

$injection_script = <<<'ENDSCRIPT'
<script>
// Injeção dinâmica de WhatsApp por vendedora
(function() {
    // Sobrescrever número de WhatsApp
    window.VENDOR_PHONE = 'PHONE_PLACEHOLDER';

    // Armazenar referência original de sendWhatsApp (se existir)
    const originalSendWhatsApp = window.sendWhatsApp;

    // Redefinir sendWhatsApp para usar vendedora
    window.sendWhatsApp = function() {
        if (typeof cart === 'undefined' || cart.length === 0) {
            alert('Carrinho vazio. Adicione produtos primeiro.');
            return;
        }

        let msg = 'Olá! Gostaria de fazer um pedido:\n\n';
        let total = 0;

        cart.forEach(item => {
            msg += `• ${item.nome} (${item.quantidade}x)\n`;
            total += item.quantidade * item.preco;
        });

        msg += `\nTotal: R$ ${total.toFixed(2)}`;

        const phone = window.VENDOR_PHONE || 'PHONE_PLACEHOLDER';
        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
    };
})();
</script>
ENDSCRIPT;

// Substituir placeholder pelo número real
$injection_script = str_replace('PHONE_PLACEHOLDER', $phone_formatted, $injection_script);

// ===== INSERIR SCRIPT NA PÁGINA =====
// Procurar por </body> e inserir o script antes
$html = str_replace('</body>', $injection_script . '</body>', $html);

// ===== ENVIAR PÁGINA =====
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
echo $html;

?>
