<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->json('body_json')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_h1')->nullable();
            $table->text('seo_og_image')->nullable();
            $table->text('seo_canonical_url')->nullable();
            $table->text('seo_robots')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'published_at']);
            $table->index(['sort_order', 'title']);
        });

        $now = now();

        DB::table('pages')->insert([
            [
                'title' => 'Политика конфиденциальности',
                'slug' => 'privacy-policy',
                'excerpt' => 'Заполните политику конфиденциальности через админ-панель.',
                'body' => '<p>Заполните содержание страницы через админ-панель.</p>',
                'body_json' => json_encode([
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                ['type' => 'text', 'text' => 'Заполните содержание страницы через админ-панель.'],
                            ],
                        ],
                    ],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_published' => false,
                'published_at' => null,
                'sort_order' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Политика использования cookie',
                'slug' => 'cookie-policy',
                'excerpt' => 'Заполните политику использования cookie через админ-панель.',
                'body' => '<p>Заполните содержание страницы через админ-панель.</p>',
                'body_json' => json_encode([
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                ['type' => 'text', 'text' => 'Заполните содержание страницы через админ-панель.'],
                            ],
                        ],
                    ],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_published' => false,
                'published_at' => null,
                'sort_order' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Контакты',
                'slug' => 'contacts',
                'excerpt' => 'Заполните страницу контактов через админ-панель.',
                'body' => '<p>Заполните содержание страницы через админ-панель.</p>',
                'body_json' => json_encode([
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                ['type' => 'text', 'text' => 'Заполните содержание страницы через админ-панель.'],
                            ],
                        ],
                    ],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_published' => false,
                'published_at' => null,
                'sort_order' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
