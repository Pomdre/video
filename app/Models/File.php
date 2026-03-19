<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = ['basename', 'extension', 'filename', 'gif', 'votes'];

    public function people()
    {
        return $this->belongsToMany(\App\Models\Person::class, 'file_person', 'file_id', 'person_id');
    }
}
