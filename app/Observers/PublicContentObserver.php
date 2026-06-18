<?php

namespace App\Observers;

use App\Support\Cache\SiteCache;
use Illuminate\Database\Eloquent\Model;

class PublicContentObserver
{
    public function saved(Model $model): void
    {
        if ($this->onlyViewsCountChanged($model)) {
            return;
        }

        SiteCache::flush();
    }

    public function deleted(Model $model): void
    {
        SiteCache::flush();
    }

    private function onlyViewsCountChanged(Model $model): bool
    {
        $changed = $model->getChanges();

        if ($changed === []) {
            return false;
        }

        return array_keys($changed) === ['views_count'];
    }
}
