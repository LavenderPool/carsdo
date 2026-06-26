<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\CarCatalog;
use App\Support\Catalogs\CatalogCarSelectionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    public function __invoke(Request $request, CarCatalog $catalog, CatalogCarSelectionService $selectionService): View
    {
        if (! $catalog->is_published && ! Auth::check()) {
            abort(404);
        }

        $page = max(1, (int) $request->integer('page', 1));
        $cars = $selectionService->paginate($catalog, 30, $page);

        return view('site.catalog', [
            'catalog' => $catalog,
            'cars' => $cars,
        ]);
    }
}
