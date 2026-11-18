<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\WeightChangeLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WeightController extends Controller
{
    public function edit(): View
    {
        $criteria = Criterion::query()->where('is_active', true)->orderBy('sort_order')->get();
        $logs = WeightChangeLog::query()->with('user')->latest()->limit(20)->get();

        return view('admin.weights.edit', compact('criteria', 'logs'));
    }

    public function update(Request $request): RedirectResponse
    {
        $weights = $request->input('weights', []);
        if (! is_array($weights) || $weights === []) {
            return back()->with('error', 'Bobot tidak valid.');
        }

        $total = 0.0;
        foreach ($weights as $weight) {
            if (! is_numeric($weight) || (float) $weight < 0) {
                return back()->with('error', 'Bobot tidak boleh negatif dan harus numerik.');
            }
            $total += (float) $weight;
        }

        if (abs($total - 1.0) > 0.0001) {
            return back()->with('error', 'Total bobot harus tepat 1.00. Saat ini: '.number_format($total, 4));
        }

        DB::transaction(function () use ($weights) {
            foreach ($weights as $id => $weight) {
                $criterion = Criterion::query()->find($id);
                if (! $criterion || ! $criterion->is_active) {
                    continue;
                }

                $old = (float) $criterion->weight;
                $new = (float) $weight;

                if (abs($old - $new) > 0.0001) {
                    $criterion->update(['weight' => $new]);
                    WeightChangeLog::query()->create([
                        'user_id' => Auth::id(),
                        'criterion_id' => $criterion->id,
                        'criterion_code' => $criterion->code,
                        'old_weight' => $old,
                        'new_weight' => $new,
                        'notes' => 'Bulk weight update',
                    ]);
                }
            }
        });

        return back()->with('success', 'Bobot kriteria berhasil diperbarui dan dicatat.');
    }
}
