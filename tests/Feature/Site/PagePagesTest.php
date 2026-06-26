<?php

namespace Tests\Feature\Site;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagePagesTest extends TestCase
{
    use RefreshDatabase;

    private function pageDocument(): array
    {
        return [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 2],
                    'content' => [
                        ['type' => 'text', 'text' => 'Структурированный заголовок страницы'],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Текст обычной страницы'],
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
                                        ['type' => 'text', 'text' => 'Чеклист страницы'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function test_generic_page_route_renders_published_page(): void
    {
        $page = Page::query()->create([
            'title' => 'О компании',
            'slug' => 'about-company',
            'body' => '<p>Legacy fallback</p>',
            'body_json' => $this->pageDocument(),
            'is_published' => true,
            'published_at' => now()->subMinute(),
            'sort_order' => 100,
        ]);

        $response = $this->get(route('pages.show', ['slug' => $page->slug]));

        $response->assertOk();
        $response->assertSee('Структурированный заголовок страницы');
        $response->assertSee('Текст обычной страницы');
        $response->assertSee('article-task-list', false);
        $response->assertDontSee('Legacy fallback');
    }

    public function test_generic_page_route_returns_404_for_draft_page(): void
    {
        $page = Page::query()->create([
            'title' => 'Черновик страницы',
            'slug' => 'draft-page',
            'body' => '<p>Draft</p>',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->get(route('pages.show', ['slug' => $page->slug]))
            ->assertNotFound();
    }

    public function test_seeded_privacy_page_is_reachable_when_published(): void
    {
        $page = Page::query()->where('slug', 'privacy-policy')->firstOrFail();

        $page->update([
            'body' => '<p>Политика конфиденциальности</p>',
            'body_json' => $this->pageDocument(),
            'is_published' => true,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('pages.privacy'));

        $response->assertOk();
        $response->assertSee('Структурированный заголовок страницы');
    }

    public function test_seeded_cookie_and_contacts_pages_exist_for_admin_bootstrap(): void
    {
        $this->assertDatabaseHas('pages', ['slug' => 'cookie-policy']);
        $this->assertDatabaseHas('pages', ['slug' => 'contacts']);
    }
}
