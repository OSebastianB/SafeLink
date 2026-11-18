<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = [
            'risk_low_max' => AppSetting::getValue('risk_low_max', 20),
            'risk_medium_max' => AppSetting::getValue('risk_medium_max', 50),
            'min_data_coverage_warning' => AppSetting::getValue('min_data_coverage_warning', 50),
            'app_disclaimer' => AppSetting::getValue('app_disclaimer', ''),
        ];

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'risk_low_max' => ['required', 'numeric', 'min:0', 'max:100'],
            'risk_medium_max' => ['required', 'numeric', 'min:0', 'max:100', 'gt:risk_low_max'],
            'min_data_coverage_warning' => ['required', 'numeric', 'min:0', 'max:100'],
            'app_disclaimer' => ['nullable', 'string'],
        ]);

        foreach ($data as $key => $value) {
            AppSetting::setValue($key, $value);
            Cache::forget("app_setting_{$key}");
        }

        return back()->with('success', 'Pengaturan kategori risiko diperbarui.');
    }
}
