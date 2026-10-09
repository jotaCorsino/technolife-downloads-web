<?php

declare(strict_types=1);

use Technolife\Downloads\DownloadScanner;

// Set only after confirming that the resulting directory is outside every webroot.
// 0 keeps a package without server configuration closed by default.
const PRIVATE_PARENT_LEVELS = 0;

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function privateDirectory(): ?string
{
    $fromEnvironment = getenv('TECHNOLIFE_LINKS_PRIVATE_DIR');
    if (is_string($fromEnvironment) && $fromEnvironment !== '') {
        return $fromEnvironment;
    }

    if (PRIVATE_PARENT_LEVELS < 2) {
        return null;
    }

    return dirname(__DIR__, PRIVATE_PARENT_LEVELS) . '/technolife-links-private';
}

/** @return array{0: ?string, 1: ?string} */
function downloadConfiguration(string $privateDirectory): array
{
    $directory = getenv('TECHNOLIFE_DOWNLOADS_DIR');
    $baseUrl = getenv('TECHNOLIFE_DOWNLOADS_BASE_URL');

    if (is_string($directory) && $directory !== '' && is_string($baseUrl) && $baseUrl !== '') {
        return [$directory, $baseUrl];
    }

    // A partial environment configuration is an error, not a reason to mix sources.
    if (($directory !== false && $directory !== '') || ($baseUrl !== false && $baseUrl !== '')) {
        return [null, null];
    }

    $configFile = $privateDirectory . '/config.php';
    if (!@is_file($configFile)) {
        return [null, null];
    }

    try {
        $config = @include $configFile;
    } catch (Throwable) {
        return [null, null];
    }

    if (!is_array($config)) {
        return [null, null];
    }

    return [
        is_string($config['downloads_dir'] ?? null) ? $config['downloads_dir'] : null,
        is_string($config['downloads_base_url'] ?? null) ? $config['downloads_base_url'] : null,
    ];
}

function privateDirectoryIsSafe(string $directory): bool
{
    $private = @realpath($directory);
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $webroot = @realpath(is_string($documentRoot) && $documentRoot !== '' ? $documentRoot : __DIR__);

    return $private !== false
        && $webroot !== false
        && $private !== $webroot
        && !str_starts_with($private, rtrim($webroot, '/') . '/')
        // cPanel may place this domain below public_html, which can itself serve another domain.
        && preg_match('~(?:^|/)public_html(?:/|$)~', $private) !== 1;
}

$downloads = [];
$loadFailed = true;
$private = privateDirectory();

if ($private !== null && privateDirectoryIsSafe($private)) {
    [$directory, $baseUrl] = downloadConfiguration($private);
    $scannerFile = $private . '/DownloadScanner.php';

    if ($directory !== null && $directory !== '' && $baseUrl !== null && $baseUrl !== '' && @is_file($scannerFile)) {
        try {
            @require_once $scannerFile;
            $downloads = DownloadScanner::scan($directory, $baseUrl);
            $loadFailed = false;
        } catch (Throwable) {
            // Never expose configuration, filesystem paths or internal errors in HTML.
        }
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
    <meta name="theme-color" content="#071a52">
    <title>Central de Links | Technolife</title>
    <link rel="icon" type="image/png" href="assets/favicon-technolife.png">
    <link rel="stylesheet" href="styles.css">
    <script src="app.js" defer></script>
</head>
<body>
    <a class="skip-link" href="#main-content">Ir para o conteúdo</a>
    <header class="topbar">
        <div class="topbar__content">
            <div class="brand" aria-label="Technolife Informática — Downloads">
                <img class="brand__logo" src="assets/logo-technolife.png" alt="Technolife Informática" width="243" height="129">
                <span class="brand__divider" aria-hidden="true"></span>
                <span class="brand__product">Downloads</span>
            </div>
            <span class="read-only-badge">
                <svg aria-hidden="true" viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>
                Somente leitura
            </span>
        </div>
    </header>

    <main class="page-shell" id="main-content">
        <header class="page-heading">
            <div>
                <p class="eyebrow">CATÁLOGO DE DOWNLOADS</p>
                <h1>Central de Links</h1>
                <p class="intro">Encontre o arquivo certo e compartilhe seu link direto.</p>
            </div>
            <p class="page-note">A lista é atualizada ao recarregar a página.</p>
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
                        <div class="search-control">
                            <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="m16 16 4 4"></path></svg>
                            <input id="catalog-search" type="search" placeholder="Digite um nome ou formato" autocomplete="off" aria-controls="file-list">
                        </div>
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
                                <a class="icon-action icon-action--open" href="<?= escape($download['url']) ?>" aria-label="Abrir ou baixar <?= escape($download['filename']) ?>" title="Abrir ou baixar">
                                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v3h16v-3"></path></svg>
                                </a>
                                <button class="icon-action icon-action--copy copy-button" type="button" data-copy-url="<?= escape($download['url']) ?>" aria-label="Copiar link de <?= escape($download['filename']) ?>" title="Copiar link">
                                    <svg aria-hidden="true" viewBox="0 0 24 24"><rect x="8" y="8" width="11" height="12" rx="2"></rect><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h2"></path></svg>
                                </button>
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
