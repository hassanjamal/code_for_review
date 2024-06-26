<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait CanBeUploaded
{
    public static function getUploadFolderName()
    {
        return self::$uploadFolderName;
    }

    public static function getUploadFolderPathWithTenantPrefix($tenantId)
    {
        return sprintf('tenant_%s/%s/', $tenantId, self::getUploadFolderName());
    }

    public function getSignedUrlAttribute()
    {
        return Storage::temporaryUrl($this->getFilePath(), now()->addMinutes(60));
    }

    public function getSignedDownloadUrlAttribute()
    {
        // Returns a URL that is a download response for the end user. When clicked the file will
        // download from the browser. Not used for text type documents as they will always be printed.
        return Storage::temporaryUrl($this->getFilePath(), now()->addMinutes(60),
            [
                'ResponseContentType' => $this->content_type,
                'ResponseContentDisposition' =>  "attachment;filename=".$this->original_name,
            ]);
    }

    private function getFilePath()
    {
        return sprintf("%s/%s", self::getUploadFolderName(), $this->key);
    }
}
