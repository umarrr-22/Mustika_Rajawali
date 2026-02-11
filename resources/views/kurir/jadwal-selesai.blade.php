@extends('layouts.kurir')

@section('title', 'Jadwal Selesai - Mustika Rajawali')

@section('content')
<div class="container-fluid px-0 px-md-3">
    <!-- Header Section -->
    <div class="card shadow-sm mb-3 border-0 bg-success text-white">
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
                <div class="col-md-3">
                    <select name="tahun" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="bulan" class="form-select form-select-sm">
                        <option value="">Semua Bulan</option>
                        @foreach($bulan as $key => $namaBulan)
                            <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                {{ $namaBulan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari lokasi/daerah/catatan..." value="{{ request('cari') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-success w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
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
            @if(request()->anyFilled(['tahun', 'bulan', 'cari']))
            <p class="text-muted">Silakan coba dengan filter yang berbeda</p>
            @endif
        </div>
    </div>
    @else
    <!-- Grouped by Year/Month -->
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
                        <span class="h5 mb-0 text-success">
                            <i class="fas fa-calendar-alt me-2"></i>Tahun {{ $year }}
                        </span>
                        <span class="badge bg-success rounded-pill">
                            {{ $yearJadwals->count() }} Jadwal
                        </span>
                    </button>
                </h2>
            </div>

            <div id="collapseYear{{ $year }}" class="collapse show" data-parent="#accordionJadwal">
                <div class="card-body p-0">
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
                                            aria-expanded="true">
                                        <span class="fw-medium">
                                            <i class="fas fa-calendar me-2"></i>{{ $monthName }}
                                        </span>
                                        <span class="badge bg-success-subtle text-success rounded-pill">
                                            {{ $monthJadwals->count() }} Jadwal
                                        </span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseMonth{{ $monthId }}" class="collapse show" data-parent="#accordionMonth{{ $year }}">
                                <div class="card-body p-0">
                                    <!-- Desktop Table View -->
                                    <div class="d-none d-md-block">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="12%">Tanggal</th>
                                                        <th width="15%">Lokasi</th>
                                                        <th width="20%">Alamat</th>
                                                        <th width="12%">Daerah</th>
                                                        <th width="12%">Selesai</th>
                                                        <th width="20%">Catatan</th>
                                                        <th width="4%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($monthJadwals as $jadwal)
                                                    <tr id="jadwal-row-{{ $jadwal->id }}">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $jadwal->formatted_tanggal }}</td>
                                                        <td>{{ $jadwal->lokasi_tujuan }}</td>
                                                        <td>{{ Str::limit($jadwal->alamat, 25) }}</td>
                                                        <td>{!! $jadwal->daerah_badge !!}</td>
                                                        <td>{{ $jadwal->completed_at->format('d/m/Y') }}</td>
                                                        <td>
                                                            @if($jadwal->catatan)
                                                            <span data-bs-toggle="tooltip" title="{{ $jadwal->catatan }}">
                                                                {{ Str::limit($jadwal->catatan, 20) }}
                                                            </span>
                                                            @else
                                                            <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-danger hapus-jadwal" 
                                                                    data-id="{{ $jadwal->id }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
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
                                            @foreach($monthJadwals as $jadwal)
                                            <div class="list-group-item border-0 py-3" id="jadwal-row-{{ $jadwal->id }}">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2" style="width:36px;height:36px;line-height:36px;">
                                                            {{ strtoupper(substr($jadwal->lokasi_tujuan, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $jadwal->lokasi_tujuan }}</h6>
                                                            <small class="text-muted">{{ $jadwal->daerah }}</small>
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-success-subtle text-success rounded-pill">
                                                        {{ $jadwal->completed_at->format('d/m/Y') }}
                                                    </span>
                                                </div>

                                                <div class="row g-2">
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

                                                <div class="d-flex justify-content-end mt-2">
                                                    <button class="btn btn-sm btn-outline-danger hapus-jadwal" 
                                                            data-id="{{ $jadwal->id }}">
                                                        <i class="fas fa-trash-alt me-1"></i> Hapus
                                                    </button>
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
        background-color: rgba(25, 135, 84, 0.05);
    }
    
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
    }
    
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1);
    }
    
    .list-group-item {
        border-left: 3px solid transparent;
        transition: all 0.2s;
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
    
    .list-group-item:hover {
        border-left-color: var(--bs-success);
        background-color: rgba(25, 135, 84, 0.03);
    }
    
    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
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

    // Handle delete button click
    document.querySelectorAll('.hapus-jadwal').forEach(button => {
        button.addEventListener('click', function() {
            const jadwalId = this.getAttribute('data-id');
            
            fetch(`/kurir/jadwal-selesai/${jadwalId}/hapus`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the deleted row
                    const row = document.getElementById(`jadwal-row-${jadwalId}`);
                    if (row) {
                        row.remove();
                    }
                    
                    // Reload the page if no more items
                    if (document.querySelectorAll('.hapus-jadwal').length === 0) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 300);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
    
    // Remember accordion state
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-bs-target');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            if (target.includes('Year') || target.includes('Month')) {
                localStorage.setItem(target, isExpanded ? 'false' : 'true');
            }
        });
    });
    
    // Restore accordion state
    document.querySelectorAll('.accordion-collapse').forEach(collapse => {
        const id = '#' + collapse.id;
        if (id.includes('Year') || id.includes('Month')) {
            const storedState = localStorage.getItem(id);
            if (storedState === 'false') {
                new bootstrap.Collapse(collapse, {toggle: false}).hide();
            }
        }
    });
});
</script>
@endsection