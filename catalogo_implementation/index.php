<?php
/**
 * Arquivo: index.php (HTML/catalogo/index.php)
 * Descrição: Router dinâmico para catálogos de vendedoras
 *
 * URLs suportadas:
 *   - /catalogo/adriana          → exibe catálogo da Adriana
 *   - /catalogo/simone           → exibe catálogo da Simone
 *   - /catalogo/                 → lista todas as vendedoras
 *   - /catalogo                  → redireciona para /catalogo/
 *
 * O servidor deve configurar .htaccess para:
 *   RewriteRule ^catalogo/(.*)$ /HTML/catalogo/index.php?vendedora=$1 [QSA,L]
 *
 * Última atualização: 2026-09-30
 */

// ===== CONFIGURAÇÃO =====
define('PAGE_TITLE', 'Catálogo de Vendedoras - Betinalimpeza');
define('SITE_NAME', 'Betinalimpeza');
define('SITE_URL', 'https://betinalimpeza.com.br');

// ===== CARREGAR DADOS =====
require_once __DIR__ . '/vendedoras.php';

// ===== EXTRAIR PARÂMETRO =====
// A URL pode vir de duas formas:
// 1. Via rewrite: /catalogo/adriana → /HTML/catalogo/index.php?vendedora=adriana
// 2. Via query string direto: index.php?vendedora=adriana

$vendedora_slug = isset($_GET['vendedora']) ? sanitize_slug($_GET['vendedora']) : '';

// ===== FUNÇÕES AUXILIARES =====

/**
 * Sanitiza slug (apenas letras, números, hífen)
 */
function sanitize_slug($slug) {
    $slug = strtolower(trim($slug));
    $slug = preg_replace('/[^a-z0-9\-_]/', '', $slug);
    return $slug;
}

/**
 * Escape HTML
 */
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// ===== LÓGICA PRINCIPAL =====

if (empty($vendedora_slug)) {
    // Sem parâmetro: mostrar lista de todas as vendedoras
    render_lista_vendedoras();
} else {
    // Com parâmetro: mostrar catálogo da vendedora
    $vendedora = get_vendedora($vendedora_slug);

    if ($vendedora) {
        render_catalogo($vendedora_slug, $vendedora);
    } else {
        render_erro_404($vendedora_slug);
    }
}

// ===== TEMPLATES =====

/**
 * Template: Lista de todas as vendedoras
 */
function render_lista_vendedoras() {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo h(PAGE_TITLE); ?></title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 40px 20px;
            }
            .container {
                max-width: 900px;
                margin: 0 auto;
            }
            header {
                text-align: center;
                color: white;
                margin-bottom: 50px;
            }
            header h1 {
                font-size: 2.5em;
                margin-bottom: 10px;
            }
            header p {
                font-size: 1.1em;
                opacity: 0.9;
            }
            .vendedoras-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 25px;
            }
            .vendedora-card {
                background: white;
                border-radius: 12px;
                padding: 30px;
                text-align: center;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                text-decoration: none;
                color: inherit;
                display: block;
            }
            .vendedora-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            }
            .vendedora-card h3 {
                font-size: 1.5em;
                color: #333;
                margin-bottom: 10px;
            }
            .vendedora-card p {
                color: #666;
                margin-bottom: 15px;
                font-size: 0.9em;
            }
            .btn-view {
                display: inline-block;
                background: #667eea;
                color: white;
                padding: 12px 24px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: 600;
                transition: background 0.3s ease;
            }
            .btn-view:hover {
                background: #764ba2;
            }
            footer {
                text-align: center;
                color: white;
                margin-top: 50px;
                font-size: 0.9em;
                opacity: 0.8;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <header>
                <h1>🏪 Catálogo de Vendedoras</h1>
                <p><?php echo h(SITE_NAME); ?></p>
            </header>

            <div class="vendedoras-grid">
                <?php
                $vendedoras_list = get_vendedoras_list();
                foreach ($vendedoras_list as $slug) {
                    $vendedora = get_vendedora($slug);
                    $url = SITE_URL . '/catalogo/' . urlencode($slug);
                    ?>
                    <a href="<?php echo h($url); ?>" class="vendedora-card">
                        <h3><?php echo h($vendedora['nome']); ?></h3>
                        <p><?php echo h($vendedora['descricao']); ?></p>
                        <span class="btn-view">Ver Catálogo →</span>
                    </a>
                    <?php
                }
                ?>
            </div>

            <footer>
                <p>Catálogos dinâmicos - Última atualização: 2026-09-30</p>
            </footer>
        </div>
    </body>
    </html>
    <?php
}

/**
 * Template: Catálogo de uma vendedora
 */
