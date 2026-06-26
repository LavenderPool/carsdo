<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageCrudTest extends TestCase
{
    use RefreshDatabase;

    private function pageDocument(string $title, string $body, bool $checked = false): string
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
                                        ['type' => 'text', 'text' => 'Пункт страницы'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }

    public function test_guest_is_redirected_to_login_from_admin_pages(): void
    {
        $response = $this->get(route('admin.pages.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manage_pages(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.pages.store'), [
                'title' => 'Покупка автомобиля онлайн',
                'slug' => '',
                'excerpt' => 'Короткое описание страницы',
                'body_json' => $this->pageDocument('Заголовок страницы', 'Контент страницы'),
                'is_published' => true,
                'published_at' => now()->toDateTimeString(),
                'sort_order' => 5,
                'seo_title' => 'SEO title',
                'seo_description' => 'SEO description',
                'seo_h1' => 'SEO H1',
                'seo_og_image' => '/images/page.jpg',
                'seo_canonical_url' => '/pages/custom/',
                'seo_robots' => 'index, follow',
            ])
            ->assertRedirect(route('admin.pages.index'));

        $page = Page::query()
            ->where('title', 'Покупка автомобиля онлайн')
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('Покупка автомобиля онлайн', $page->title);
        $this->assertNotSame('', $page->slug);
        $this->assertTrue($page->is_published);
        $this->assertSame(5, $page->sort_order);
        $this->assertIsArray($page->body_json);
        $this->assertStringContainsString('<h2>Заголовок страницы</h2>', $page->body);
        $this->assertStringContainsString('article-task-list', $page->body);

        $this->actingAs($user)
            ->put(route('admin.pages.update', $page), [
                'title' => 'Обновленная страница',
                'slug' => 'updated-page',
                'excerpt' => 'Новое описание',
                'body_json' => $this->pageDocument('Новый контент', 'Обновленный текст', true),
                'is_published' => false,
                'published_at' => null,
                'sort_order' => 15,
                'seo_title' => 'Updated title',
                'seo_description' => 'Updated SEO',
                'seo_h1' => 'Updated H1',
                'seo_og_image' => '/images/updated.jpg',
                'seo_canonical_url' => '/pages/updated/',
                'seo_robots' => 'noindex, nofollow',
            ])
            ->assertRedirect(route('admin.pages.index'));

        $page->refresh();

        $this->assertSame('Обновленная страница', $page->title);
        $this->assertSame('updated-page', $page->slug);
        $this->assertFalse($page->is_published);
        $this->assertSame(15, $page->sort_order);
        $this->assertSame('noindex, nofollow', $page->seo_robots);
        $this->assertStringContainsString('<h2>Новый контент</h2>', $page->body);
        $this->assertStringContainsString('checked', $page->body);

        $this->actingAs($user)
            ->delete(route('admin.pages.destroy', $page))
            ->assertRedirect(route('admin.pages.index'));

        $this->assertSoftDeleted($page);
    }
}
