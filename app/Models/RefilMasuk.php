<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RefilMasuk extends Model
{
    use HasFactory;

    protected $table = 'refil_masuk';

    protected $fillable = [
        'tanggal_masuk',
        'nama_pelanggan',
        'no_telepon',
        'jenis_layanan',
        'jenis_kartrid',
        'alamat',
        'kerusakan',
        'foto_kartrid',
        'diambil_oleh',
        'status',
        'verifikasi',
        'user_id',
        'ditangani_oleh',
        'sparepart',
        'tanggal_selesai'
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'verifikasi' => 'boolean'
    ];

    protected $appends = ['foto_url', 'status_badge'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function penangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto_kartrid) {
            return null;
        }
        
        return Storage::disk('public')->exists('refil-images/'.$this->foto_kartrid)
            ? asset('storage/refil-images/'.$this->foto_kartrid)
            : asset('images/default-image.jpg');
    }

    public function getStatusBadgeAttribute(): string
    {
        $status = $this->status;
        $badgeClass = [
            'Menunggu' => 'bg-warning-subtle text-warning',
            'Diproses' => 'bg-info-subtle text-info',
            'Selesai' => 'bg-success-subtle text-success'
        ][$status] ?? 'bg-secondary-subtle text-secondary';

        return '<span class="badge '.$badgeClass.' rounded-pill py-2 px-3">'.$status.'</span>';
    }
}