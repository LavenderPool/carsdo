<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Support\Articles\ArticleBodyRenderer;
use App\Support\Cache\SiteCache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $page = max(1, (int) $request->integer('page', 1));

        $articles = SiteCache::remember("blog:page:{$page}", static fn () => Article::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12));

        return view('site.blog.index', [
            'articles' => $articles,
        ]);
    }

    public function show(Article $article, ArticleBodyRenderer $articleBodyRenderer): View
    {
        abort_unless(
            $article->is_published && $article->published_at !== null && $article->published_at->lessThanOrEqualTo(now()),
            404
        );

        Article::query()->whereKey($article->id)->increment('views_count');
        $article->refresh();

        return view('site.blog.show', [
            'article' => $article,
            'articleBodyHtml' => $article->body_json !== null
                ? $articleBodyRenderer->render($article->body_json)
                : (string) $article->body,
        ]);
    }
}
