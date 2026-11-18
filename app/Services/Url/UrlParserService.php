<?php

namespace App\Services\Url;

use InvalidArgumentException;

class UrlParserService
{
    public const MAX_URL_LENGTH = 2048;

    /**
     * Parse and validate a URL without making any network request to the target.
     *
     * @return array{
     *     original_url: string,
     *     normalized_url: string,
     *     scheme: string,
     *     host: string,
     *     domain: string,
     *     registrable_domain: string,
     *     path: string,
     *     query: string,
     *     port: int|null,
     *     is_ip: bool,
     *     ip_version: string|null,
     *     punycode_host: string
     * }
     */
    public function parse(string $url): array
    {
        $url = trim($url);

        if ($url === '') {
            throw new InvalidArgumentException('URL tidak boleh kosong.');
        }

        if (mb_strlen($url) > self::MAX_URL_LENGTH) {
            throw new InvalidArgumentException('URL melebihi batas panjang maksimum 2.048 karakter.');
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $url)) {
            throw new InvalidArgumentException('URL mengandung karakter kontrol yang tidak diizinkan.');
        }

        if (preg_match('#^([a-z][a-z0-9+.-]*)://#i', $url, $schemeMatch)) {
            $declaredScheme = strtolower($schemeMatch[1]);
            if (! in_array($declaredScheme, ['http', 'https'], true)) {
                throw new InvalidArgumentException('Hanya protokol HTTP dan HTTPS yang diperbolehkan.');
            }
        } else {
            $url = 'http://'.$url;
        }

        $parts = parse_url($url);

        if ($parts === false || empty($parts['host'])) {
            throw new InvalidArgumentException('Format URL tidak valid atau domain tidak dapat diparsing.');
        }

        $scheme = strtolower($parts['scheme'] ?? '');
        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('Hanya protokol HTTP dan HTTPS yang diperbolehkan.');
        }


        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new InvalidArgumentException('URL dengan kredensial username/password tertanam tidak diizinkan.');
        }

        $host = strtolower($parts['host']);
        $host = rtrim($host, '.');
        $host = trim($host, '[]');

        if (preg_match('/\s/', $host) || $host === '') {
            throw new InvalidArgumentException('Format URL tidak valid atau domain tidak dapat diparsing.');
        }

        // Convert IDN to ASCII punycode when possible.
        $punycodeHost = $host;
        if (! $this->isIpAddress($host) && function_exists('idn_to_ascii')) {
            $converted = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if ($converted !== false) {
                $punycodeHost = strtolower($converted);
            }
        }

        $isIp = $this->isIpAddress($punycodeHost);
        $ipVersion = null;
        if ($isIp) {
            $normalizedIp = trim($punycodeHost, '[]');
            $ipVersion = filter_var($normalizedIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 'ipv6' : 'ipv4';
        } elseif (! preg_match('/^[a-z0-9.-]+$/i', $punycodeHost)) {
            throw new InvalidArgumentException('Format URL tidak valid atau domain tidak dapat diparsing.');
        }


        $path = $parts['path'] ?? '/';
        $query = $parts['query'] ?? '';
        $port = $parts['port'] ?? null;

        $normalized = $scheme.'://'.$punycodeHost;
        if ($port !== null) {
            $normalized .= ':'.$port;
        }
        $normalized .= $path === '' ? '/' : $path;
        if ($query !== '') {
            $normalized .= '?'.$query;
        }

        $registrableDomain = $isIp ? $punycodeHost : $this->extractRegistrableDomain($punycodeHost);

        return [
            'original_url' => trim($url),
            'normalized_url' => $normalized,
            'scheme' => $scheme,
            'host' => $punycodeHost,
            'domain' => $punycodeHost,
            'registrable_domain' => $registrableDomain,
            'path' => $path,
            'query' => $query,
            'port' => $port,
            'is_ip' => $isIp,
            'ip_version' => $ipVersion,
            'punycode_host' => $punycodeHost,
        ];
    }

    public function isIpAddress(string $host): bool
    {
        $host = trim($host, '[]');

        return filter_var($host, FILTER_VALIDATE_IP) !== false;
    }

    public function isPrivateOrLocalHost(string $host): bool
    {
        $host = trim($host, '[]');

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return true;
        }

        if (! $this->isIpAddress($host)) {
            return false;
        }

        return filter_var(
            $host,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }

    /**
     * Simplified registrable domain extraction (eTLD+1 heuristic).
     */
    public function extractRegistrableDomain(string $host): string
    {
        $host = strtolower(trim($host, '.'));
        $parts = explode('.', $host);

        if (count($parts) <= 2) {
            return $host;
        }

        $multiPartTlds = ['co.id', 'or.id', 'ac.id', 'go.id', 'co.uk', 'com.au', 'co.jp'];
        $lastTwo = $parts[count($parts) - 2].'.'.$parts[count($parts) - 1];

        if (in_array($lastTwo, $multiPartTlds, true) && count($parts) >= 3) {
            return $parts[count($parts) - 3].'.'.$lastTwo;
        }

        return $lastTwo;
    }
}
