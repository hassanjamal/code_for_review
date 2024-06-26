<?php

namespace App;

use App\Traits\CanBeUploaded;
use Illuminate\Database\Eloquent\Model;

class ManagedImage extends Model
{
    use CanBeUploaded;

    protected $guarded = [];

    protected static $uploadFolderName= 'image-manager';

    protected $appends = [
        'signed_url',
        'signed_download_url',
    ];
}
