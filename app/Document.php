<?php

namespace App;

use App\Traits\CanBeUploaded;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Document extends Model
{
    use LogsActivity, CanBeUploaded;

    protected $guarded = [];

    protected static $logOnlyDirty = true;

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logName = 'documents';

    protected static $uploadFolderName = 'documents';

    protected $appends = [
        'signed_url',
        'signed_download_url',
    ];

    /**
 * Custom Functions
 */

    /**
     * Accessors & Mutators
     */

    /**
     * Relationships
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Query Scopes
     */
}
