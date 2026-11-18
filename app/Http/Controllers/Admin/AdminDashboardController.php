<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use App\Models\User;
use App\Services\Reputation\ThreatIntelligenceProviderInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(ThreatIntelligenceProviderInterface $threatProvider): View
    {
        $stats = [
            'users' => User::query()->count(),
            'scans' => Scan::query()->count(),
            'low' => Scan::query()->where('risk_category', 'low')->count(),
            'medium' => Scan::query()->where('risk_category', 'medium')->count(),
            'high' => Scan::query()->where('risk_category', 'high')->count(),
        ];

        $recent = Scan::query()->with('user')->latest('scanned_at')->limit(10)->get();

        $daily = Scan::query()
            ->select(DB::raw('DATE(scanned_at) as day'), DB::raw('COUNT(*) as total'))
            ->where('scanned_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $providerStatus = [
            'name' => $threatProvider->name(),
            'available' => $threatProvider->isAvailable(),
            'rdap_enabled' => (bool) config('safelink.rdap_enabled'),
        ];

        return view('admin.dashboard', compact('stats', 'recent', 'daily', 'providerStatus'));
    }
}
