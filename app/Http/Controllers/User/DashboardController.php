<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $stats = [
            'total' => Scan::query()->where('user_id', $userId)->count(),
            'low' => Scan::query()->where('user_id', $userId)->where('risk_category', 'low')->count(),
            'medium' => Scan::query()->where('user_id', $userId)->where('risk_category', 'medium')->count(),
            'high' => Scan::query()->where('user_id', $userId)->where('risk_category', 'high')->count(),
        ];

        $recent = Scan::query()
            ->where('user_id', $userId)
            ->latest('scanned_at')
            ->limit(10)
            ->get();

        return view('user.dashboard', compact('stats', 'recent'));
    }
}
