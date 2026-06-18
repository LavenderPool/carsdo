<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Support\Cache\SiteCache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class NewCarController extends Controller
{
    public function __invoke(Request $request, string $year): View
    {
        $page = max(1, (int) $request->integer('page', 1));

        $newCars = SiteCache::remember("new-cars:{$year}:page:{$page}", static fn () => Car::query()
            ->with(['brand:id,name,slug'])
            ->whereHas('brand')
            ->where('year', $year)
            ->where('is_soon', false)
            ->orderBy('name')
            ->paginate(30));

        return view('site.new-cars', [
            'year' => $year,
            'newCars' => $newCars,
            'navigationYears' => $this->navigationYears($year),
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function navigationYears(string $year): array
    {
        return SiteCache::remember("new-cars:{$year}:navigation", static function () use ($year): array {
            $displayYear = (int) $year;
            $candidates = [$displayYear + 1, $displayYear - 1, $displayYear + 2];

            $yearsWithCars = Car::query()
                ->whereHas('brand')
                ->whereIn('year', array_map('strval', $candidates))
                ->where('is_soon', false)
                ->distinct()
                ->pluck('year')
                ->map(static fn ($value): int => (int) $value)
                ->all();

            $hasCarsForYear = static fn (int $candidateYear): bool => in_array($candidateYear, $yearsWithCars, true);

            $navigationYears = [];

            if ($hasCarsForYear($displayYear + 1)) {
                $navigationYears[] = $displayYear + 1;
            }

            if ($hasCarsForYear($displayYear - 1)) {
                $navigationYears[] = $displayYear - 1;
            } elseif ($hasCarsForYear($displayYear + 2)) {
                $navigationYears[] = $displayYear + 2;
            }

            return array_values(array_unique($navigationYears));
        });
    }
}
