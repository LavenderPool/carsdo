<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\Articles\ArticleBodyRenderer;
use App\Support\Cache\SiteCache;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function show(string $slug, ArticleBodyRenderer $articleBodyRenderer): View
    {
        return $this->renderPage($slug, $articleBodyRenderer);
    }

    public function privacy(ArticleBodyRenderer $articleBodyRenderer): View
    {
        return $this->renderPage('privacy-policy', $articleBodyRenderer);
    }

    public function cookie(ArticleBodyRenderer $articleBodyRenderer): View
    {
        return $this->renderPage('cookie-policy', $articleBodyRenderer);
    }

    public function contacts(ArticleBodyRenderer $articleBodyRenderer): View
    {
        return $this->renderPage('contacts', $articleBodyRenderer);
    }

    private function renderPage(string $slug, ArticleBodyRenderer $articleBodyRenderer): View
    {
        $page = SiteCache::remember($this->cacheKey($slug), static function () use ($slug) {
            $query = Page::query()->where('slug', $slug);

            if (! Page::isSystemSlug($slug)) {
                $query->published();
            }

            return $query->first();
        });

        abort_if(! $page instanceof Page, 404);

        return view('site.pages.show', [
            'page' => $page,
            'pageBodyHtml' => $page->body_json !== null
                ? $articleBodyRenderer->render($page->body_json)
                : (string) $page->body,
        ]);
    }

    private function cacheKey(string $slug): string
    {
        return Page::isSystemSlug($slug)
            ? "pages:system:{$slug}:v2"
            : "pages:published:{$slug}";
    }
}
