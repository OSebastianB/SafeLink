<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationDataset;
use App\Models\EvaluationDatasetRow;
use App\Services\Evaluation\DatasetEvaluationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    public function index(): View
    {
        $datasets = EvaluationDataset::query()->withCount('rows')->latest()->paginate(10);

        return view('admin.evaluation.index', compact('datasets'));
    }

    public function create(): View
    {
        return view('admin.evaluation.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), fgetcsv($handle) ?: []);

        if (! in_array('url', $header, true) || ! in_array('label', $header, true)) {
            fclose($handle);

            return back()->with('error', 'CSV harus memiliki kolom url dan label.');
        }

        $dataset = EvaluationDataset::query()->create([
            'name' => $request->input('name'),
            'source' => $request->input('source'),
            'description' => $request->input('description'),
            'total_rows' => 0,
            'imported_by' => Auth::id(),
        ]);

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            if (! $data || empty($data['url'])) {
                continue;
            }

            $label = strtolower(trim((string) $data['label']));
            if (! in_array($label, ['phishing', 'legitimate'], true)) {
                continue;
            }

            EvaluationDatasetRow::query()->create([
                'dataset_id' => $dataset->id,
                'url' => trim($data['url']),
                'ground_truth_label' => $label,
                'source_reference' => $data['source'] ?? null,
            ]);
            $count++;
        }
        fclose($handle);

        $dataset->update(['total_rows' => $count]);

        return redirect()->route('admin.evaluation.show', $dataset)
            ->with('success', "Dataset diimpor ({$count} baris valid).");
    }

    public function show(EvaluationDataset $dataset): View
    {
        $dataset->load('rows');

        return view('admin.evaluation.show', compact('dataset'));
    }

    public function run(Request $request, EvaluationDataset $dataset, DatasetEvaluationService $service): View
    {
        $mode = $request->input('mode', 'binary');
        if (! in_array($mode, ['binary', 'multiclass'], true)) {
            $mode = 'binary';
        }

        $result = $service->evaluate($dataset, $mode);

        return view('admin.evaluation.result', compact('dataset', 'result', 'mode'));
    }

    public function destroy(EvaluationDataset $dataset): RedirectResponse
    {
        $dataset->delete();

        return redirect()->route('admin.evaluation.index')->with('success', 'Dataset dihapus.');
    }
}
