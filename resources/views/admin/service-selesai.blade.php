@extends('layouts.admin')

@section('title', 'Service Selesai - Mustika Rajawali')

@section('content')
<div class="container-fluid px-0 px-md-3">
    <!-- CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Header Section -->
    <div class="card shadow-sm mb-3 border-0 bg-success text-white">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-lg me-3"></i>
                    <div>
                        <h4 class="mb-0">Service Selesai</h4>
                        <small class="opacity-75">Total: {{ $services->total() }} Service</small>
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
                            <option value="{{ $tahun }}" {{ $currentTahun == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="bulan" class="form-select form-select-sm">
                        @foreach($bulan as $key => $namaBulan)
                            <option value="{{ $key }}" {{ $currentBulan == $key ? 'selected' : '' }}>
                                {{ $namaBulan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="jenis_layanan" class="form-select form-select-sm">
                        <option value="">Semua Jenis</option>
                        <option value="Service" {{ $currentJenisLayanan == 'Service' ? 'selected' : '' }}>Service</option>
                        <option value="Komplain" {{ $currentJenisLayanan == 'Komplain' ? 'selected' : '' }}>Komplain</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="teknisi" class="form-select form-select-sm">
                        <option value="">Semua Teknisi</option>
                        @foreach($teknisiList as $teknisi)
                            <option value="{{ $teknisi->id }}" {{ $currentTeknisi == $teknisi->id ? 'selected' : '' }}>
                                {{ $teknisi->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="cari" class="form-control form-control-sm" 
                           placeholder="Cari pelanggan/barang/telepon..." 
                           value="{{ $currentCari }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm btn-success w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
                @if($currentTahun || $currentBulan || $currentJenisLayanan || $currentTeknisi || $currentCari)
                <div class="col-md-1 mt-2 mt-md-0">
                    <a href="{{ route('admin.service-selesai') }}" class="btn btn-sm btn-outline-danger w-100">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Modal untuk menampilkan gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalTitle">Foto Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" class="img-fluid" style="max-height: 80vh; width: auto;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    @if($services->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada service selesai</h5>
            @if($currentTahun || $currentBulan || $currentJenisLayanan || $currentTeknisi || $currentCari)
            <p class="text-muted">Silakan coba dengan filter yang berbeda</p>
            @endif
        </div>
    </div>
    @else
    <!-- Grouped by Year/Month/Teknisi/Layanan -->
    <div class="accordion" id="accordionService">
        @php
            // Group by year
            $groupedByYear = $services->groupBy(function($item) {
                return $item->tanggal_selesai->format('Y');
            });
        @endphp

        @foreach($groupedByYear as $year => $yearServices)
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white p-0">
                <h2 class="mb-0">
                    <button class="btn btn-block text-left px-4 py-3 d-flex justify-content-between align-items-center" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapseYear{{ $year }}"
                            aria-expanded="{{ $currentTahun == $year ? 'true' : 'false' }}">
                        <span class="h5 mb-0 text-success">
                            <i class="fas fa-calendar-alt me-2"></i>Tahun {{ $year }}
                        </span>
                        <span class="badge bg-success rounded-pill">
                            {{ $yearServices->count() }} Service
                        </span>
                    </button>
                </h2>
            </div>

            <div id="collapseYear{{ $year }}" class="collapse {{ $currentTahun == $year ? 'show' : '' }}" data-parent="#accordionService">
                <div class="card-body p-0">
                    <div class="accordion" id="accordionMonth{{ $year }}">
                        @php
                            // Group by month within year
                            $groupedByMonth = $yearServices->groupBy(function($item) {
                                return $item->tanggal_selesai->format('m');
                            })->sortKeysDesc();
                        @endphp

                        @foreach($groupedByMonth as $month => $monthServices)
                        @php
                            $monthName = $bulan[$month];
                            $monthId = $year . $month;
                            $shouldExpand = ($currentTahun == $year && $currentBulan == $month);
                        @endphp
                        <div class="card border-0">
                            <div class="card-header bg-light p-0">
                                <h2 class="mb-0">
                                    <button class="btn btn-block text-left px-4 py-2 d-flex justify-content-between align-items-center" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapseMonth{{ $monthId }}"
                                            aria-expanded="{{ $shouldExpand ? 'true' : 'false' }}">
                                        <span class="fw-medium">
                                            <i class="fas fa-calendar me-2"></i>{{ $monthName }}
                                        </span>
                                        <span class="badge bg-success-subtle text-success rounded-pill">
                                            {{ $monthServices->count() }} Service
                                        </span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseMonth{{ $monthId }}" class="collapse {{ $shouldExpand ? 'show' : '' }}" data-parent="#accordionMonth{{ $year }}">
                                <div class="card-body p-0">
                                    <!-- Group by Teknisi -->
                                    <div class="accordion" id="accordionTeknisi{{ $monthId }}">
                                        @php
                                            // Group by teknisi within month
                                            $groupedByTeknisi = $monthServices->groupBy(function($item) {
                                                return $item->teknisi_id ? $item->teknisi->name : 'Belum Ditentukan';
                                            });
                                        @endphp

                                        @foreach($groupedByTeknisi as $teknisiName => $teknisiServices)
                                        <div class="card border-0 mt-2">
                                            <div class="card-header bg-light p-0">
                                                <h2 class="mb-0">
                                                    <button class="btn btn-block text-left px-4 py-2 d-flex justify-content-between align-items-center" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#collapseTeknisi{{ $monthId }}{{ $loop->index }}"
                                                            aria-expanded="false">
                                                        <span class="fw-medium">
                                                            <i class="fas fa-user-cog me-2"></i>{{ $teknisiName }}
                                                        </span>
                                                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                                                            {{ $teknisiServices->count() }}
                                                        </span>
                                                    </button>
                                                </h2>
                                            </div>

                                            <div id="collapseTeknisi{{ $monthId }}{{ $loop->index }}" class="collapse" data-parent="#accordionTeknisi{{ $monthId }}">
                                                <div class="card-body p-0">
                                                    <!-- Group by Layanan (Service/Komplain) -->
                                                    <div class="accordion" id="accordionLayanan{{ $monthId }}{{ $loop->index }}">
                                                        @php
                                                            $serviceItems = $teknisiServices->where('jenis_layanan', 'Service');
                                                            $komplainItems = $teknisiServices->where('jenis_layanan', 'Komplain');
                                                        @endphp

                                                        <!-- Service Section -->
                                                        @if($serviceItems->count() > 0)
                                                        <div class="card border-0">
                                                            <div class="card-header bg-light p-0">
                                                                <h2 class="mb-0">
                                                                    <button class="btn btn-block text-left px-4 py-2 d-flex justify-content-between align-items-center" 
                                                                            type="button" 
                                                                            data-bs-toggle="collapse" 
                                                                            data-bs-target="#collapseService{{ $monthId }}{{ $loop->index }}"
                                                                            aria-expanded="false">
                                                                        <span class="fw-medium">
                                                                            <i class="fas fa-tools me-2"></i>Service
                                                                        </span>
                                                                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                                                                            {{ $serviceItems->count() }}
                                                                        </span>
                                                                    </button>
                                                                </h2>
                                                            </div>

                                                            <div id="collapseService{{ $monthId }}{{ $loop->index }}" class="collapse" data-parent="#accordionLayanan{{ $monthId }}{{ $loop->index }}">
                                                                <div class="card-body p-0">
                                                                    <!-- Desktop Table View -->
                                                                    <div class="table-responsive d-none d-md-block">
                                                                        <table class="table table-hover mb-0">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th width="3%">No</th>
                                                                                    <th width="8%">Tgl Masuk</th>
                                                                                    <th width="12%">Pelanggan</th>
                                                                                    <th width="8%">Telepon</th>
                                                                                    <th width="15%">Alamat</th>
                                                                                    <th width="10%">Barang</th>
                                                                                    <th width="12%">Kerusakan</th>
                                                                                    <th width="10%">Teknisi</th>
                                                                                    <th width="10%">Sparepart</th>
                                                                                    <th width="8%">Tgl Selesai</th>
                                                                                    <th width="6%">Foto</th>
                                                                                    <th width="8%">Verifikasi</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($serviceItems as $service)
                                                                                <tr>
                                                                                    <td>{{ ($services->currentPage() - 1) * $services->perPage() + $loop->iteration }}</td>
                                                                                    <td>{{ $service->tanggal_masuk->format('d/m/Y') }}</td>
                                                                                    <td>
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                                                                                                {{ strtoupper(substr($service->nama_pelanggan, 0, 1)) }}
                                                                                            </div>
                                                                                            <div class="fw-medium">{{ $service->nama_pelanggan }}</div>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>{{ $service->no_telepon }}</td>
                                                                                    <td class="text-truncate" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $service->alamat }}">
                                                                                        {{ $service->alamat }}
                                                                                    </td>
                                                                                    <td>{{ $service->jenis_barang }}</td>
                                                                                    <td class="text-truncate" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $service->kerusakan }}">
                                                                                        {{ $service->kerusakan ?? '-' }}
                                                                                    </td>
                                                                                    <td>{{ $service->teknisi->name ?? '-' }}</td>
                                                                                    <td class="text-truncate" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $service->sparepart_diganti }}">
                                                                                        {{ $service->sparepart_diganti ?? '-' }}
                                                                                    </td>
                                                                                    <td>{{ $service->tanggal_selesai->format('d/m/Y H:i') }}</td>
                                                                                    <td>
                                                                                        @if($service->foto_barang)
                                                                                        <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.service-selesai.image', $service->foto_barang) }}', 'Foto {{ $service->jenis_barang }} - {{ $service->nama_pelanggan }}')">
                                                                                            <img src="{{ route('admin.service-selesai.image', $service->foto_barang) }}" 
                                                                                                 class="rounded border cursor-zoom" 
                                                                                                 style="width:40px;height:40px;object-fit:cover">
                                                                                        </a>
                                                                                        @else
                                                                                        <span class="text-muted">-</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        <form class="toggle-verifikasi-form" method="POST" action="{{ route('admin.service-selesai.toggle-verifikasi', $service->id) }}">
                                                                                            @csrf
                                                                                            <button type="submit" class="btn btn-sm {{ $service->is_verified ? 'btn-success' : 'btn-danger' }}" 
                                                                                                    title="{{ $service->is_verified ? 'Terverifikasi' : 'Klik untuk verifikasi' }}">
                                                                                                <i class="fas fa-check"></i>
                                                                                            </button>
                                                                                        </form>
                                                                                    </td>
                                                                                </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    <!-- Mobile List View -->
                                                                    <div class="d-md-none">
                                                                        <div class="list-group list-group-flush">
                                                                            @foreach($serviceItems as $service)
                                                                            <div class="list-group-item border-0 py-3">
                                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                    <div class="d-flex align-items-center">
                                                                                        <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                                                                            {{ strtoupper(substr($service->nama_pelanggan, 0, 1)) }}
                                                                                        </div>
                                                                                        <h6 class="mb-0">{{ $service->nama_pelanggan }}</h6>
                                                                                    </div>
                                                                                    <form class="toggle-verifikasi-form" method="POST" action="{{ route('admin.service-selesai.toggle-verifikasi', $service->id) }}">
                                                                                        @csrf
                                                                                        <button type="submit" class="btn btn-sm {{ $service->is_verified ? 'btn-success' : 'btn-danger' }}" 
                                                                                                title="{{ $service->is_verified ? 'Terverifikasi' : 'Klik untuk verifikasi' }}">
                                                                                            <i class="fas fa-check"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                </div>

                                                                                @if($service->foto_barang)
                                                                                <div class="text-center mb-2">
                                                                                    <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.service-selesai.image', $service->foto_barang) }}', 'Foto {{ $service->jenis_barang }} - {{ $service->nama_pelanggan }}')">
                                                                                        <img src="{{ route('admin.service-selesai.image', $service->foto_barang) }}" 
                                                                                             class="img-fluid rounded border cursor-zoom" 
                                                                                             style="max-height: 120px;">
                                                                                    </a>
                                                                                </div>
                                                                                @endif

                                                                                <div class="row g-2">
                                                                                    <div class="col-6">
                                                                                        <div class="text-muted small">Tgl Masuk</div>
                                                                                        <div>{{ $service->tanggal_masuk->format('d/m/Y') }}</div>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <div class="text-muted small">Tgl Selesai</div>
                                                                                        <div>{{ $service->tanggal_selesai->format('d/m/Y H:i') }}</div>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <div class="text-muted small">Barang</div>
                                                                                        <div>{{ $service->jenis_barang }}</div>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <div class="text-muted small">Teknisi</div>
                                                                                        <div>{{ $service->teknisi->name ?? '-' }}</div>
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <div class="text-muted small">Alamat</div>
                                                                                        <div>{{ $service->alamat }}</div>
                                                                                    </div>
                                                                                    @if($service->kerusakan)
                                                                                    <div class="col-12">
                                                                                        <div class="text-muted small">Kerusakan</div>
                                                                                        <div>{{ $service->kerusakan }}</div>
                                                                                    </div>
                                                                                    @endif
                                                                                    @if($service->sparepart_diganti)
                                                                                    <div class="col-12">
                                                                                        <div class="text-muted small">Sparepart Diganti</div>
                                                                                        <div>{{ $service->sparepart_diganti }}</div>
                                                                                    </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        <!-- Komplain Section -->
                                                        @if($komplainItems->count() > 0)
                                                        <div class="card border-0 mt-2">
                                                            <div class="card-header bg-light p-0">
                                                                <h2 class="mb-0">
                                                                    <button class="btn btn-block text-left px-4 py-2 d-flex justify-content-between align-items-center" 
                                                                            type="button" 
                                                                            data-bs-toggle="collapse" 
                                                                            data-bs-target="#collapseKomplain{{ $monthId }}{{ $loop->index }}"
                                                                            aria-expanded="false">
                                                                        <span class="fw-medium">
                                                                            <i class="fas fa-exclamation-triangle me-2"></i>Komplain
                                                                        </span>
                                                                        <span class="badge bg-danger-subtle text-danger rounded-pill">
                                                                            {{ $komplainItems->count() }}
                                                                        </span>
                                                                    </button>
                                                                </h2>
                                                            </div>

                                                            <div id="collapseKomplain{{ $monthId }}{{ $loop->index }}" class="collapse" data-parent="#accordionLayanan{{ $monthId }}{{ $loop->index }}">
                                                                <div class="card-body p-0">
                                                                    <!-- Desktop Table View -->
                                                                    <div class="table-responsive d-none d-md-block">
                                                                        <table class="table table-hover mb-0">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th width="3%">No</th>
                                                                                    <th width="8%">Tgl Masuk</th>
                                                                                    <th width="12%">Pelanggan</th>
                                                                                    <th width="8%">Telepon</th>
                                                                                    <th width="15%">Alamat</th>
                                                                                    <th width="10%">Barang</th>
                                                                                    <th width="12%">Kerusakan</th>
                                                                                    <th width="10%">Teknisi</th>
                                                                                    <th width="10%">Sparepart</th>
                                                                                    <th width="8%">Tgl Selesai</th>
                                                                                    <th width="6%">Foto</th>
                                                                                    <th width="8%">Verifikasi</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($komplainItems as $service)
                                                                                <tr>
                                                                                    <td>{{ ($services->currentPage() - 1) * $services->perPage() + $loop->iteration }}</td>
                                                                                    <td>{{ $service->tanggal_masuk->format('d/m/Y') }}</td>
                                                                                    <td>
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                                                                                                {{ strtoupper(substr($service->nama_pelanggan, 0, 1)) }}
                                                                                            </div>
                                                                                            <div class="fw-medium">{{ $service->nama_pelanggan }}</div>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>{{ $service->no_telepon }}</td>
                                                                                    <td class="text-truncate" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $service->alamat }}">
                                                                                        {{ $service->alamat }}
                                                                                    </td>
                                                                                    <td>{{ $service->jenis_barang }}</td>
                                                                                    <td class="text-truncate" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $service->kerusakan }}">
                                                                                        {{ $service->kerusakan ?? '-' }}
                                                                                    </td>
                                                                                    <td>{{ $service->teknisi->name ?? '-' }}</td>
                                                                                    <td class="text-truncate" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $service->sparepart_diganti }}">
                                                                                        {{ $service->sparepart_diganti ?? '-' }}
                                                                                    </td>
                                                                                    <td>{{ $service->tanggal_selesai->format('d/m/Y H:i') }}</td>
                                                                                    <td>
                                                                                        @if($service->foto_barang)
                                                                                        <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.service-selesai.image', $service->foto_barang) }}', 'Foto {{ $service->jenis_barang }} - {{ $service->nama_pelanggan }}')">
                                                                                            <img src="{{ route('admin.service-selesai.image', $service->foto_barang) }}" 
                                                                                                 class="rounded border cursor-zoom" 
                                                                                                 style="width:40px;height:40px;object-fit:cover">
                                                                                        </a>
                                                                                        @else
                                                                                        <span class="text-muted">-</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        <form class="toggle-verifikasi-form" method="POST" action="{{ route('admin.service-selesai.toggle-verifikasi', $service->id) }}">
                                                                                            @csrf
                                                                                            <button type="submit" class="btn btn-sm {{ $service->is_verified ? 'btn-success' : 'btn-danger' }}" 
                                                                                                    title="{{ $service->is_verified ? 'Terverifikasi' : 'Klik untuk verifikasi' }}">
                                                                                                <i class="fas fa-check"></i>
                                                                                            </button>
                                                                                        </form>
                                                                                    </td>
                                                                                </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    <!-- Mobile List View -->
                                                                    <div class="d-md-none">
                                                                        <div class="list-group list-group-flush">
                                                                            @foreach($komplainItems as $service)
                                                                            <div class="list-group-item border-0 py-3">
                                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                    <div class="d-flex align-items-center">
                                                                                        <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                                                                            {{ strtoupper(substr($service->nama_pelanggan, 0, 1)) }}
                                                                                        </div>
                                                                                        <h6 class="mb-0">{{ $service->nama_pelanggan }}</h6>
                                                                                    </div>
                                                                                    <form class="toggle-verifikasi-form" method="POST" action="{{ route('admin.service-selesai.toggle-verifikasi', $service->id) }}">
                                                                                        @csrf
                                                                                        <button type="submit" class="btn btn-sm {{ $service->is_verified ? 'btn-success' : 'btn-danger' }}" 
                                                                                                title="{{ $service->is_verified ? 'Terverifikasi' : 'Klik untuk verifikasi' }}">
                                                                                            <i class="fas fa-check"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                </div>

                                                                                @if($service->foto_barang)
                                                                                <div class="text-center mb-2">
                                                                                    <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.service-selesai.image', $service->foto_barang) }}', 'Foto {{ $service->jenis_barang }} - {{ $service->nama_pelanggan }}')">
                                                                                        <img src="{{ route('admin.service-selesai.image', $service->foto_barang) }}" 
                                                                                             class="img-fluid rounded border cursor-zoom" 
                                                                                             style="max-height: 120px;">
                                                                                    </a>
                                                                                </div>
                                                                                @endif

                                                                                <div class="row g-2">
                                                                                    <div class="col-6">
                                                                                        <div class="text-muted small">Tgl Masuk</div>
                                                                                        <div>{{ $service->tanggal_masuk->format('d/m/Y') }}</div>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <div class="text-muted small">Tgl Selesai</div>
                                                                                        <div>{{ $service->tanggal_selesai->format('d/m/Y H:i') }}</div>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <div class="text-muted small">Barang</div>
                                                                                        <div>{{ $service->jenis_barang }}</div>
                                                                                    </div>
                                                                                    <div class="col-6">
                                                                                        <div class="text-muted small">Teknisi</div>
                                                                                        <div>{{ $service->teknisi->name ?? '-' }}</div>
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <div class="text-muted small">Alamat</div>
                                                                                        <div>{{ $service->alamat }}</div>
                                                                                    </div>
                                                                                    @if($service->kerusakan)
                                                                                    <div class="col-12">
                                                                                        <div class="text-muted small">Kerusakan</div>
                                                                                        <div>{{ $service->kerusakan }}</div>
                                                                                    </div>
                                                                                    @endif
                                                                                    @if($service->sparepart_diganti)
                                                                                    <div class="col-12">
                                                                                        <div class="text-muted small">Sparepart Diganti</div>
                                                                                        <div>{{ $service->sparepart_diganti }}</div>
                                                                                    </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
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

    <!-- Pagination -->
    @if($services->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $services->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Tombol Toggle Style */
    .toggle-verifikasi-form button {
        transition: all 0.3s ease;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    .toggle-verifikasi-form button.btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    .toggle-verifikasi-form button.btn-danger:hover {
        background-color: #bb2d3b;
        border-color: #b02a37;
    }
    
    .toggle-verifikasi-form button.btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .toggle-verifikasi-form button.btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    /* Image preview style */
    .cursor-zoom {
        cursor: zoom-in;
        transition: transform 0.2s;
    }
    
    .cursor-zoom:hover {
        transform: scale(1.05);
    }
    
    /* Loading spinner style */
    .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize image modal
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    
    // Function to show image in modal
    window.showImageModal = function(imageUrl, title) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModalTitle').textContent = title || 'Foto Barang';
        imageModal.show();
    };

    // Handle verification toggle form submission
    document.querySelectorAll('.toggle-verifikasi-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const button = this.querySelector('button');
            const originalHTML = button.innerHTML;
            
            // Show loading state
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            button.disabled = true;
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: new FormData(this)
                });
                
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Network response was not ok');
                }

                if (data.success) {
                    // Update button appearance
                    button.classList.toggle('btn-danger');
                    button.classList.toggle('btn-success');
                    
                    const newTitle = data.is_verified ? 'Terverifikasi' : 'Klik untuk verifikasi';
                    button.setAttribute('title', newTitle);
                    
                    // Show success notification
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Show error notification
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                
                Toast.fire({
                    icon: 'error',
                    title: 'Gagal: ' + error.message
                });
                
                // Restore original button content
                button.innerHTML = originalHTML;
            } finally {
                button.disabled = false;
            }
        });
    });

    // Event listener for all zoomable images (fallback)
    document.querySelectorAll('img.cursor-zoom').forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            const parentLink = this.closest('a');
            if (parentLink && parentLink.onclick) return;
            showImageModal(this.src, 'Foto Barang');
        });
    });

    // Function to determine if accordion should be expanded
    function shouldExpandAccordion(elementId) {
        const urlParams = new URLSearchParams(window.location.search);
        const tahun = urlParams.get('tahun');
        const bulan = urlParams.get('bulan');
        
        // Check if elementId matches current filter
        if (tahun && elementId.includes(`Year${tahun}`)) {
            return true;
        }
        
        if (tahun && bulan && elementId.includes(`Month${tahun}${bulan}`)) {
            return true;
        }
        
        // Check localStorage for user preference
        const storedState = localStorage.getItem(`#${elementId}`);
        return storedState === 'true';
    }

    // Initialize accordions based on current filter and localStorage
    document.querySelectorAll('.accordion-collapse').forEach(collapse => {
        const elementId = collapse.id;
        const shouldExpand = shouldExpandAccordion(elementId);
        
        if (shouldExpand) {
            new bootstrap.Collapse(collapse, { toggle: true }).show();
        } else {
            new bootstrap.Collapse(collapse, { toggle: false }).hide();
        }
    });

    // Save accordion state when toggled
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-bs-target');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Only save state for year, month, teknisi, and layanan accordions
            if (target.includes('Year') || target.includes('Month') || 
                target.includes('Teknisi') || target.includes('Service') || 
                target.includes('Komplain')) {
                localStorage.setItem(target, isExpanded ? 'false' : 'true');
            }
        });
    });

    // Handle filter form submission
    const filterForm = document.querySelector('form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Reset page to 1 when applying new filters
            const url = new URL(window.location.href);
            if (url.searchParams.get('page')) {
                url.searchParams.delete('page');
                window.location.href = url.toString();
                e.preventDefault();
            }
        });
    }
});
</script>
@endsection