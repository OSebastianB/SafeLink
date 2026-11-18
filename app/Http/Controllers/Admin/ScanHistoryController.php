<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Scan::query()->with('user')->latest('scanned_at');

        if ($search = trim((string) $request->get('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('original_url', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->where('risk_category', $category);
        }

        $scans = $query->paginate(20)->withQueryString();

        return view('admin.scans.index', compact('scans'));
    }

    public function show(Scan $scan): View
    {
        $scan->load(['criterionResults', 'user']);

        return view('admin.scans.show', compact('scan'));
    }
}
