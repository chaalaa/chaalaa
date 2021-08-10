<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function instances()
    {
        return $this->hasMany(Instance::class);
    }

    public function instance()
    {
        return $this->hasOne(Instance::class)->ofMany();
    }
}
