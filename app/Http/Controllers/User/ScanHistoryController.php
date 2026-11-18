<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScanHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Scan::query()
            ->where('user_id', Auth::id())
            ->latest('scanned_at');

        if ($search = trim((string) $request->get('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('original_url', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->where('risk_category', $category);
        }

        if ($from = $request->get('from')) {
            $query->whereDate('scanned_at', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('scanned_at', '<=', $to);
        }

        $scans = $query->paginate(15)->withQueryString();

        return view('user.history', compact('scans'));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Scan::query()
            ->where('user_id', Auth::id())
            ->latest('scanned_at');

        if ($category = $request->get('category')) {
            $query->where('risk_category', $category);
        }

        $filename = 'safelink-history-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'url', 'domain', 'security_score', 'risk_index', 'risk_category', 'scanned_at']);

            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $scan) {
                    fputcsv($handle, [
                        $scan->id,
                        $scan->original_url,
                        $scan->domain,
                        $scan->security_score,
                        $scan->risk_index,
                        $scan->risk_category,
                        optional($scan->scanned_at)?->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
