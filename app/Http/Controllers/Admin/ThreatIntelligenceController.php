<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThreatIntelligence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThreatIntelligenceController extends Controller
{
    public function index(Request $request): View
    {
        $query = ThreatIntelligence::query()->latest();

        if ($search = trim((string) $request->get('q', ''))) {
            $query->where('domain', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $records = $query->paginate(20)->withQueryString();

        return view('admin.threats.index', compact('records'));
    }

    public function create(): View
    {
        return view('admin.threats.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['domain'] = strtolower($data['domain']);
        $data['is_synthetic'] = $request->boolean('is_synthetic');

        ThreatIntelligence::query()->create($data);

        return redirect()->route('admin.threats.index')->with('success', 'Data reputasi domain ditambahkan.');
    }

    public function edit(ThreatIntelligence $threat): View
    {
        return view('admin.threats.edit', ['record' => $threat]);
    }

    public function update(Request $request, ThreatIntelligence $threat): RedirectResponse
    {
        $data = $this->validated($request);
        $data['domain'] = strtolower($data['domain']);
        $data['is_synthetic'] = $request->boolean('is_synthetic');
        $threat->update($data);

        return redirect()->route('admin.threats.index')->with('success', 'Data reputasi domain diperbarui.');
    }

    public function destroy(ThreatIntelligence $threat): RedirectResponse
    {
        $threat->delete();

        return redirect()->route('admin.threats.index')->with('success', 'Data reputasi dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            if (! $data || empty($data['domain']) || empty($data['status'])) {
                continue;
            }

            $domain = strtolower(trim($data['domain']));
            if (! preg_match('/^[a-z0-9.-]+$/', $domain) && filter_var($domain, FILTER_VALIDATE_IP) === false) {
                continue;
            }

            ThreatIntelligence::query()->updateOrCreate(
                [
                    'domain' => $domain,
                    'source' => $data['source'] ?? 'csv_import',
                ],
                [
                    'status' => $data['status'],
                    'threat_type' => $data['threat_type'] ?? null,
                    'verified_at' => ! empty($data['verified_at']) ? $data['verified_at'] : now(),
                    'expires_at' => $data['expires_at'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'is_synthetic' => true,
                ]
            );
            $imported++;
        }

        fclose($handle);

        return back()->with('success', "Import selesai. {$imported} baris diproses.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'domain' => ['required', 'string', 'max:255'],
            'url_hash' => ['nullable', 'string', 'max:64'],
            'status' => ['required', 'in:malicious,phishing,suspicious,no_match,unknown'],
            'threat_type' => ['nullable', 'string', 'max:60'],
            'source' => ['required', 'string', 'max:255'],
            'verified_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:verified_at'],
            'notes' => ['nullable', 'string'],
            'is_synthetic' => ['nullable', 'boolean'],
        ]);
    }
}
