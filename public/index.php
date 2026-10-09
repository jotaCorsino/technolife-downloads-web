<?php

declare(strict_types=1);

use Technolife\Downloads\DownloadScanner;
use Technolife\Downloads\DownloadScanException;

require_once __DIR__ . '/../src/DownloadScanner.php';

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$directory = getenv('TECHNOLIFE_DOWNLOADS_DIR');
$baseUrl = getenv('TECHNOLIFE_DOWNLOADS_BASE_URL');
$downloads = [];
$loadFailed = true;

if (is_string($directory) && $directory !== '' && is_string($baseUrl) && $baseUrl !== '') {
    try {
        $downloads = DownloadScanner::scan($directory, $baseUrl);
        $loadFailed = false;
    } catch (DownloadScanException $error) {
        // Configuration and filesystem details must never appear in the page.
    }
}

$total = count($downloads);
$initialCount = $total === 1 ? '1 arquivo disponível' : $total . ' arquivos disponíveis';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Central de Links | Technolife</title>
    <link rel="stylesheet" href="styles.css">
    <script src="app.js" defer></script>
</head>
<body>
    <main class="page-shell">
        <header class="page-header">
            <div>
                <p class="brand">TECHNOLIFE <span aria-hidden="true">/</span> SUPORTE</p>
                <h1>Central de Links</h1>
                <p class="intro">Encontre o arquivo certo e compartilhe seu link direto.</p>
            </div>
            <p class="header-note">Arquivos disponíveis para consulta</p>
        </header>

        <section class="catalog" aria-labelledby="catalog-title">
            <div class="catalog-toolbar">
                <div>
                    <h2 id="catalog-title">Arquivos</h2>
                    <?php if (!$loadFailed): ?>
                        <p class="result-count" id="result-count" role="status" aria-live="polite"><?= escape($initialCount) ?></p>
                    <?php endif; ?>
                </div>
                <?php if (!$loadFailed && $total > 0): ?>
                    <div class="search-field" role="search">
                        <label for="catalog-search">Pesquisar arquivos</label>
                        <input id="catalog-search" type="search" placeholder="Digite um nome ou formato" autocomplete="off" aria-controls="file-list">
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($loadFailed): ?>
                <div class="state-message state-error" role="alert">
                    <h3>Não foi possível carregar os arquivos</h3>
                    <p>Tente novamente mais tarde ou avise a equipe responsável.</p>
                </div>
            <?php elseif ($total === 0): ?>
                <div class="state-message">
                    <h3>Nenhum arquivo disponível</h3>
                    <p>Quando houver arquivos para consulta, eles aparecerão aqui.</p>
                </div>
            <?php else: ?>
                <ul class="file-list" id="file-list">
                    <?php foreach ($downloads as $download): ?>
                        <li class="file-row" data-file-row data-search="<?= escape($download['title'] . ' ' . $download['filename']) ?>">
                            <div class="file-details">
                                <div class="file-heading">
                                    <h3><?= escape($download['title']) ?></h3>
                                    <?php if ($download['extension'] !== null): ?>
                                        <span class="file-extension"><?= escape($download['extension']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="file-name"><?= escape($download['filename']) ?></p>
                            </div>
                            <div class="file-actions">
                                <a class="action-link" href="<?= escape($download['url']) ?>">Abrir / baixar</a>
                                <button class="copy-button" type="button" data-copy-url="<?= escape($download['url']) ?>" aria-label="Copiar link de <?= escape($download['title']) ?>">Copiar link</button>
                                <span class="copy-feedback" role="status" aria-live="polite"></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="state-message" id="no-results" hidden>
                    <h3>Nenhum resultado</h3>
                    <p>Tente pesquisar por outro nome ou formato.</p>
                </div>
            <?php endif; ?>
        </section>

        <footer class="page-footer">Os links abrem diretamente o arquivo hospedado.</footer>
    </main>
</body>
</html>
