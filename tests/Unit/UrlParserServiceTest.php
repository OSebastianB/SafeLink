<?php

namespace Tests\Unit;

use App\Services\Url\UrlParserService;
use InvalidArgumentException;
use Tests\TestCase;

class UrlParserServiceTest extends TestCase
{
    public function test_parses_valid_https_url(): void
    {
        $parsed = app(UrlParserService::class)->parse('https://example.com/path?q=1');
        $this->assertSame('https', $parsed['scheme']);
        $this->assertSame('example.com', $parsed['domain']);
        $this->assertFalse($parsed['is_ip']);
    }

    public function test_rejects_credentials_and_invalid_protocol(): void
    {
        $parser = app(UrlParserService::class);

        $this->expectException(InvalidArgumentException::class);
        $parser->parse('https://user:pass@example.com');
    }

    public function test_detects_ipv4_and_ipv6(): void
    {
        $parser = app(UrlParserService::class);

        $v4 = $parser->parse('http://192.168.1.10/login');
        $this->assertTrue($v4['is_ip']);
        $this->assertSame('ipv4', $v4['ip_version']);

        $ipv6Url = 'http://[' . '2001:db8::1' . ']';
        $v6 = $parser->parse($ipv6Url);
        $this->assertTrue($v6['is_ip']);
        $this->assertSame('ipv6', $v6['ip_version']);
    }

    public function test_detects_private_hosts(): void
    {
        $parser = app(UrlParserService::class);
        $this->assertTrue($parser->isPrivateOrLocalHost('127.0.0.1'));
        $this->assertTrue($parser->isPrivateOrLocalHost('localhost'));
        $this->assertTrue($parser->isPrivateOrLocalHost('10.0.0.5'));
        $this->assertFalse($parser->isPrivateOrLocalHost('example.com'));
    }
}