function render_catalogo($slug, $vendedora) {
    $whatsapp_url = whatsapp_link($vendedora['telefone']);
    $backlink = SITE_URL . '/catalogo/';
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo h($vendedora['nome']); ?> - Catálogo | <?php echo h(SITE_NAME); ?></title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background: #f8f9fa;
                color: #333;
            }
            header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 40px 20px;
                text-align: center;
            }
            header h1 {
                font-size: 2.5em;
                margin-bottom: 10px;
            }
            header .subtitle {
                font-size: 1.1em;
                opacity: 0.9;
            }
            .container {
                max-width: 900px;
                margin: 0 auto;
                padding: 40px 20px;
            }
            .info-section {
                background: white;
                border-radius: 12px;
                padding: 30px;
                margin-bottom: 30px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .info-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }
            .info-row label {
                font-weight: 600;
                color: #666;
            }
            .info-row value {
                color: #333;
                font-size: 1.1em;
            }
            .cta-buttons {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
                margin-top: 30px;
            }
            .btn {
                flex: 1;
                min-width: 200px;
                padding: 16px 24px;
                border: none;
                border-radius: 8px;
                font-size: 1em;
                font-weight: 600;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                transition: all 0.3s ease;
            }
            .btn-whatsapp {
                background: #25D366;
                color: white;
            }
            .btn-whatsapp:hover {
                background: #20BA5A;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
            }
            .btn-back {
                background: #e9ecef;
                color: #333;
            }
            .btn-back:hover {
                background: #dee2e6;
            }
            .placeholder {
                background: #e9ecef;
                border-radius: 8px;
                padding: 40px;
                text-align: center;
                color: #999;
                margin: 30px 0;
            }
            footer {
                text-align: center;
                padding: 20px;
                color: #999;
                font-size: 0.9em;
            }
        </style>
    </head>
    <body>
        <header>
            <h1>🛍️ <?php echo h($vendedora['nome']); ?></h1>
            <p class="subtitle"><?php echo h(SITE_NAME); ?> - Catálogo</p>
        </header>

        <div class="container">
            <div class="info-section">
                <h2 style="margin-bottom: 20px;">Informações da Vendedora</h2>

                <div class="info-row">
                    <label>Nome:</label>
                    <value><?php echo h($vendedora['nome']); ?></value>
                </div>

                <div class="info-row">
                    <label>Telefone:</label>
                    <value><?php echo h($vendedora['telefone']); ?></value>
                </div>

                <div class="cta-buttons">
                    <a href="<?php echo h($whatsapp_url); ?>" class="btn btn-whatsapp" target="_blank">
                        💬 Conversar no WhatsApp
                    </a>
                    <a href="<?php echo h($backlink); ?>" class="btn btn-back">
                        ← Voltar
                    </a>
                </div>
            </div>

            <div class="info-section">
                <h2 style="margin-bottom: 20px;">Catálogo de Produtos</h2>
                <div class="placeholder">
                    <p>📦 Catálogo em desenvolvimento</p>
                    <p style="font-size: 0.9em; margin-top: 10px;">Os produtos serão adicionados em breve</p>
                </div>
            </div>
        </div>

        <footer>
            <p>Catálogo de <?php echo h($vendedora['nome']); ?> - Betinalimpeza</p>
            <p>Acesso: /catalogo/<?php echo h($slug); ?></p>
        </footer>
    </body>
    </html>
    <?php
}

/**
 * Template: Erro 404
 */
function render_erro_404($slug_tentado) {
    $backlink = SITE_URL . '/catalogo/';
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - Vendedora Não Encontrada</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .error-container {
                background: white;
                border-radius: 12px;
                padding: 50px;
                text-align: center;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                max-width: 500px;
            }
            .error-container h1 {
                font-size: 3em;
                color: #667eea;
                margin-bottom: 20px;
            }
            .error-container p {
                font-size: 1.1em;
                color: #666;
                margin-bottom: 30px;
            }
            .error-slug {
                background: #f8f9fa;
                border-radius: 6px;
                padding: 15px;
                margin: 20px 0;
                font-family: 'Courier New', monospace;
                color: #333;
            }
            .btn {
                display: inline-block;
                background: #667eea;
                color: white;
                padding: 12px 30px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: 600;
                transition: background 0.3s ease;
            }
            .btn:hover {
                background: #764ba2;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>404</h1>
            <p>Vendedora não encontrada</p>
            <div class="error-slug">
                /catalogo/<?php echo h($slug_tentado); ?>
            </div>
            <p style="font-size: 0.9em; color: #999;">
                A vendedora que você procura não está cadastrada.
            </p>
            <a href="<?php echo h($backlink); ?>" class="btn">Voltar ao Catálogo</a>
        </div>
    </body>
    </html>
    <?php
}

?>
