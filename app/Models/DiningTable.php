<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiningTable extends Model
{
    use HasFactory;

    protected $fillable = [
        "floor",
        "size",
        "status"
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
