<?php

namespace App\Traits;

trait HasMeta
{
    public function getMetaAttribute($value)
    {
        // Return a null meta attribute as an empty array.
        // This prevents having to do data_get($this, 'meta', []); everywhere we want meta.
        // This overrides the $cast property for this column on the parent model.
        return json_decode($value, true) ?? [];
    }

    public function putMeta($key, $value = null)
    {
        if (is_array($key)) { // ['code' => 'foo', 'link' => 'bar']
            foreach ($key as $index => $value) {
                data_set($this, 'meta', $key);
            }

            $this->save();

            return;
        }

        $this->update(['meta' => array_merge($this->meta, [$key => $value])]);
    }

    public function getMeta($key = null, $default = null)
    {
        if ($key) {
            return data_get($this, "meta.{$key}", $default);
        }

        return data_get($this, 'meta', $default);
    }
}
