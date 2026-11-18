<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScanUrlRequest;
use App\Models\Scan;
use App\Services\Scan\UrlScanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function create(): View
    {
        return view('public.scan');
    }

    public function store(ScanUrlRequest $request, UrlScanService $scanService): RedirectResponse
    {
        $scan = $scanService->scan($request->validated('url'), Auth::user());

        return redirect()->route('scan.show', $scan);
    }

    public function show(Scan $scan): View
    {
        $scan->load('criterionResults');

        // Guest scans are public by token/id for demo; authenticated users can only
        // view their own scans unless admin.
        $user = Auth::user();
        if ($scan->user_id !== null) {
            if (! $user) {
                abort(403, 'Silakan masuk untuk melihat riwayat pemeriksaan ini.');
            }
            if (! $user->isAdmin() && $user->id !== $scan->user_id) {
                abort(403, 'Anda tidak berwenang melihat hasil pemeriksaan ini.');
            }
        }

        return view('public.result', compact('scan'));
    }
}
