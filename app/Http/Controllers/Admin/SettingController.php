<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use App\Support\Seo\AdminSeoFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(): Response
    {
        $setting = $this->resolveSetting();

        return Inertia::render('Admin/Settings/Edit', [
            'setting' => array_merge([
                'brand_name' => $setting->brand_name,
                'favicon_url' => $setting->favicon_path
                    ? asset('storage/'.$setting->favicon_path)
                    : asset('favicon.ico'),
            ], $setting->only(AdminSeoFields::settingFields())),
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $setting = $this->resolveSetting();
        $validated = $request->validated();

        if ($request->hasFile('favicon')) {
            if ($setting->favicon_path) {
                Storage::disk('public')->delete($setting->favicon_path);
            }

            $validated['favicon_path'] = $request->file('favicon')->store('settings/favicon', 'public');
        }

        unset($validated['favicon']);

        $setting->update($validated);

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Настройки обновлены.');
    }

    private function resolveSetting(): Setting
    {
        return Setting::query()->firstOrCreate(
            ['id' => 1],
            ['brand_name' => 'carsDo'],
        );
    }
}
