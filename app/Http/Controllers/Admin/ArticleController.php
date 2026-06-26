<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Models\Article;
use App\Support\Media\MediaVariantService;
use App\Support\Seo\AdminSeoFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));
        $status = (string) $request->string('status');

        $articles = Article::query()
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
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Article $article): array => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'is_published' => $article->is_published,
                'published_at' => $article->published_at?->toDateTimeString(),
                'views_count' => $article->views_count,
                'created_at' => $article->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles,
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
        return Inertia::render('Admin/Articles/Create');
    }

    public function store(StoreArticleRequest $request, MediaVariantService $mediaVariantService): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['cover']);

        $article = Article::create($validated);
        $coverFile = $request->coverFile();

        if ($coverFile !== null) {
            $stored = $mediaVariantService->storeUploadedFile(
                $coverFile,
                "images/articles/{$article->slug}",
                Article::class,
                $article->id,
            );
            $article->update(['cover_path' => $stored['source_path']]);
        }

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Статья создана.');
    }

    public function edit(Article $article): Response
    {
        return Inertia::render('Admin/Articles/Edit', [
            'article' => array_merge([
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'body' => $article->body,
                'body_json' => $article->body_json !== null
                    ? json_encode($article->body_json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null,
                'cover_url' => filled($article->cover_path) ? $article->coverUrl(false) : null,
                'is_published' => $article->is_published,
                'published_at' => $article->published_at?->format('Y-m-d\TH:i'),
            ], $article->only(AdminSeoFields::articleFields())),
        ]);
    }

    public function update(
        UpdateArticleRequest $request,
        Article $article,
        MediaVariantService $mediaVariantService
    ): RedirectResponse {
        $validated = $request->validated();
        unset($validated['cover']);
        $article->update($validated);

        $coverFile = $request->coverFile();

        if ($coverFile !== null) {
            if (filled($article->cover_path)) {
                Storage::disk('public')->delete((string) $article->cover_path);
                $mediaVariantService->deleteVariants((string) $article->cover_path);
            }

            $stored = $mediaVariantService->storeUploadedFile(
                $coverFile,
                "images/articles/{$article->slug}",
                Article::class,
                $article->id,
            );
            $article->update(['cover_path' => $stored['source_path']]);
        }

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Статья обновлена.');
    }

    public function destroy(Article $article, MediaVariantService $mediaVariantService): RedirectResponse
    {
        if (filled($article->cover_path)) {
            Storage::disk('public')->delete((string) $article->cover_path);
            $mediaVariantService->deleteVariants((string) $article->cover_path);
        }

        Article::query()
            ->whereKey($article->id)
            ->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Статья удалена.');
    }
}
