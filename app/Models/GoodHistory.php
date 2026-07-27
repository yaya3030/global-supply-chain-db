<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodHistory extends Model
{
    use HasFactory;

    protected $table = 'goods_history';
    protected $fillable = ['good_id', 'port_id', 'status', 'arrival_time', 'departure_time'];

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function port()
    {
        return $this->belongsTo(Port::class);
    }
}
