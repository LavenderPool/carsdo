<?php

namespace Tests\Feature\Site;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPagesTest extends TestCase
{
    use RefreshDatabase;

    private function articleDocument(): array
    {
        return [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 2],
                    'content' => [
                        ['type' => 'text', 'text' => 'Структурированный заголовок'],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Текст из JSON документа'],
                    ],
                ],
                [
                    'type' => 'taskList',
                    'content' => [
                        [
                            'type' => 'taskItem',
                            'attrs' => ['checked' => true],
                            'content' => [
                                [
                                    'type' => 'paragraph',
                                    'content' => [
                                        ['type' => 'text', 'text' => 'Чеклист'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function test_blog_list_shows_only_published_articles(): void
    {
        Article::query()->create([
            'title' => 'Опубликованная статья',
            'slug' => 'published-article',
            'body' => '<p>Published</p>',
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        Article::query()->create([
            'title' => 'Черновик',
            'slug' => 'draft-article',
            'body' => '<p>Draft</p>',
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('blog.index'));

        $response->assertOk();
        $response->assertSee('Опубликованная статья');
        $response->assertDontSee('Черновик');
    }

    public function test_blog_show_returns_404_for_draft_article(): void
    {
        $article = Article::query()->create([
            'title' => 'Черновик',
            'slug' => 'draft-article',
            'body' => '<p>Draft</p>',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->get(route('blog.show', $article))
            ->assertNotFound();
    }

    public function test_blog_show_renders_structured_article_body_from_json(): void
    {
        $article = Article::query()->create([
            'title' => 'Статья из JSON',
            'slug' => 'json-article',
            'body' => '<p>Legacy fallback</p>',
            'body_json' => $this->articleDocument(),
            'is_published' => true,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('blog.show', $article));

        $response->assertOk();
        $response->assertSee('Структурированный заголовок');
        $response->assertSee('Текст из JSON документа');
        $response->assertSee('article-task-list', false);
        $response->assertDontSee('Legacy fallback');
    }

    public function test_blog_show_falls_back_to_legacy_html_when_json_is_missing(): void
    {
        $article = Article::query()->create([
            'title' => 'Legacy article',
            'slug' => 'legacy-article',
            'body' => '<p>Старый HTML контент</p>',
            'is_published' => true,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('blog.show', $article));

        $response->assertOk();
        $response->assertSee('Старый HTML контент');
    }
}
