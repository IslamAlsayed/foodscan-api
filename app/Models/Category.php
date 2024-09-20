<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "image",
        "status",
    ];

    public function meal()
    {
        return $this->hasMany(Meal::class);
    }

    public function addon()
    {
        return $this->hasMany(Addon::class);
    }

    public function extra()
    {
        return $this->hasMany(Extra::class);
    }
}
