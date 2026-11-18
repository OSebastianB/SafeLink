<?php

namespace App\Providers;

use App\Services\Domain\DomainAgeProviderInterface;
use App\Services\Domain\NullDomainAgeProvider;
use App\Services\Domain\RdapDomainAgeProvider;
use App\Services\Reputation\LocalThreatIntelligenceProvider;
use App\Services\Reputation\NullThreatIntelligenceProvider;
use App\Services\Reputation\ThreatIntelligenceProviderInterface;
use Illuminate\Support\ServiceProvider;

class SafelinkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ThreatIntelligenceProviderInterface::class, function () {
            return match (config('safelink.threat_provider', 'local')) {
                'null' => new NullThreatIntelligenceProvider,
                default => new LocalThreatIntelligenceProvider,
            };
        });

        $this->app->bind(DomainAgeProviderInterface::class, function () {
            if (config('safelink.rdap_enabled')) {
                return new RdapDomainAgeProvider(true);
            }

            return new NullDomainAgeProvider;
        });
    }

    public function boot(): void
    {
        //
    }
}
