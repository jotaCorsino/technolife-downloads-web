<?php

declare(strict_types=1);

use Technolife\Downloads\DownloadScanner;
use Technolife\Downloads\DownloadScanException;

require_once __DIR__ . '/../src/DownloadScanner.php';

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

/** @return array<string, array{filename: string, title: string, extension: ?string, url: string}> */
function byFilename(array $downloads): array
{
    $indexed = [];
    foreach ($downloads as $download) {
        expect(!isset($indexed[$download['filename']]), 'Arquivo duplicado: ' . $download['filename']);
        $indexed[$download['filename']] = $download;
    }
    return $indexed;
}

function expectScanError(string $directory, string $baseUrl, string $expectedMessage): void
{
    try {
        DownloadScanner::scan($directory, $baseUrl);
    } catch (DownloadScanException $error) {
        expect($error->getMessage() === $expectedMessage, 'Mensagem de erro inesperada');
        expect(!str_contains($error->getMessage(), $directory), 'Caminho físico exposto');
        return;
    }

    throw new RuntimeException('Falha controlada esperada');
}

/** Deletes only the temporary fixture created by this test. */
function removeFixture(string $directory): void
{
    foreach (new DirectoryIterator($directory) as $entry) {
        if ($entry->isDot()) {
            continue;
        }
        $path = $entry->getPathname();
        if ($entry->isDir() && !$entry->isLink()) {
            removeFixture($path);
        } else {
            unlink($path);
        }
    }
    rmdir($directory);
}

$fixture = sys_get_temp_dir() . '/technolife-scan-test-' . bin2hex(random_bytes(8));
$downloads = $fixture . '/downloads';
$protected = $fixture . '/protected';
mkdir($fixture, 0700);
mkdir($downloads, 0700);
mkdir($protected, 0700);

