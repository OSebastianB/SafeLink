<?php

namespace App\Services\Url;

class UrlFeatureExtractionService
{
    public function __construct(
        private readonly UrlParserService $parser
    ) {
    }

    /**
     * Extract deterministic structural features from a parsed URL.
     * Never performs network access to the target URL.
     *
     * @param  array<string, mixed>  $parsed
     * @return array<string, mixed>
     */
    public function extract(array $parsed): array
    {
        $url = $parsed['normalized_url'];
        $host = $parsed['host'];
        $path = $parsed['path'] ?? '/';
        $query = $parsed['query'] ?? '';
        $full = $url;

        $subdomainCount = $this->countSubdomains($host, (bool) $parsed['is_ip']);
        $urlLength = mb_strlen($full);
        $hasAtSymbol = str_contains($full, '@');
        $encodedCount = substr_count(strtolower($full), '%');
        $hyphenCount = substr_count($host, '-');
        $digitRatio = $this->digitRatio($host);
        $suspiciousTld = $this->hasSuspiciousTld($host);
        $pathDepth = substr_count(trim($path, '/'), '/');
        if (trim($path, '/') !== '') {
            $pathDepth += 1;
        }

        $keywords = $this->findSuspiciousKeywords($full);
        $hasIpHost = (bool) $parsed['is_ip'];

        return [
            'url_length' => $urlLength,
            'host' => $host,
            'registrable_domain' => $parsed['registrable_domain'],
            'scheme' => $parsed['scheme'],
            'subdomain_count' => $subdomainCount,
            'has_at_symbol' => $hasAtSymbol,
            'encoded_char_count' => $encodedCount,
            'hyphen_count_in_host' => $hyphenCount,
            'digit_ratio_in_host' => $digitRatio,
            'suspicious_tld' => $suspiciousTld,
            'path_depth' => $pathDepth,
            'query_length' => mb_strlen($query),
            'has_ip_host' => $hasIpHost,
            'ip_version' => $parsed['ip_version'],
            'suspicious_keywords' => $keywords,
            'is_private_or_local' => $this->parser->isPrivateOrLocalHost($host),
        ];
    }

    private function countSubdomains(string $host, bool $isIp): int
    {
        if ($isIp) {
            return 0;
        }

        $registrable = $this->parser->extractRegistrableDomain($host);
        if ($host === $registrable) {
            return 0;
        }

        $prefix = substr($host, 0, -strlen($registrable) - 1);
        if ($prefix === false || $prefix === '') {
            return 0;
        }

        return count(explode('.', $prefix));
    }

    private function digitRatio(string $host): float
    {
        $len = strlen($host);
        if ($len === 0) {
            return 0.0;
        }

        $digits = preg_match_all('/\d/', $host);

        return $digits / $len;
    }

    private function hasSuspiciousTld(string $host): bool
    {
        $suspicious = ['tk', 'ml', 'ga', 'cf', 'gq', 'xyz', 'top', 'click', 'link', 'work'];
        $parts = explode('.', strtolower($host));
        $tld = end($parts);

        return in_array($tld, $suspicious, true);
    }

    /**
     * @return list<string>
     */
    private function findSuspiciousKeywords(string $url): array
    {
        $patterns = [
            'login', 'verify', 'account', 'update', 'secure', 'password',
            'signin', 'banking', 'confirm', 'credential', 'wallet', 'unlock',
        ];

        $found = [];
        $haystack = strtolower($url);

        foreach ($patterns as $pattern) {
            if (str_contains($haystack, $pattern)) {
                $found[] = $pattern;
            }
        }

        return $found;
    }
}
