<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandWatchlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandWatchlistController extends Controller
{
    public function index(): View
    {
        $brands = BrandWatchlist::query()->orderBy('brand_name')->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['keywords'] = $this->parseKeywords($data['keywords'] ?? '');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['official_domain'] = strtolower($data['official_domain']);

        BrandWatchlist::query()->create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand watchlist ditambahkan.');
    }

    public function edit(BrandWatchlist $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, BrandWatchlist $brand): RedirectResponse
    {
        $data = $this->validated($request);
        $data['keywords'] = $this->parseKeywords($data['keywords'] ?? '');
        $data['is_active'] = $request->boolean('is_active');
        $data['official_domain'] = strtolower($data['official_domain']);
        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand watchlist diperbarui.');
    }

    public function destroy(BrandWatchlist $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand watchlist dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'brand_name' => ['required', 'string', 'max:255'],
            'official_domain' => ['required', 'string', 'max:255'],
            'keywords' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function parseKeywords(string $raw): array
    {
        return array_values(array_filter(array_map(
            fn ($k) => strtolower(trim($k)),
            preg_split('/[,;\n]+/', $raw) ?: []
        )));
    }
}
