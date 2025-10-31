<?php

namespace App\Scout\Engines;

use Illuminate\Support\Str;
use Laravel\Scout\Engines\DatabaseEngine as BaseDatabaseEngine;

class CustomDatabaseEngine extends BaseDatabaseEngine
{
    public function update($models)
    {
        $models->each(function ($model) {
            $searchableAttributes = collect($model->toSearchableArray())->mapWithKeys(fn ($value, $key) => [Str::snake($key) => $value]);
            $fillableAttributes = collect($model->getFillable());
            $attributes = $searchableAttributes->filter(fn ($value, $key) => $fillableAttributes->contains($key))->all();

            if (! empty($attributes)) {
                $model->newModelQuery()->whereKey($model->getKey())->update($attributes);
            }
        });
    }

    public function delete($models)
    {
        parent::delete($models);
    }
}
