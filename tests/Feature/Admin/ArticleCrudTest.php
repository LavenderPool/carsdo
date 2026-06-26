<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleCrudTest extends TestCase
{
    use RefreshDatabase;

    private function articleDocument(string $title, string $body, bool $checked = false): string
    {
        return json_encode([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 2],
                    'content' => [
                        ['type' => 'text', 'text' => $title],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => $body],
                    ],
                ],
                [
                    'type' => 'taskList',
                    'content' => [
                        [
                            'type' => 'taskItem',
                            'attrs' => ['checked' => $checked],
                            'content' => [
                                [
                                    'type' => 'paragraph',
                                    'content' => [
                                        ['type' => 'text', 'text' => 'Пункт чеклиста'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }

    public function test_guest_is_redirected_to_login_from_admin_articles(): void
    {
        $response = $this->get(route('admin.articles.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manage_articles(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.articles.store'), [
                'title' => 'Первый тест статьи',
                'slug' => '',
                'excerpt' => 'Краткое описание новости',
                'body_json' => $this->articleDocument('Заголовок блока', 'Контент статьи'),
                'is_published' => true,
                'published_at' => now()->toDateTimeString(),
                'seo_title' => '{article} title',
                'seo_description' => 'SEO description',
                'seo_h1' => 'SEO H1',
                'seo_og_image' => '/images/article.jpg',
                'seo_canonical_url' => '/blog/custom/',
                'seo_robots' => 'index, follow',
            ])
            ->assertRedirect(route('admin.articles.index'));

        $article = Article::query()->firstOrFail();

        $this->assertSame('Первый тест статьи', $article->title);
        $this->assertSame('pervyi-test-stati', $article->slug);
        $this->assertTrue($article->is_published);
        $this->assertSame('{article} title', $article->seo_title);
        $this->assertIsArray($article->body_json);
        $this->assertStringContainsString('<h2>Заголовок блока</h2>', $article->body);
        $this->assertStringContainsString('article-task-list', $article->body);

        $this->actingAs($user)
            ->put(route('admin.articles.update', $article), [
                'title' => 'Обновленная статья',
                'slug' => 'updated-article',
                'excerpt' => 'Новое краткое описание',
                'body_json' => $this->articleDocument('Новый контент', 'Обновленный абзац', true),
                'is_published' => false,
                'published_at' => null,
                'seo_title' => 'Updated title',
                'seo_description' => 'Updated SEO',
                'seo_h1' => 'Updated H1',
                'seo_og_image' => '/images/updated.jpg',
                'seo_canonical_url' => '/blog/updated/',
                'seo_robots' => 'noindex, nofollow',
            ])
            ->assertRedirect(route('admin.articles.index'));

        $article->refresh();

        $this->assertSame('Обновленная статья', $article->title);
        $this->assertSame('updated-article', $article->slug);
        $this->assertFalse($article->is_published);
        $this->assertSame('Updated title', $article->seo_title);
        $this->assertSame('noindex, nofollow', $article->seo_robots);
        $this->assertStringContainsString('<h2>Новый контент</h2>', $article->body);
        $this->assertStringContainsString('checked', $article->body);

        $this->actingAs($user)
            ->delete(route('admin.articles.destroy', $article))
            ->assertRedirect(route('admin.articles.index'));

        $this->assertSoftDeleted($article);
    }
}