$checks = 0;
try {
    expect(DownloadScanner::scan($downloads, 'https://example.invalid/downloads') === [], 'Pasta vazia não retornou []');
    $checks++;

    $files = [
        'Technolife-RustDesk-Windows.zip',
        'Olá Mundo #1?.tar.gz',
        'a-b.txt',
        'a_b.txt',
        'sem_extensao',
        '---.zip',
    ];
    foreach ($files as $filename) {
        file_put_contents($downloads . '/' . $filename, 'fixture:' . $filename);
    }
    file_put_contents($downloads . '/.htaccess', 'hidden');
    file_put_contents($downloads . '/.env', 'hidden');
    file_put_contents($downloads . "/line\nbreak.txt", 'hidden');
    file_put_contents($downloads . "/invalid-\xFF.zip", 'hidden');
    mkdir($downloads . '/subfolder');
    file_put_contents($downloads . '/subfolder/inside.zip', 'nested');
    file_put_contents($fixture . '/outside.zip', 'outside');
    expect(symlink($fixture . '/outside.zip', $downloads . '/outside-link.zip'), 'Symlink de arquivo indisponível');
    expect(symlink($downloads . '/subfolder', $downloads . '/folder-link'), 'Symlink de pasta indisponível');

    $before = [];
    foreach ($files as $filename) {
        $before[$filename] = hash_file('sha256', $downloads . '/' . $filename);
    }
    $result = DownloadScanner::scan($downloads, 'https://example.invalid/downloads/');
    $indexed = byFilename($result);
    expect(count($result) === count($files), 'Filtro incluiu ou omitiu entradas');
    foreach ($files as $filename) {
        expect(isset($indexed[$filename]), 'Arquivo ausente: ' . $filename);
        expect(hash_file('sha256', $downloads . '/' . $filename) === $before[$filename], 'Arquivo alterado');
    }
    foreach (['.htaccess', '.env', 'subfolder', 'outside-link.zip', 'folder-link', "line\nbreak.txt", "invalid-\xFF.zip"] as $excluded) {
        expect(!isset($indexed[$excluded]), 'Entrada proibida incluída');
    }
    $checks++;

    expect($indexed['Technolife-RustDesk-Windows.zip']['title'] === 'Technolife RustDesk Windows', 'Título normalizado incorreto');
    expect($indexed['Technolife-RustDesk-Windows.zip']['extension'] === 'ZIP', 'Extensão incorreta');
    expect($indexed['sem_extensao']['title'] === 'sem extensao', 'Título sem extensão incorreto');
    expect($indexed['sem_extensao']['extension'] === null, 'Extensão inexistente não é null');
    expect($indexed['---.zip']['title'] === '---.zip', 'Fallback de título vazio falhou');
    $checks++;

    expect(
        $indexed['Olá Mundo #1?.tar.gz']['url'] === 'https://example.invalid/downloads/Ol%C3%A1%20Mundo%20%231%3F.tar.gz',
        'URL especial não preservou/codificou o nome original'
    );
    expect($indexed['Olá Mundo #1?.tar.gz']['title'] === 'Olá Mundo #1?.tar', 'Título com extensão final incorreto');
    expect($indexed['Olá Mundo #1?.tar.gz']['extension'] === 'GZ', 'Extensão final incorreta');
    expect($indexed['Olá Mundo #1?.tar.gz']['filename'] === 'Olá Mundo #1?.tar.gz', 'Nome original alterado');
    $checks++;

    $names = array_column($result, 'filename');
    expect(array_search('a-b.txt', $names, true) < array_search('a_b.txt', $names, true), 'Desempate por filename incorreto');
    $titles = array_column($result, 'title');
    $sortedTitles = $titles;
    usort($sortedTitles, 'strcmp');
    expect($titles === $sortedTitles, 'Ordenação por título incorreta');
    $checks++;

    file_put_contents($downloads . '/novo_arquivo.txt', 'new');
    expect(isset(byFilename(DownloadScanner::scan($downloads, 'https://example.invalid/downloads'))['novo_arquivo.txt']), 'Novo arquivo não apareceu');
    unlink($downloads . '/novo_arquivo.txt');
    expect(!isset(byFilename(DownloadScanner::scan($downloads, 'https://example.invalid/downloads'))['novo_arquivo.txt']), 'Arquivo removido ainda aparece');
    $checks++;

    expectScanError($fixture . '/missing', 'https://example.invalid/downloads', 'Não foi possível ler os downloads.');
    expectScanError($fixture . '/outside.zip', 'https://example.invalid/downloads', 'Não foi possível ler os downloads.');
    $checks++;

    chmod($protected, 0000);
    if (@scandir($protected) === false) {
        expectScanError($protected, 'https://example.invalid/downloads', 'Não foi possível ler os downloads.');
        $checks++;
    } else {
        echo "SKIP: permissão de leitura privilegiada impede simular diretório inacessível\n";
    }
    chmod($protected, 0700);

    foreach ([
        'http://example.invalid/downloads',
        '//example.invalid/downloads',
        'https://user:secret@example.invalid/downloads',
        'https://example.invalid/downloads?file=x',
        'https://example.invalid/downloads#section',
        'https://example.invalid/downloads\\extra',
        'https://example.invalid/downloads with space',
    ] as $badUrl) {
        expectScanError($downloads, $badUrl, 'Configuração de downloads inválida.');
    }
    $checks++;

    echo "PASS: {$checks} grupos de verificações; " . count($files) . " arquivos regulares; filtros, URLs, ordenação, erros e atualização validados.\n";
    echo "EXEMPLO: {$indexed['Technolife-RustDesk-Windows.zip']['title']} | {$indexed['Technolife-RustDesk-Windows.zip']['extension']} | {$indexed['Technolife-RustDesk-Windows.zip']['url']}\n";
    echo "EXEMPLO: {$indexed['Olá Mundo #1?.tar.gz']['title']} | {$indexed['Olá Mundo #1?.tar.gz']['extension']} | {$indexed['Olá Mundo #1?.tar.gz']['url']}\n";
} finally {
    chmod($protected, 0700);
    removeFixture($fixture);
}
