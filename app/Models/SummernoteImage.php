<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SummernoteImage extends Model implements hasMedia
{
    use InteractsWithMedia;

     // Automatically create a UUID for new records
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // This line creates a new file in the WebP format.
        $this->addMediaConversion('webp')
             ->format('webp')
             ->nonQueued();
    }
}
