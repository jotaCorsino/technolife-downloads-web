<?php

declare(strict_types=1);

namespace Technolife\Downloads;

use RuntimeException;

final class DownloadScanException extends RuntimeException
{
}

/**
 * Produces download metadata from one server-configured directory.
 * No path or URL in this class should come from a visitor's request.
 */
final class DownloadScanner
{
    /**
     * @return list<array{filename: string, title: string, extension: ?string, url: string}>
     * @throws DownloadScanException
     */
    public static function scan(string $directory, string $baseUrl): array
    {
        if ($directory === '') {
            throw new DownloadScanException('Não foi possível ler os downloads.');
        }

        $urlPrefix = self::validatedUrlPrefix($baseUrl);
        $entries = @scandir($directory);

        if ($entries === false) {
            throw new DownloadScanException('Não foi possível ler os downloads.');
        }

        $downloads = [];
        foreach ($entries as $filename) {
            if ($filename === '' || $filename[0] === '.') {
                continue;
            }

            // Invalid text and control characters are unsafe for later rendering.
            if (preg_match('//u', $filename) !== 1 || preg_match('/[\x00-\x1F\x7F]/', $filename) === 1) {
                continue;
            }

            // lstat checks the entry itself. It does not follow a symbolic link.
            $path = $directory . DIRECTORY_SEPARATOR . $filename;
            $stat = @lstat($path);
            if ($stat === false || ($stat['mode'] & 0170000) !== 0100000) {
                continue;
            }

            $dotPosition = strrpos($filename, '.');
            $hasExtension = $dotPosition !== false && $dotPosition > 0 && $dotPosition < strlen($filename) - 1;
            $stem = $hasExtension ? substr($filename, 0, $dotPosition) : $filename;
            $extension = $hasExtension ? strtoupper(substr($filename, $dotPosition + 1)) : null;
            $title = trim((string) preg_replace('/[-_\s]+/u', ' ', $stem));

            $downloads[] = [
                'filename' => $filename,
                'title' => $title !== '' ? $title : $filename,
                'extension' => $extension,
                'url' => $urlPrefix . rawurlencode($filename),
            ];
        }

        usort($downloads, static function (array $left, array $right): int {
            return strcmp($left['title'], $right['title']) ?: strcmp($left['filename'], $right['filename']);
        });

        return $downloads;
    }

    /** @throws DownloadScanException */
    private static function validatedUrlPrefix(string $baseUrl): string
    {
        $parts = parse_url($baseUrl);
        if (
            $parts === false
            || filter_var($baseUrl, FILTER_VALIDATE_URL) === false
            || strtolower($parts['scheme'] ?? '') !== 'https'
            || !isset($parts['host'])
            || array_key_exists('user', $parts)
            || array_key_exists('pass', $parts)
            || array_key_exists('query', $parts)
            || array_key_exists('fragment', $parts)
            || preg_match('/[\x00-\x20\x7F\\\\]/', $baseUrl) === 1
        ) {
            throw new DownloadScanException('Configuração de downloads inválida.');
        }

        return rtrim($baseUrl, '/') . '/';
    }
}
