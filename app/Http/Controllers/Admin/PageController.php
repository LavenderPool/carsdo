<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use App\Support\Seo\AdminSeoFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));
        $status = (string) $request->string('status');

        $pages = Page::query()
            ->when($search !== '', fn ($query) => $query->where(function ($nested) use ($search) {
                $nested
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            }))
            ->when($status !== '' && in_array($status, ['published', 'draft'], true), function ($query) use ($status) {
                if ($status === 'published') {
                    $query->where('is_published', true);
                }

                if ($status === 'draft') {
                    $query->where('is_published', false);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Page $page): array => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'is_published' => $page->is_published,
                'published_at' => $page->published_at?->toDateTimeString(),
                'sort_order' => $page->sort_order,
                'updated_at' => $page->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pages/Create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        Page::create($request->validated());

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Страница создана.');
    }

    public function edit(Page $page): Response
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => array_merge([
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'excerpt' => $page->excerpt,
                'body' => $page->body,
                'body_json' => $page->body_json !== null
                    ? json_encode($page->body_json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null,
                'is_published' => $page->is_published,
                'published_at' => $page->published_at?->format('Y-m-d\TH:i'),
                'sort_order' => $page->sort_order,
            ], $page->only(AdminSeoFields::pageFields())),
        ]);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $page->update($request->validated());

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Страница обновлена.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        Page::query()
            ->whereKey($page->id)
            ->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Страница удалена.');
    }
}
