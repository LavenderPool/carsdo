<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarConfiguration;
use App\Models\CarConfigurationEquipment;
use App\Models\CarConfigurationEquipmentCategory;
use App\Models\CarConfigurationGroup;
use App\Models\CarCrashTest;
use App\Models\CarPhoto;
use App\Models\CarPhotoGroup;
use App\Models\CarReview;
use App\Models\CarTestDrive;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DangerController extends Controller
{
    public function fullClear(): RedirectResponse
    {
        DB::transaction(function (): void {
            CarConfigurationEquipment::query()->delete();
            CarConfigurationEquipmentCategory::query()->delete();
            CarConfiguration::query()->delete();
            CarConfigurationGroup::query()->delete();
            CarPhoto::query()->delete();
            CarPhotoGroup::query()->delete();
            CarCrashTest::query()->delete();
            CarReview::query()->delete();
            CarTestDrive::query()->delete();
            Car::query()->delete();
            Brand::withTrashed()->forceDelete();
        });

        Storage::disk('public')->deleteDirectory('covers');

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Все бренды, автомобили и связанные записи удалены.');
    }
}
