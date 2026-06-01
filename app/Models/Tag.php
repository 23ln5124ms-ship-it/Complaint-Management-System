<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_tag');
    }
}
