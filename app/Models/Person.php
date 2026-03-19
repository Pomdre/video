<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'people';

    protected $fillable = ['name', 'face_encoding', 'thumbnail'];

    public function files()
    {
        return $this->belongsToMany(\App\Models\File::class, 'file_person', 'person_id', 'file_id');
    }
}
