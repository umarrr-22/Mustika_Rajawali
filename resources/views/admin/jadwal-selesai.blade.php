@extends('layouts.admin')

@section('title', 'Jadwal Selesai - Mustika Rajawali')

@section('content')
<div class="container-fluid px-0 px-md-3">
    <!-- Header Section -->
    <div class="card shadow-sm mb-3 border-0 bg-primary text-white">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-lg me-3"></i>
                    <div>
                        <h4 class="mb-0">Jadwal Selesai</h4>
                        <small class="opacity-75">Total: {{ $jadwals->count() }} Jadwal</small>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-light d-md-none" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-3">
        <div class="card-body p-2">
            <form class="row g-2 align-items-center">
                <div class="col-md-2">
                    <select name="tahun" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="bulan" class="form-select form-select-sm">
                        <option value="">Semua Bulan</option>
                        @foreach($bulan as $key => $namaBulan)
                            <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                {{ $namaBulan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="kurir_id" class="form-select form-select-sm">
                        <option value="">Semua Kurir</option>
                        @foreach($kurirList as $kurir)
                            <option value="{{ $kurir->id }}" {{ request('kurir_id') == $kurir->id ? 'selected' : '' }}>
                                {{ $kurir->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="daerah" class="form-select form-select-sm">
                        <option value="">Semua Daerah</option>
                        <option value="Semarang Barat" {{ request('daerah') == 'Semarang Barat' ? 'selected' : '' }}>Semarang Barat</option>
                        <option value="Semarang Timur" {{ request('daerah') == 'Semarang Timur' ? 'selected' : '' }}>Semarang Timur</option>
                        <option value="Semarang Kota" {{ request('daerah') == 'Semarang Kota' ? 'selected' : '' }}>Semarang Kota</option>
                        <option value="Ungaran" {{ request('daerah') == 'Ungaran' ? 'selected' : '' }}>Ungaran</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <input type="text" name="cari" class="form-control" placeholder="Cari lokasi/alamat..." value="{{ request('cari') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request()->anyFilled(['tahun', 'bulan', 'kurir_id', 'daerah', 'cari']))
                        <a href="{{ route('admin.jadwal-selesai') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Content Section -->
    @if($jadwals->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada jadwal selesai</h5>
            @if(request()->anyFilled(['tahun', 'bulan', 'kurir_id', 'daerah', 'cari']))
            <p class="text-muted">Silakan coba dengan filter yang berbeda</p>
            @endif
        </div>
    </div>
    @else
    <!-- Grouped by Year -->
    <div class="accordion" id="accordionJadwal">
        @php
            $groupedByYear = $jadwals->groupBy(function($item) {
                return $item->completed_at->format('Y');
            })->sortKeysDesc();
        @endphp

        @foreach($groupedByYear as $year => $yearJadwals)
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white p-0">
                <h2 class="mb-0">
                    <button class="btn btn-block text-left px-4 py-3 d-flex justify-content-between align-items-center" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapseYear{{ $year }}"
                            aria-expanded="true">
                        <span class="h5 mb-0 text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Tahun {{ $year }}
                        </span>
                        <span class="badge bg-primary rounded-pill">
                            {{ $yearJadwals->count() }} Jadwal
                        </span>
                    </button>
                </h2>
            </div>

            <div id="collapseYear{{ $year }}" class="collapse show" data-parent="#accordionJadwal">
                <div class="card-body p-0">
                    <!-- Grouped by Month -->
                    <div class="accordion" id="accordionMonth{{ $year }}">
                        @php
                            $groupedByMonth = $yearJadwals->groupBy(function($item) {
                                return $item->completed_at->format('m');
                            })->sortKeysDesc();
                        @endphp

                        @foreach($groupedByMonth as $month => $monthJadwals)
                        @php
                            $monthName = $bulan[(int)$month];
                            $monthId = $year . $month;
                        @endphp
                        <div class="card border-0">
                            <div class="card-header bg-light p-0">
                                <h2 class="mb-0">
                                    <button class="btn btn-block text-left px-4 py-2 d-flex justify-content-between align-items-center" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapseMonth{{ $monthId }}"
                                            aria-expanded="false">
                                        <span class="fw-medium">
                                            <i class="fas fa-calendar me-2"></i>{{ $monthName }}
                                        </span>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                                            {{ $monthJadwals->count() }} Jadwal
                                        </span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseMonth{{ $monthId }}" class="collapse" data-parent="#accordionMonth{{ $year }}">
                                <div class="card-body p-0">
                                    <!-- Grouped by Kurir -->
                                    <div class="accordion" id="accordionKurir{{ $monthId }}">
                                        @php
                                            $groupedByKurir = $monthJadwals->groupBy('kurir_id')->sortBy(function($items, $key) {
                                                return $items->first()->kurir->name ?? 'ZZZ';
                                            });
                                        @endphp

                                        @foreach($groupedByKurir as $kurirId => $kurirJadwals)
                                        @php
                                            $kurir = $kurirJadwals->first()->kurir;
                                            $namaKurir = $kurir ? $kurir->name : 'Tanpa Kurir';
                                        @endphp
                                        <div class="card border-0">
                                            <div class="card-header bg-light p-0">
                                                <h2 class="mb-0">
                                                    <button class="btn btn-block text-left px-4 py-2 d-flex justify-content-between align-items-center" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#collapseKurir{{ $monthId }}{{ $kurirId }}"
                                                            aria-expanded="false">
                                                        <span class="fw-medium">
                                                            <i class="fas fa-user-tie me-2"></i>{{ $namaKurir }}
                                                        </span>
                                                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                                                            {{ $kurirJadwals->count() }} Jadwal
                                                        </span>
                                                    </button>
                                                </h2>
                                            </div>

                                            <div id="collapseKurir{{ $monthId }}{{ $kurirId }}" class="collapse" data-parent="#accordionKurir{{ $monthId }}">
                                                <div class="card-body p-0">
                                                    <!-- Desktop Table View -->
                                                    <div class="d-none d-md-block">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th width="5%">#</th>
                                                                        <th width="12%">Tanggal</th>
                                                                        <th width="20%">Lokasi</th>
                                                                        <th width="25%">Alamat</th>
                                                                        <th width="13%">Daerah</th>
                                                                        <th width="15%">Selesai</th>
                                                                        <th width="10%">Catatan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($kurirJadwals as $jadwal)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $jadwal->formatted_tanggal }}</td>
                                                                        <td>{{ $jadwal->lokasi_tujuan }}</td>
                                                                        <td>{{ Str::limit($jadwal->alamat, 30) }}</td>
                                                                        <td>{!! $jadwal->daerah_badge !!}</td>
                                                                        <td>{{ $jadwal->completed_at->format('d/m/Y') }}</td>
                                                                        <td>
                                                                            @if($jadwal->catatan)
                                                                            <span data-bs-toggle="tooltip" title="{{ $jadwal->catatan }}">
                                                                                {{ Str::limit($jadwal->catatan, 15) }}
                                                                            </span>
                                                                            @else
                                                                            <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <!-- Mobile List View -->
                                                    <div class="d-md-none">
                                                        <div class="list-group list-group-flush">
                                                            @foreach($kurirJadwals as $index => $jadwal)
                                                            <div class="list-group-item border-0 py-3">
                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="me-2 fw-bold text-primary">
                                                                            {{ $index + 1 }}.
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-0">{{ $jadwal->lokasi_tujuan }}</h6>
                                                                            <small class="text-muted">{!! $jadwal->daerah_badge !!}</small>
                                                                        </div>
                                                                    </div>
                                                                    <span class="badge bg-primary-subtle text-primary rounded-pill">
                                                                        {{ $jadwal->completed_at->format('d/m/Y') }}
                                                                    </span>
                                                                </div>

                                                                <div class="row g-2 mt-2">
                                                                    <div class="col-6">
                                                                        <div class="text-muted small">Tanggal</div>
                                                                        <div>{{ $jadwal->formatted_tanggal }}</div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="text-muted small">Alamat</div>
                                                                        <div>{{ $jadwal->alamat }}</div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="text-muted small">Catatan</div>
                                                                        <div>{{ $jadwal->catatan ?? '-' }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<style>
    .accordion .card {
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        border: 1px solid rgba(0,0,0,.125);
    }
    
    .accordion .card-header {
        border-bottom: 1px solid rgba(0,0,0,.05);
        background-color: #f8f9fa;
    }
    
    .accordion .btn {
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .accordion .btn:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .list-group-item {
        border-left: 3px solid transparent;
        transition: all 0.2s;
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
    
    .list-group-item:hover {
        border-left-color: var(--bs-primary);
        background-color: rgba(13, 110, 253, 0.03);
    }
    
    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .badge {
            font-size: 0.75rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Buka semua accordion Year secara default
    document.querySelectorAll('[id^="collapseYear"]').forEach(collapse => {
        new bootstrap.Collapse(collapse, { show: true });
    });

    // Handler untuk accordion button
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function() {
            // Update aria-expanded attribute
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
        });
    });
});
</script>
@endsection