<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JadwalKurir extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kurir';
    
    protected $fillable = [
        'tanggal',
        'daerah',
        'lokasi_tujuan',
        'alamat',
        'keperluan',
        'status',
        'tanggal_kirim',
        'completed_at',
        'catatan',
        'user_id',
        'kurir_id',
        'is_duplicated'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_kirim' => 'datetime',
        'completed_at' => 'datetime',
        'is_duplicated' => 'boolean'
    ];

    protected $appends = [
        'formatted_tanggal',
        'formatted_completed_at',
        'status_badge',
        'daerah_badge'
    ];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => 'User Tidak Ditemukan'
        ]);
    }

    public function kurir()
    {
        return $this->belongsTo(User::class, 'kurir_id')->withDefault([
            'name' => 'Kurir Tidak Ditemukan'
        ]);
    }

    public function scopeDikirim($query)
    {
        return $query->where('status', 'dikirim');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeDariKurir($query, $kurirId)
    {
        return $query->where('kurir_id', $kurirId);
    }

    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal ? $this->tanggal->format('d M Y') : null;
    }

    public function getFormattedCompletedAtAttribute()
    {
        return $this->completed_at ? $this->completed_at->format('d M Y H:i') : null;
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'draft' => ['badge' => 'secondary', 'text' => 'Draft'],
            'dikirim' => ['badge' => 'primary', 'text' => 'Dikirim'],
            'selesai' => ['badge' => 'success', 'text' => 'Selesai']
        ];

        $status = $statuses[$this->status] ?? ['badge' => 'dark', 'text' => $this->status];

        return '<span class="badge bg-'.$status['badge'].'">'.$status['text'].'</span>';
    }

    public function getDaerahBadgeAttribute()
    {
        $daerahColors = [
            'Semarang Barat' => 'danger',
            'Semarang Timur' => 'primary',
            'Semarang Kota' => 'success',
            'Ungaran' => 'warning'
        ];

        $color = $daerahColors[$this->daerah] ?? 'secondary';

        return '<span class="badge bg-'.$color.'">'.$this->daerah.'</span>';
    }

    public function markAsCompleted($kurirId, $catatan = null)
    {
        $this->update([
            'status' => 'selesai',
            'completed_at' => now(),
            'kurir_id' => $kurirId,
            'catatan' => $catatan
        ]);
        
        return $this;
    }

    public function duplicateToAdmin($catatan = null)
    {
        $newJadwal = $this->replicate();
        $newJadwal->status = 'draft';
        $newJadwal->tanggal_kirim = null;
        $newJadwal->completed_at = null;
        $newJadwal->kurir_id = null;
        $newJadwal->catatan = $catatan;
        $newJadwal->is_duplicated = true;
        $newJadwal->save();

        return $newJadwal;
    }
}