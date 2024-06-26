<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasName
{
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getInitialsAttribute()
    {
        return sprintf("%s%s", Str::substr($this->first_name, 0, 1), Str::substr($this->last_name, 0, 1));
    }

    public function scopeByName($query, $search)
    {
        return $query->where(\DB::raw("CONCAT_WS(' ',first_name,last_name)"), 'like', "%" . $search . "%");
    }
}
