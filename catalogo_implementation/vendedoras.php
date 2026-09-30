<?php
/**
 * Arquivo: vendedoras.php
 * Descrição: Base de dados de vendedoras para catálogos dinâmicos
 *
 * Formato:
 *   'slug' => [
 *       'nome' => 'Nome da Vendedora',
 *       'telefone' => '+55 XX 9XXXX-XXXX',
 *       'descricao' => 'Breve descrição (opcional)',
 *   ]
 *
 * Última atualização: 2026-09-30
 */

$vendedoras = [
    'adriana' => [
        'nome' => 'Adriana',
        'telefone' => '5524988541099',
        'descricao' => 'Vendedora Adriana',
    ],
    'mariaeduarda' => [
        'nome' => 'MariaEduarda',
        'telefone' => '5524988541099',
        'descricao' => 'Vendedora MariaEduarda',
    ],
    'simone' => [
        'nome' => 'Simone',
        'telefone' => '5524992298532',
        'descricao' => 'Vendedora Simone',
    ],
    'silvana' => [
        'nome' => 'Silvana',
        'telefone' => '5524988541098',
        'descricao' => 'Vendedora Silvana',
    ],
];

/**
 * Retorna os dados de uma vendedora ou null se não encontrada
 */
function get_vendedora($slug) {
    global $vendedoras;
    return isset($vendedoras[$slug]) ? $vendedoras[$slug] : null;
}

/**
 * Retorna lista de todos os slugs de vendedoras
 */
function get_vendedoras_list() {
    global $vendedoras;
    return array_keys($vendedoras);
}

/**
 * Gera URL de WhatsApp a partir do telefone
 * Entrada esperada: 5524988541099 (sem espaços ou caracteres especiais)
 */
function whatsapp_link($telefone) {
    // Remove caracteres não-numéricos
    $numeros = preg_replace('/[^0-9]/', '', $telefone);
    return 'https://wa.me/' . $numeros;
}

?>
