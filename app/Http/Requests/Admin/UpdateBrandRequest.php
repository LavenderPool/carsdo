<?php

namespace App\Http\Requests\Admin;

use App\Models\Brand;
use App\Support\Seo\AdminSeoFields;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $name = (string) $this->input('name', '');
        $slug = (string) $this->input('slug', '');
        $leaveFromRussian = $this->boolean('leave_from_russian');

        $data = [
            'name' => trim($name),
            'slug' => trim($slug) !== '' ? Str::slug($slug) : Str::slug($name),
            'leave_from_russian' => $leaveFromRussian,
        ];

        foreach (AdminSeoFields::brandFields() as $field) {
            $data[$field] = $this->normalizeSeoField($field);
        }

        $this->merge($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Brand $brand */
        $brand = $this->route('brand');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'slug')->ignore($brand->id),
            ],
            'leave_from_russian' => ['boolean'],
        ];

        foreach (AdminSeoFields::brandFields() as $field) {
            $rules[$field] = $this->seoFieldRules($field);
        }

        return $rules;
    }

    /**
     * Get custom validation messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge([
            'name.required' => 'Укажите название бренда.',
            'name.max' => 'Название бренда не должно превышать 255 символов.',
            'slug.required' => 'Укажите идентификатор бренда.',
            'slug.max' => 'Идентификатор бренда не должен превышать 255 символов.',
            'slug.unique' => 'Бренд с таким идентификатором уже существует.',
        ], $this->seoMessages());
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

    /**
     * @return array<string, string>
     */
    private function seoMessages(): array
    {
        return [
            'seo_title.max' => 'SEO title не должен превышать 255 символов.',
            'seo_h1.max' => 'SEO H1 не должен превышать 255 символов.',
            'seo_canonical_url.max' => 'SEO canonical не должен превышать 2048 символов.',
            'seo_og_image.max' => 'SEO og:image не должен превышать 2048 символов.',
            'seo_robots.max' => 'SEO robots не должен превышать 255 символов.',
        ];
    }
}
