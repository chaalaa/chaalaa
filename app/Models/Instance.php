<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getDirectoryAttribute()
    {
        return config('app.root').'/instances/'.$this->name;
    }
}
