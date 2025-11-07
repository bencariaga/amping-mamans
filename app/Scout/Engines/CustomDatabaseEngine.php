<?php

namespace App\Scout\Engines;

use Illuminate\Support\Str;
use Laravel\Scout\Engines\DatabaseEngine as BaseDatabaseEngine;

class CustomDatabaseEngine extends BaseDatabaseEngine
{
    public function update($models)
    {
        $models->each(function ($model) {
            $attributes = collect($model->toSearchableArray())
                ->mapWithKeys(fn($value, $key) => [Str::snake($key) => $value])
                ->all();

            $model->newModelQuery()
                ->whereKey($model->getKey())
                ->update($attributes);
        });
    }

    public function delete($models)
    {
        parent::delete($models);
    }
}
