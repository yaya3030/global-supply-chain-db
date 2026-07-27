<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tracking_number', 'status'];

    public function history()
    {
        return $this->hasMany(GoodHistory::class);
    }
}
