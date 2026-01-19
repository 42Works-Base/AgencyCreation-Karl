<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $fillable = [
        'name',
        'subdomain',
        'prefix',
        'type',
        'logo_path',
        'email_logo_path',
        'background_image_path',
        'skin_color',
    ];

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }
}
