<?php

namespace App\Models;

use App\Observers\PublicContentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([PublicContentObserver::class])]
#[Fillable(['name', 'slug'])]
class City extends Model
{
    public function nameInPrepositionalCase(): string
    {
        $cityName = trim((string) $this->name);
        if ($cityName === '') {
            return $cityName;
        }

        $parts = preg_split('/\s+/u', $cityName);
        if (! is_array($parts) || $parts === []) {
            return $cityName;
        }

        $lastPartIndex = array_key_last($parts);
        if ($lastPartIndex === null) {
            return $cityName;
        }

        $parts[$lastPartIndex] = $this->inflectWordToPrepositional((string) $parts[$lastPartIndex]);

        return implode(' ', $parts);
    }

    private function inflectWordToPrepositional(string $word): string
    {
        $word = trim($word);
        if ($word === '' || preg_match('/\p{Cyrillic}/u', $word) !== 1) {
            return $word;
        }

        $wordLower = mb_strtolower($word, 'UTF-8');
        $exceptions = [
            'новосибирск' => 'новосибирске',
        ];

        if (isset($exceptions[$wordLower])) {
            return $this->applyOriginalCasing($word, $exceptions[$wordLower]);
        }

        $inflected = match (true) {
            str_ends_with($wordLower, 'ь') => mb_substr($wordLower, 0, -1, 'UTF-8').'и',
            str_ends_with($wordLower, 'й') => mb_substr($wordLower, 0, -1, 'UTF-8').'е',
            str_ends_with($wordLower, 'ия') => mb_substr($wordLower, 0, -2, 'UTF-8').'ии',
            str_ends_with($wordLower, 'а') => mb_substr($wordLower, 0, -1, 'UTF-8').'е',
            str_ends_with($wordLower, 'я') => mb_substr($wordLower, 0, -1, 'UTF-8').'е',
            preg_match('/[бвгджзйклмнпрстфхцчшщ]$/u', $wordLower) === 1 => $wordLower.'е',
            default => $wordLower,
        };

        return $this->applyOriginalCasing($word, $inflected);
    }

    private function applyOriginalCasing(string $original, string $inflected): string
    {
        if ($original === mb_strtoupper($original, 'UTF-8')) {
            return mb_strtoupper($inflected, 'UTF-8');
        }

        $firstLetter = mb_substr($original, 0, 1, 'UTF-8');
        if ($firstLetter === mb_strtoupper($firstLetter, 'UTF-8')) {
            return mb_convert_case($inflected, MB_CASE_TITLE, 'UTF-8');
        }

        return $inflected;
    }

    public function carDealers(): HasMany
    {
        return $this->hasMany(CarDealer::class);
    }
}
