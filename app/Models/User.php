<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relasi ke jadwal dimana user sebagai kurir
    public function jadwalKurir()
    {
        return $this->hasMany(JadwalKurir::class, 'kurir_id');
    }

    // Relasi ke jadwal dimana user sebagai pengirim
    public function jadwalPengirim()
    {
        return $this->hasMany(JadwalKurir::class, 'user_id');
    }

    // Relasi untuk refil yang ditangani oleh teknisi
    public function refilDitangani()
    {
        return $this->hasMany(RefilMasuk::class, 'ditangani_oleh');
    }

    // Relasi untuk service masuk yang ditangani oleh teknisi
    public function serviceMasukDitangani()
    {
        return $this->hasMany(ServiceMasuk::class, 'teknisi_id');
    }

    // Relasi untuk service proses yang ditangani oleh teknisi
    public function serviceProsesDitangani()
    {
        return $this->hasMany(ServiceMasuk::class, 'teknisi_id')->where('status', 'Diproses');
    }

    // Relasi untuk service selesai yang ditangani oleh teknisi
    public function serviceSelesaiDitangani()
    {
        return $this->hasMany(ServiceMasuk::class, 'teknisi_id')->where('status', 'Selesai');
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function isTeknisi(): bool
    {
        return $this->role && $this->role->name === 'teknisi';
    }

    public function isKurir(): bool
    {
        return $this->role && $this->role->name === 'kurir';
    }

    public function isRefil(): bool
    {
        return $this->role && $this->role->name === 'refil';
    }

    // Scope untuk mendapatkan hanya teknisi
    public function scopeTeknisi($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'teknisi');
        });
    }
}