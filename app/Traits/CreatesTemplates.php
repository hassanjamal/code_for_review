<?php

namespace App\Traits;

use App\Template;

trait CreatesTemplates
{
    public function createdTemplates()
    {
        return $this->morphMany(Template::class, 'creator', 'creator_type', 'creator_id', 'id');
    }
}
