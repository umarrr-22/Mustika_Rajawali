<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceMasuk extends Model
{
    use HasFactory;

    protected $table = 'service_masuk';

    protected $fillable = [
        'tanggal_masuk',
        'nama_pelanggan',
        'no_telepon',
        'alamat',
        'jenis_layanan',
        'jenis_barang',
        'kerusakan',
        'sparepart_diganti',
        'tanggal_selesai',
        'keterangan',
        'foto_barang',
        'diambil_oleh',
        'status',
        'user_id',
        'teknisi_id',
        'alasan_penghapusan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_selesai' => 'datetime'
    ];
    
    // Service Types
    const TYPE_SERVICE = 'Service';
    const TYPE_KOMPLAIN = 'Komplain';

    // Status Values
    const STATUS_MENUNGGU = 'Menunggu';
    const STATUS_DIPROSES = 'Diproses';
    const STATUS_SELESAI = 'Selesai';
    const STATUS_BATAL = 'Batal';

    // Scopes
    public function scopeMenunggu($query)
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    public function scopeDiproses($query)
    {
        return $query->where('status', self::STATUS_DIPROSES);
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    public function scopeBatal($query)
    {
        return $query->where('status', self::STATUS_BATAL);
    }

    public function scopeService($query)
    {
        return $query->where('jenis_layanan', self::TYPE_SERVICE);
    }

    public function scopeKomplain($query)
    {
        return $query->where('jenis_layanan', self::TYPE_KOMPLAIN);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    // Helpers
    public function isService()
    {
        return $this->jenis_layanan === self::TYPE_SERVICE;
    }

    public function isKomplain()
    {
        return $this->jenis_layanan === self::TYPE_KOMPLAIN;
    }

    /**
     * Get status badge with appropriate colors
     * - Menunggu: Gray (secondary)
     * - Diproses: Yellow (warning)
     * - Selesai: Green (success)
     * - Batal: Red (danger)
     */
    public function getStatusBadgeAttribute()
    {
        $statusBadge = [
            self::STATUS_MENUNGGU => [
                'class' => 'bg-secondary bg-opacity-10 text-secondary',
                'icon' => 'fas fa-clock'
            ],
            self::STATUS_DIPROSES => [
                'class' => 'bg-warning bg-opacity-10 text-warning',
                'icon' => 'fas fa-cogs'
            ],
            self::STATUS_SELESAI => [
                'class' => 'bg-success bg-opacity-10 text-success',
                'icon' => 'fas fa-check-circle'
            ],
            self::STATUS_BATAL => [
                'class' => 'bg-danger bg-opacity-10 text-danger',
                'icon' => 'fas fa-times-circle'
            ]
        ];

        $currentStatus = $statusBadge[$this->status] ?? [
            'class' => 'bg-secondary bg-opacity-10 text-secondary',
            'icon' => 'fas fa-question-circle'
        ];

        return sprintf(
            '<span class="badge rounded-pill %s"><i class="%s me-1"></i>%s</span>',
            $currentStatus['class'],
            $currentStatus['icon'],
            $this->status
        );
    }

    /**
     * Get service type badge
     * - Service: Blue (primary)
     * - Komplain: Red (danger)
     */
    public function getLayananBadgeAttribute()
    {
        $type = $this->isService() ? [
            'class' => 'bg-primary bg-opacity-10 text-primary',
            'icon' => 'fas fa-tools'
        ] : [
            'class' => 'bg-danger bg-opacity-10 text-danger',
            'icon' => 'fas fa-exclamation-triangle'
        ];

        return sprintf(
            '<span class="badge rounded-pill %s"><i class="%s me-1"></i>%s</span>',
            $type['class'],
            $type['icon'],
            $this->jenis_layanan
        );
    }

    /**
     * Get formatted entry date
     */
    public function getTanggalMasukFormattedAttribute()
    {
        return $this->tanggal_masuk->format('d/m/Y');
    }

    /**
     * Get formatted completion date if exists
     */
    public function getTanggalSelesaiFormattedAttribute()
    {
        return $this->tanggal_selesai ? $this->tanggal_selesai->format('d/m/Y') : '-';
    }
}