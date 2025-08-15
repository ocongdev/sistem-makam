<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    protected $fillable = ['almarhum_id', 'nama', 'hubungan', 'telepon', 'alamat'];

    public function almarhum()
    {
        return $this->belongsTo(Almarhum::class);
    }
}