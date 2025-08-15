<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Almarhum extends Model
{
    protected $fillable = ['nama', 'tanggal_lahir', 'tanggal_wafat', 'blok_makam', 'nomor_makam', 'foto', 'riwayat'];
    
    protected $casts = [
    'tanggal_lahir' => 'date:Y-m-d',  // Format eksplisit
    'tanggal_wafat' => 'date:Y-m-d'
    ];

    // Hitung usia otomatis
    public function getUsiaAttribute()
    {
        if (!$this->tanggal_lahir || !$this->tanggal_wafat) {
            return null;
        }
        
        // Hitung selisih dengan cara lebih robust
        $diff = $this->tanggal_lahir->diff($this->tanggal_wafat);
        return $diff->y; // Ambil tahunnya saja
    }

    // Format tanggal Indo (17-08-1945)
    public function getTanggalIndoAttribute()
    {
        return $this->tanggal_lahir?->format('d-m-Y');
    }

    // Bonus: Untuk menampilkan usia lengkap (tahun + bulan)
    public function getUsiaLengkapAttribute()
    {
        if (!$this->tanggal_lahir || !$this->tanggal_wafat) {
            return '-';
        }
        
        $diff = $this->tanggal_lahir->diff($this->tanggal_wafat);
        return $diff->y . ' tahun ' . $diff->m . ' bulan';
    }
    public function keluargas()
    {
        return $this->hasMany(\App\Models\Keluarga::class);
    }
}