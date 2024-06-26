<?php

namespace App;

use App\Traits\HasCompositeKey;
use Illuminate\Database\Eloquent\Model;

class ApiModel extends Model
{
    use HasCompositeKey;

    public $incrementing = false;

    protected $keyType = 'string';
}
