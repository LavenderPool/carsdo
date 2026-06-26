<?php

namespace App\Http\Requests\Admin;

use App\Support\Articles\ArticleBodyRenderer;
use App\Support\Seo\AdminSeoFields;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $title = (string) $this->input('title', '');
        $slug = (string) $this->input('slug', '');
        $isPublished = $this->boolean('is_published');
        $publishedAt = $this->input('published_at');
        $excerpt = $this->input('excerpt');
        $bodyJson = $this->decodeBodyJson($this->input('body_json'));
        $renderer = app(ArticleBodyRenderer::class);

        $data = [
            'title' => trim($title),
            'slug' => trim($slug) !== '' ? Str::slug($slug) : Str::slug($title),
            'excerpt' => is_string($excerpt) && trim($excerpt) !== '' ? trim($excerpt) : null,
            'body_json' => $bodyJson,
            'body' => trim($renderer->render($bodyJson)),
            'is_published' => $isPublished,
            'published_at' => $isPublished
                ? ($publishedAt ?: now()->toDateTimeString())
                : (filled($publishedAt) ? $publishedAt : null),
            'sort_order' => max(0, (int) $this->integer('sort_order')),
            '_body_json_is_invalid' => $this->input('body_json') !== null && $this->input('body_json') !== '' && $bodyJson === null,
        ];

        foreach (AdminSeoFields::pageFields() as $field) {
            $data[$field] = $this->normalizeSeoField($field);
        }

        $this->merge($data);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('pages', 'slug')],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body_json' => [
                'required',
                'array',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->boolean('_body_json_is_invalid')) {
                        $fail('Содержимое страницы повреждено. Откройте редактор заново и повторите сохранение.');

                        return;
                    }

                    if (! app(ArticleBodyRenderer::class)->hasMeaningfulContent(is_array($value) ? $value : null)) {
                        $fail('Добавьте текст страницы.');
                    }
                },
            ],
            'body' => ['required', 'string'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        foreach (AdminSeoFields::pageFields() as $field) {
            $rules[$field] = $this->seoFieldRules($field);
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Укажите заголовок страницы.',
            'slug.required' => 'Укажите идентификатор страницы.',
            'slug.unique' => 'Страница с таким идентификатором уже существует.',
            'body_json.required' => 'Добавьте текст страницы.',
            'published_at.date' => 'Дата публикации указана некорректно.',
            'sort_order.integer' => 'Порядок сортировки должен быть числом.',
            'sort_order.min' => 'Порядок сортировки не может быть отрицательным.',
        ];
    }

    /**
     * @return array<int, ValidationRule|string>
     */
    private function seoFieldRules(string $field): array
    {
        return match (true) {
            str_ends_with($field, '_description') => ['nullable', 'string', 'max:5000'],
            str_ends_with($field, '_canonical_url'), str_ends_with($field, '_og_image') => ['nullable', 'string', 'max:2048'],
            default => ['nullable', 'string', 'max:255'],
        };
    }

    private function normalizeSeoField(string $field): ?string
    {
        $value = $this->input($field);

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    private function decodeBodyJson(mixed $value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }
}
