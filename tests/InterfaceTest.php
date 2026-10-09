<?php

declare(strict_types=1);

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

/** @param array<string, string> $environment */
function runPage(string $entryPoint, array $environment): string
{
    $process = proc_open(
        [PHP_BINARY, $entryPoint],
        [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes,
        null,
        $environment
    );
    check(is_resource($process), 'Não foi possível executar a página');
    $html = stream_get_contents($pipes[1]);
    $errors = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    check(proc_close($process) === 0, 'A página terminou com erro: ' . $errors);
    check($errors === '', 'A página emitiu warnings: ' . $errors);
    return $html;
}

function renderPage(?string $directory, ?string $baseUrl): string
{
    $environment = ['TECHNOLIFE_LINKS_PRIVATE_DIR' => (string) realpath(__DIR__ . '/../src')];
    if ($directory !== null) {
        $environment['TECHNOLIFE_DOWNLOADS_DIR'] = $directory;
    }
    if ($baseUrl !== null) {
        $environment['TECHNOLIFE_DOWNLOADS_BASE_URL'] = $baseUrl;
    }

    return runPage(__DIR__ . '/../public/index.php', $environment);
}

function removeTestDirectory(string $directory): void
{
    foreach (new DirectoryIterator($directory) as $entry) {
        if ($entry->isDot()) {
            continue;
        }
        $path = $entry->getPathname();
        if ($entry->isDir() && !$entry->isLink()) {
            removeTestDirectory($path);
        } else {
            unlink($path);
        }
    }
    rmdir($directory);
}

$fixture = sys_get_temp_dir() . '/technolife-ui-test-' . bin2hex(random_bytes(8));
mkdir($fixture, 0700);
$directory = $fixture . '/downloads';
mkdir($directory, 0700);

try {
    $noConfig = renderPage(null, null);
    check(str_contains($noConfig, 'Não foi possível carregar os arquivos'), 'Estado sem configuração ausente');
    check(!str_contains($noConfig, $fixture), 'Caminho físico exposto sem configuração');

    $empty = renderPage($directory, 'https://example.invalid/downloads');
    check(str_contains($empty, 'Nenhum arquivo disponível'), 'Estado de pasta vazia ausente');
    check(str_contains($empty, '0 arquivos disponíveis'), 'Contagem vazia incorreta');

    file_put_contents($directory . '/Technolife-RustDesk-Windows.zip', 'fixture');
    file_put_contents($directory . '/Olá Mundo #1?.tar.gz', 'fixture');
    file_put_contents($directory . '/ACME-<img src=x onerror=alert(1)>.zip', 'fixture');
    file_put_contents($directory . '/.htaccess', 'hidden');
    mkdir($directory . '/subfolder');
    file_put_contents($directory . '/subfolder/inside.zip', 'nested');
    check(symlink($directory . '/Technolife-RustDesk-Windows.zip', $directory . '/alias.zip'), 'Symlink indisponível');

    $html = renderPage($directory, 'https://example.invalid/downloads');
    check(substr_count($html, 'data-file-row') === 3, 'A página não exibiu exatamente três arquivos regulares');
    check(str_contains($html, 'Technolife RustDesk Windows'), 'Título normalizado ausente');
    check(str_contains($html, '3 arquivos disponíveis'), 'Contagem incorreta');
    check(str_contains($html, 'href="https://example.invalid/downloads/Ol%C3%A1%20Mundo%20%231%3F.tar.gz"'), 'Link direto codificado incorreto');
    check(str_contains($html, 'data-copy-url="https://example.invalid/downloads/Ol%C3%A1%20Mundo%20%231%3F.tar.gz"'), 'URL de cópia incorreta');
    check(str_contains($html, 'aria-label="Abrir ou baixar Technolife-RustDesk-Windows.zip"'), 'Ação de download sem nome acessível');
    check(str_contains($html, 'aria-label="Copiar link de Technolife-RustDesk-Windows.zip"'), 'Ação de cópia sem nome acessível');
    check(str_contains($html, 'assets/logo-technolife.png'), 'Marca Technolife ausente');
    check(!str_contains($html, '>Abrir / baixar</a>') && !str_contains($html, '>Copiar link</button>'), 'Ações devem exibir apenas ícones');
    check(str_contains($html, 'ACME &lt;img src=x onerror=alert(1)&gt;'), 'Nome HTML não foi escapado');
    check(!str_contains($html, '<img src=x onerror=alert(1)>'), 'HTML não confiável foi injetado');
    foreach (['.htaccess', 'alias.zip', 'inside.zip', $fixture] as $excluded) {
        check(!str_contains($html, $excluded), 'Entrada ou caminho proibido apareceu');
    }

    $badUrl = renderPage($directory, 'http://example.invalid/downloads');
    check(str_contains($badUrl, 'Não foi possível carregar os arquivos'), 'Erro de URL-base não controlado');
    check(!str_contains($badUrl, 'Technolife RustDesk Windows'), 'Lista exposta com URL-base inválida');

    $missing = renderPage($fixture . '/missing', 'https://example.invalid/downloads');
    check(str_contains($missing, 'Não foi possível carregar os arquivos'), 'Erro de leitura não controlado');
    check(!str_contains($missing, $fixture), 'Caminho físico exposto no erro');

    $webroot = $fixture . '/webroot';
    $links = $webroot . '/links';
    $private = $fixture . '/technolife-links-private';
    mkdir($webroot);
    mkdir($links);
    mkdir($private);
    $indexSource = file_get_contents(__DIR__ . '/../public/index.php');
    check(is_string($indexSource), 'Index original não pôde ser lido');
    file_put_contents($links . '/index.php', $indexSource);
    copy(__DIR__ . '/../src/DownloadScanner.php', $private . '/DownloadScanner.php');

    $unconfigured = runPage($links . '/index.php', []);
    check(str_contains($unconfigured, 'Não foi possível carregar os arquivos'), 'Pacote sem configuração não falhou fechado');

    $configuredIndex = str_replace('const PRIVATE_PARENT_LEVELS = 0;', 'const PRIVATE_PARENT_LEVELS = 2;', $indexSource);
    check($configuredIndex !== $indexSource, 'Marcador de nível privado ausente');
    file_put_contents($links . '/index.php', $configuredIndex);
    $noPrivateConfig = runPage($links . '/index.php', []);
    check(str_contains($noPrivateConfig, 'Não foi possível carregar os arquivos'), 'Configuração privada ausente não falhou fechado');

    $settings = ['downloads_dir' => $directory, 'downloads_base_url' => 'https://example.invalid/downloads'];
    file_put_contents($private . '/config.php', "<?php return " . var_export($settings, true) . ";\n");
    $configured = runPage($links . '/index.php', []);
    check(str_contains($configured, '3 arquivos disponíveis'), 'Configuração privada fora da webroot não carregou a lista');
    check(!str_contains($configured, $fixture), 'Caminho privado exposto no HTML');

    $partial = runPage($links . '/index.php', ['TECHNOLIFE_DOWNLOADS_DIR' => $directory]);
    check(str_contains($partial, 'Não foi possível carregar os arquivos'), 'Configuração parcial do ambiente não falhou fechado');

    $unsafe = $fixture . '/public_html/technolife-links-private';
    mkdir($fixture . '/public_html');
    mkdir($unsafe);
    copy(__DIR__ . '/../src/DownloadScanner.php', $unsafe . '/DownloadScanner.php');
    $unsafePage = runPage($links . '/index.php', [
        'TECHNOLIFE_LINKS_PRIVATE_DIR' => $unsafe,
        'TECHNOLIFE_DOWNLOADS_DIR' => $directory,
        'TECHNOLIFE_DOWNLOADS_BASE_URL' => 'https://example.invalid/downloads',
    ]);
    check(str_contains($unsafePage, 'Não foi possível carregar os arquivos'), 'Leitor sob public_html foi aceito');

    unlink($private . '/DownloadScanner.php');
    $missingReader = runPage($links . '/index.php', []);
    check(str_contains($missingReader, 'Não foi possível carregar os arquivos'), 'Leitor privado ausente não falhou fechado');

    echo "PASS: interface PHP — estados, listagem, escape, URLs, configuração privada separada e falhas fechadas.\n";
} finally {
    removeTestDirectory($fixture);
}
