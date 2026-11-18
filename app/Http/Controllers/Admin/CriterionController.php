<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCriterionRequest;
use App\Http\Requests\UpdateCriterionRequest;
use App\Models\Criterion;
use App\Models\CriterionScale;
use App\Models\WeightChangeLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CriterionController extends Controller
{
    public function index(): View
    {
        $criteria = Criterion::query()->with('scales')->orderBy('sort_order')->get();
        $totalWeight = $criteria->where('is_active', true)->sum('weight');

        return view('admin.criteria.index', compact('criteria', 'totalWeight'));
    }

    public function create(): View
    {
        return view('admin.criteria.create');
    }

    public function store(StoreCriterionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_core'] = false;
        $data['attribute'] = $data['attribute'] ?? 'benefit';
        $data['max_score'] = $data['max_score'] ?? 5;

        Criterion::query()->create($data);

        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function edit(Criterion $criterion): View
    {
        $criterion->load('scales');

        return view('admin.criteria.edit', compact('criterion'));
    }

    public function update(UpdateCriterionRequest $request, Criterion $criterion): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $oldWeight = (float) $criterion->weight;
        $criterion->update($data);

        if (abs($oldWeight - (float) $criterion->weight) > 0.0001) {
            WeightChangeLog::query()->create([
                'user_id' => Auth::id(),
                'criterion_id' => $criterion->id,
                'criterion_code' => $criterion->code,
                'old_weight' => $oldWeight,
                'new_weight' => $criterion->weight,
                'notes' => 'Perubahan bobot melalui form kriteria',
            ]);
        }

        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Criterion $criterion): RedirectResponse
    {
        if ($criterion->is_core) {
            return back()->with('error', 'Kriteria inti penelitian tidak boleh dihapus.');
        }

        $criterion->delete();

        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }

    public function storeScale(Request $request, Criterion $criterion): RedirectResponse
    {
        $data = $request->validate([
            'condition_name' => ['required', 'string', 'max:255'],
            'condition_description' => ['nullable', 'string'],
            'score' => ['required', 'numeric', 'min:0', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $criterion->scales()->create([
            'condition_name' => $data['condition_name'],
            'condition_description' => $data['condition_description'] ?? null,
            'score' => $data['score'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Skala penilaian ditambahkan.');
    }

    public function updateScale(Request $request, CriterionScale $scale): RedirectResponse
    {
        $data = $request->validate([
            'condition_name' => ['required', 'string', 'max:255'],
            'condition_description' => ['nullable', 'string'],
            'score' => ['required', 'numeric', 'min:0', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $scale->update([
            'condition_name' => $data['condition_name'],
            'condition_description' => $data['condition_description'] ?? null,
            'score' => $data['score'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Skala penilaian diperbarui.');
    }

    public function destroyScale(CriterionScale $scale): RedirectResponse
    {
        $scale->delete();

        return back()->with('success', 'Skala penilaian dihapus.');
    }
}
