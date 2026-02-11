@extends('layouts.kurir')

@section('title', 'Jadwal Hari Ini - Mustika Rajawali')

@section('content')
<div class="container-fluid px-0 px-md-3">
    <!-- Header Section -->
    <div class="card shadow-sm mb-3 border-0 bg-primary text-white">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-day fa-lg me-3"></i>
                    <div>
                        <h4 class="mb-0">Jadwal Pengiriman Hari Ini</h4>
                        <small class="opacity-75">Total: {{ $jadwals->count() }} Jadwal</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="card shadow-sm mb-3">
        <div class="card-body p-2">
            <form class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="cari" class="form-control form-control-sm" 
                           placeholder="Cari lokasi/daerah/keperluan..." 
                           value="{{ request('cari') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Content Section -->
    @if($jadwals->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
            <h5 class="text-muted">Tidak ada jadwal pengiriman hari ini</h5>
            @if(request()->filled('cari'))
            <p class="text-muted">Silakan coba dengan kata kunci lain</p>
            @endif
        </div>
    </div>
    @else
    @php
        $groupedJadwals = $jadwals->groupBy('daerah');
        $daerahColors = [
            'Semarang Barat' => 'danger',
            'Semarang Timur' => 'primary',
            'Semarang Kota' => 'success',
            'Ungaran' => 'warning'
        ];
    @endphp

    <!-- Desktop View -->
    <div class="d-none d-md-block">
        @foreach($groupedJadwals as $daerah => $jadwalsPerDaerah)
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-{{ $daerahColors[$daerah] ?? 'secondary' }} text-white">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ $daerah }} ({{ $jadwalsPerDaerah->count() }} Lokasi)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Waktu</th>
                                <th width="20%">Lokasi</th>
                                <th width="30%">Alamat</th>
                                <th width="20%">Keperluan</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalsPerDaerah as $jadwal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $jadwal->formatted_tanggal }}</td>
                                <td>{{ $jadwal->lokasi_tujuan }}</td>
                                <td>{{ Str::limit($jadwal->alamat, 30) }}</td>
                                <td>{{ Str::limit($jadwal->keperluan, 20) }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#completeModal{{ $jadwal->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $jadwal->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Mobile View -->
    <div class="d-block d-md-none">
        @foreach($groupedJadwals as $daerah => $jadwalsPerDaerah)
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-{{ $daerahColors[$daerah] ?? 'secondary' }} text-white">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ $daerah }} ({{ $jadwalsPerDaerah->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($jadwalsPerDaerah as $jadwal)
                    <div class="list-group-item py-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-secondary">#{{ $loop->iteration }}</span>
                            <small>{{ $jadwal->formatted_tanggal }}</small>
                        </div>
                        <h6 class="mb-1">{{ $jadwal->lokasi_tujuan }}</h6>
                        <p class="mb-1 small text-muted">{{ Str::limit($jadwal->alamat, 40) }}</p>
                        <p class="mb-2 small">{{ Str::limit($jadwal->keperluan, 30) }}</p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#completeModalMobile{{ $jadwal->id }}">
                                <i class="fas fa-check me-1"></i> Selesai
                            </button>
                            <button class="btn btn-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModalMobile{{ $jadwal->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Modals -->
    @foreach($jadwals as $jadwal)
    <!-- Completion Modal -->
    <div class="modal fade" id="completeModal{{ $jadwal->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kurir.jadwal-hari-ini.selesai', $jadwal->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle me-2"></i>Konfirmasi Penyelesaian
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Konfirmasi penyelesaian pengiriman ke:</p>
                        <h6 class="fw-bold mb-3">{{ $jadwal->lokasi_tujuan }}</h6>
                        <div class="mb-3">
                            <label for="catatan{{ $jadwal->id }}" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="catatan{{ $jadwal->id }}" name="catatan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal{{ $jadwal->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kurir.jadwal-hari-ini.hapus', $jadwal->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Penghapusan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Anda yakin ingin menghapus jadwal ke:</p>
                        <h6 class="fw-bold mb-3">{{ $jadwal->lokasi_tujuan }}</h6>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Data yang dihapus tidak dapat dikembalikan!
                        </div>
                        <div class="mb-3">
                            <label for="alasanHapus{{ $jadwal->id }}" class="form-label">Alasan Penghapusan (Opsional)</label>
                            <textarea class="form-control" id="alasanHapus{{ $jadwal->id }}" name="alasan_hapus" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Completion Modal -->
    <div class="modal fade" id="completeModalMobile{{ $jadwal->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kurir.jadwal-hari-ini.selesai', $jadwal->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle me-2"></i>Konfirmasi
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Konfirmasi penyelesaian pengiriman ke:</p>
                        <h6 class="fw-bold mb-3">{{ $jadwal->lokasi_tujuan }}</h6>
                        <div class="mb-3">
                            <label for="catatanMobile{{ $jadwal->id }}" class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatanMobile{{ $jadwal->id }}" name="catatan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Delete Modal -->
    <div class="modal fade" id="deleteModalMobile{{ $jadwal->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kurir.jadwal-hari-ini.hapus', $jadwal->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>Hapus Jadwal
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Hapus jadwal ke:</p>
                        <h6 class="fw-bold mb-3">{{ $jadwal->lokasi_tujuan }}</h6>
                        <div class="alert alert-danger mt-2">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Data tidak dapat dikembalikan!
                        </div>
                        <div class="mb-3">
                            <label for="alasanHapusMobile{{ $jadwal->id }}" class="form-label">Alasan</label>
                            <textarea class="form-control" id="alasanHapusMobile{{ $jadwal->id }}" name="alasan_hapus" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .badge.bg-danger { background-color: #dc3545; }
    .badge.bg-primary { background-color: #0d6efd; }
    .badge.bg-success { background-color: #198754; }
    .badge.bg-warning { background-color: #ffc107; color: #000; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endsection