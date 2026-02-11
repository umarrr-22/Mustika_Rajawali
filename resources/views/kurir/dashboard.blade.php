@extends('layouts.kurir')

@section('title', 'Dashboard Kurir')

@section('content')
<div class="container-fluid px-4">
    <!-- Header dengan animasi -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 animate__animated animate__fadeInDown">
        <div>
            <h2 class="mb-0 fw-bold text-gradient">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Kurir
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-day me-1"></i> 
                <span id="real-time-clock">{{ date('l, d F Y H:i:s') }}</span>
            </p>
        </div>
        <div class="mt-2 mt-md-0">
            <div class="user-profile-badge">
                <img src="{{ asset('images/default-avatar.png') }}" 
                     class="rounded-circle me-2" width="40" height="40" alt="User">
                <span class="fw-medium">Bagas Kurir</span>
            </div>
        </div>
    </div>

    <!-- Statistik Cards dengan Hover Effect -->
    <div class="row g-4 mb-4">
        <!-- Jadwal Hari Ini -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card stat-card bg-primary bg-opacity-10 border-0 h-100 hover-scale">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Jadwal Hari Ini</h6>
                            <h2 class="mb-0 fw-bold">{{ $todaySchedules }}</h2>
                            <small class="text-muted">Total: {{ $totalSchedules }} jadwal</small>
                        </div>
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-calendar-day fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: {{ $todaySchedules > 0 ? ($todaySchedules/$totalSchedules)*100 : 0 }}%" 
                             aria-valuenow="{{ $todaySchedules }}" 
                             aria-valuemin="0" 
                             aria-valuemax="{{ $totalSchedules }}">
                        </div>
                    </div>
                    <a href="{{ route('kurir.jadwal-hari-ini') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        
        <!-- Selesai Hari Ini -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card stat-card bg-success bg-opacity-10 border-0 h-100 hover-scale">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Selesai Hari Ini</h6>
                            <h2 class="mb-0 fw-bold">{{ $completedToday }}</h2>
                            <small class="text-muted">Total: {{ $totalCompleted }} selesai</small>
                        </div>
                        <div class="icon-circle bg-success text-white">
                            <i class="fas fa-check-circle fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $completedToday > 0 ? ($completedToday/$totalCompleted)*100 : 0 }}%" 
                             aria-valuenow="{{ $completedToday }}" 
                             aria-valuemin="0" 
                             aria-valuemax="{{ $totalCompleted }}">
                        </div>
                    </div>
                    <a href="{{ route('kurir.jadwal-selesai') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        
        <!-- Refil Draft -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card stat-card bg-warning bg-opacity-10 border-0 h-100 hover-scale">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Refil Draft</h6>
                            <h2 class="mb-0 fw-bold">{{ $pendingRefils }}</h2>
                            <small class="text-muted">Menunggu verifikasi</small>
                        </div>
                        <div class="icon-circle bg-warning text-white">
                            <i class="fas fa-tint fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: {{ $pendingRefils > 0 ? 100 : 0 }}%" 
                             aria-valuenow="{{ $pendingRefils }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <a href="{{ route('kurir.refil-masuk') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        
        <!-- Service Draft -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card stat-card bg-info bg-opacity-10 border-0 h-100 hover-scale">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2">Service Draft</h6>
                            <h2 class="mb-0 fw-bold">{{ $pendingServices }}</h2>
                            <small class="text-muted">Menunggu verifikasi</small>
                        </div>
                        <div class="icon-circle bg-info text-white">
                            <i class="fas fa-tools fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-info" role="progressbar" 
                             style="width: {{ $pendingServices > 0 ? 100 : 0 }}%" 
                             aria-valuenow="{{ $pendingServices }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <a href="{{ route('kurir.service-masuk') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Jadwal dalam Satu Baris -->
    <div class="row g-4 mb-4">
        <!-- Grafik -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Statistik Pengiriman
                    </h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="?filter=week" class="btn btn-outline-primary {{ $currentFilter === 'week' ? 'active' : '' }}">
                            Mingguan
                        </a>
                        <a href="?filter=month" class="btn btn-outline-primary {{ $currentFilter === 'month' ? 'active' : '' }}">
                            Bulanan
                        </a>
                        <a href="?filter=year" class="btn btn-outline-primary {{ $currentFilter === 'year' ? 'active' : '' }}">
                            Tahunan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="deliveryChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Cuaca dan Aktivitas -->
        <div class="col-lg-4">
            <div class="card weather-card bg-gradient border-0 text-white h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-1"><i class="fas fa-map-marker-alt me-1"></i> Lokasi Anda</h6>
                            <h5 class="mb-0 fw-bold">Semarang</h5>
                        </div>
                        <div class="weather-icon">
                            <i class="fas fa-sun fa-2x"></i>
                        </div>
                    </div>
                    <div class="text-center my-4">
                        <h1 class="display-4 fw-bold">28°C</h1>
                        <p class="mb-0">Cerah Berawan</p>
                    </div>
                    <div class="weather-details mt-auto">
                        <div class="row">
                            <div class="col-4 text-center">
                                <p class="mb-1"><i class="fas fa-wind"></i></p>
                                <p class="mb-0">12 km/h</p>
                            </div>
                            <div class="col-4 text-center">
                                <p class="mb-1"><i class="fas fa-tint"></i></p>
                                <p class="mb-0">65%</p>
                            </div>
                            <div class="col-4 text-center">
                                <p class="mb-1"><i class="fas fa-eye"></i></p>
                                <p class="mb-0">10 km</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-list-check me-2 text-success"></i>
                        Jadwal Hari Ini
                    </h6>
                    <span class="badge bg-success bg-opacity-10 text-success py-2">
                        Total: {{ $todaySchedules }} Jadwal
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($latestSchedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Pelanggan</th>
                                        <th>Lokasi</th>
                                        <th>Waktu</th>
                                        <th width="12%">Status</th>
                                        <th width="8%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latestSchedules as $schedule)
                                    <tr class="schedule-row" data-id="{{ $schedule->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('images/user-avatar.png') }}" 
                                                     class="rounded-circle me-2" width="30" height="30" alt="User">
                                                <span>User</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                                Semarang
                                            </span>
                                        </td>
                                        <td>{{ date('H:i', strtotime($schedule->tanggal)) }}</td>
                                        <td>
                                            @if($schedule->status == 'selesai' || $schedule->completed_at)
                                                <span class="badge bg-success rounded-pill px-2 py-1">Selesai</span>
                                            @else
                                                <span class="badge bg-primary rounded-pill px-2 py-1">Belum</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary view-detail" 
                                                    data-id="{{ $schedule->id }}">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <h5>Tidak ada jadwal hari ini</h5>
                            <p class="text-muted">Anda tidak memiliki jadwal pengiriman hari ini</p>
                            <a href="{{ route('kurir.jadwal-hari-ini') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Tambah Jadwal
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white text-end py-2">
                    <a href="{{ route('kurir.jadwal-hari-ini') }}" class="btn btn-sm btn-success">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Jadwal -->
<div class="modal fade" id="scheduleDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detail Pengiriman</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="scheduleDetailContent">
                <!-- Konten akan diisi via AJAX -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-2">Memuat detail pengiriman...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="markAsDoneBtn">
                    <i class="fas fa-check-circle me-1"></i> Tandai Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS Kustom -->
<style>
    /* Animasi */
    .animate__animated {
        animation-duration: 0.5s;
    }
    
    /* Gradient Text */
    .text-gradient {
        background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    
    /* Card Statistik */
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .stat-card .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Weather Card */
    .weather-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .weather-icon {
        background: rgba(255,255,255,0.2);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Table */
    .schedule-row:hover {
        background-color: rgba(13, 110, 253, 0.05) !important;
    }
    
    /* Empty State */
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    
    /* User Profile */
    .user-profile-badge {
        display: flex;
        align-items: center;
        background: rgba(13, 110, 253, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    
    .user-profile-badge:hover {
        background: rgba(13, 110, 253, 0.2);
    }
    
    /* Hover Scale Effect */
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>

<!-- JavaScript dan Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Fungsi untuk menampilkan notifikasi
function showAlert(icon, title, text) {
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        timer: 3000,
        showConfirmButton: false
    });
}

// Fungsi untuk memformat tanggal
function formatDate(dateString) {
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Asia/Jakarta'
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

document.addEventListener('DOMContentLoaded', function() {
    // Jam Real-time
    function updateClock() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            timeZone: 'Asia/Jakarta'
        };
        document.getElementById('real-time-clock').textContent = now.toLocaleDateString('id-ID', options);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Grafik Pengiriman
    const ctx = document.getElementById('deliveryChart').getContext('2d');
    const deliveryChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($schedules['labels']),
            datasets: [
                {
                    label: 'Jadwal Pengiriman',
                    data: @json($schedules['data']),
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Selesai',
                    data: @json($completed['data']),
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw + ' pengiriman';
                        }
                    }
                },
                datalabels: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value + ' pengiriman';
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // Modal Detail Jadwal
    document.querySelectorAll('.view-detail').forEach(button => {
        button.addEventListener('click', function() {
            const scheduleId = this.getAttribute('data-id');
            const modal = new bootstrap.Modal(document.getElementById('scheduleDetailModal'));
            
            // Tampilkan loading spinner
            document.getElementById('scheduleDetailContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-2">Memuat detail pengiriman...</p>
                </div>
            `;
            
            modal.show();
            
            // Ambil data via AJAX
            fetch(`/kurir/jadwal/${scheduleId}/detail`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal memuat data');
                    }
                    return response.text();
                })
                .then(html => {
                    document.getElementById('scheduleDetailContent').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('scheduleDetailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${error.message}
                        </div>
                    `;
                    console.error('Error:', error);
                });
        });
    });

    // Tandai Selesai
    document.getElementById('markAsDoneBtn').addEventListener('click', function() {
        const scheduleId = document.querySelector('#scheduleDetailContent').getAttribute('data-schedule-id');
        
        if (!scheduleId) {
            showAlert('error', 'Gagal', 'ID Jadwal tidak ditemukan');
            return;
        }
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menandai pengiriman ini sebagai selesai?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesai',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#198754'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim permintaan AJAX
                fetch(`/kurir/jadwal/${scheduleId}/selesai`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ _method: 'PUT' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Berhasil', 'Pengiriman telah ditandai selesai');
                        // Tutup modal dan refresh halaman setelah 2 detik
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('scheduleDetailModal')).hide();
                            location.reload();
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Gagal memperbarui status');
                    }
                })
                .catch(error => {
                    showAlert('error', 'Gagal', error.message);
                });
            }
        });
    });

    // Efek hover pada card
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('shadow-lg');
        });
        card.addEventListener('mouseleave', function() {
            this.classList.remove('shadow-lg');
        });
    });
    
    // Auto refresh halaman setiap 5 menit (300000 ms)
    setTimeout(() => {
        location.reload();
    }, 300000);
});
</script>
@endsection