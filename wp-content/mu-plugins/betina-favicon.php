<?php
/**
 * Plugin: Betina Favicon
 * Description: Injetar favicon da empresa no <head> de todas as páginas
 * Version: 1.0
 */

function betina_favicon_wp_head() {
    $favicon_url = home_url('/wp-content/uploads/favicon.jpg');
    echo '<link rel="icon" type="image/jpeg" href="' . esc_attr($favicon_url) . '">';
    echo "\n";
}

add_action('wp_head', 'betina_favicon_wp_head', 1);
?>
