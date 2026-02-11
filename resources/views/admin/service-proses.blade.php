@extends('layouts.admin')

@section('title', 'Service Diproses - Mustika Rajawali')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">
    <!-- Header -->
    <div class="card shadow-sm mb-3 border-0 bg-warning">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <i class="fas fa-tools text-dark me-2 fs-4"></i>
                    <h4 class="fw-semibold mb-0 text-dark">Daftar Service Diproses</h4>
                    <span class="badge bg-white text-warning py-2 px-3 rounded-pill ms-3">
                        <i class="fas fa-box me-1"></i> {{ $totalServices }} Data
                    </span>
                </div>
                
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <!-- Filter Teknisi -->
                    <div class="w-100 w-md-auto">
                        <select class="form-select" id="teknisiFilter">
                            <option value="">Semua Teknisi</option>
                            @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" @if($selectedTeknisi == $tech->id) selected @endif>
                                {{ $tech->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Form Pencarian -->
                    <form action="{{ route('admin.service-proses') }}" method="GET" class="w-100 w-md-auto">
                        <div class="input-group">
                            <input type="text" name="cari" class="form-control" 
                                   placeholder="Cari pelanggan/barang/telepon/alamat..." 
                                   value="{{ request('cari') }}">
                            <button type="submit" class="btn btn-light">
                                <i class="fas fa-search text-warning"></i>
                            </button>
                            @if(request('cari') || request('teknisi_id'))
                            <a href="{{ route('admin.service-proses') }}" class="btn btn-outline-light">
                                <i class="fas fa-times"></i>
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
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

    <!-- Tampilan Desktop -->
    <div class="card shadow-sm d-none d-md-block mb-3">
        <div class="table-responsive">
            @if($totalServices > 0)
                @foreach($technicians as $tech)
                    @if($tech->serviceProsesDitangani->count() > 0)
                    <div class="bg-light p-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;line-height:1;">
                                {{ strtoupper(substr($tech->name, 0, 1)) }}
                            </div>
                            <h5 class="mb-0">{{ $tech->name }}</h5>
                            <span class="badge bg-warning bg-opacity-10 text-warning ms-2">
                                {{ $tech->serviceProsesDitangani->count() }} Service
                            </span>
                        </div>
                    </div>
                    
                    <table class="table table-hover align-middle mb-4">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Tanggal Masuk</th>
                                <th width="12%">Pelanggan</th>
                                <th width="8%">Telepon</th>
                                <th width="8%">Alamat</th>
                                <th width="8%">Layanan</th>
                                <th width="10%">Barang</th>
                                <th width="12%">Kerusakan</th>
                                <th width="8%">Sparepart</th>
                                <th width="7%">Foto</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tech->serviceProsesDitangani as $service)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $service->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:28px;height:28px;line-height:1;">
                                            {{ strtoupper(substr($service->nama_pelanggan, 0, 1)) }}
                                        </div>
                                        <div class="fw-medium">{{ $service->nama_pelanggan }}</div>
                                    </div>
                                </td>
                                <td>
                                    <a href="tel:{{ $service->no_telepon }}" class="text-decoration-none">
                                        {{ $service->no_telepon }}
                                    </a>
                                </td>
                                <td class="text-truncate" style="max-width: 150px;" title="{{ $service->alamat ?? '-' }}">
                                    {{ $service->alamat ?? '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $service->jenis_layanan == 'Service' ? 'primary' : 'danger' }}-subtle text-{{ $service->jenis_layanan == 'Service' ? 'primary' : 'danger' }} rounded-pill">
                                        {{ $service->jenis_layanan }}
                                    </span>
                                </td>
                                <td>{{ $service->jenis_barang }}</td>
                                <td class="text-truncate" style="max-width: 150px;" title="{{ $service->kerusakan ?? '-' }}">
                                    {{ $service->kerusakan ?? '-' }}
                                </td>
                                <td>{{ $service->sparepart_diganti ?? '-' }}</td>
                                <td>
                                    @if($service->foto_barang)
                                    <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.service-masuk.image', $service->foto_barang) }}', 'Foto {{ $service->jenis_barang }}')">
                                        <img src="{{ route('admin.service-masuk.image', $service->foto_barang) }}" 
                                             class="rounded border cursor-zoom" 
                                             style="width:40px;height:40px;object-fit:cover">
                                    </a>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {!! $service->getStatusBadgeAttribute() !!}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                @endforeach
            @else
                <div class="text-center py-4">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-tools fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Belum ada data service yang diproses</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Tampilan Mobile -->
    <div class="d-md-none">
        @if($totalServices > 0)
            @foreach($technicians as $tech)
                @if($tech->serviceProsesDitangani->count() > 0)
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;line-height:1;">
                                {{ strtoupper(substr($tech->name, 0, 1)) }}
                            </div>
                            <h5 class="mb-0">{{ $tech->name }}</h5>
                            <span class="badge bg-warning bg-opacity-10 text-warning ms-auto">
                                {{ $tech->serviceProsesDitangani->count() }} Service
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        @foreach($tech->serviceProsesDitangani as $service)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;line-height:1;">
                                        {{ strtoupper(substr($service->nama_pelanggan, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $service->nama_pelanggan }}</h6>
                                        <small class="text-muted">{{ $service->no_telepon }}</small>
                                    </div>
                                </div>
                                {!! $service->getStatusBadgeAttribute() !!}
                            </div>

                            @if($service->foto_barang)
                            <div class="text-center mb-2">
                                <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.service-masuk.image', $service->foto_barang) }}', 'Foto {{ $service->jenis_barang }}')">
                                    <img src="{{ route('admin.service-masuk.image', $service->foto_barang) }}" 
                                         class="img-fluid rounded border cursor-zoom" 
                                         style="max-height: 120px;">
                                </a>
                            </div>
                            @endif

                            <div class="mb-2">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-box text-muted me-2" style="width:20px"></i>
                                    <span>{{ $service->jenis_barang }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-exclamation-triangle text-muted me-2" style="width:20px"></i>
                                    <span class="text-truncate-2-lines">{{ $service->kerusakan ?? 'Tidak ada keterangan kerusakan' }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-map-marker-alt text-muted me-2" style="width:20px"></i>
                                    <span class="text-truncate-2-lines">{{ $service->alamat ?? 'Tidak ada alamat' }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-calendar text-muted me-2" style="width:20px"></i>
                                    <span>Masuk: {{ $service->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-cogs text-muted me-2" style="width:20px"></i>
                                    <span>Sparepart: {{ $service->sparepart_diganti ?? 'Tidak ada' }}</span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-2">
                                <span class="badge bg-{{ $service->jenis_layanan == 'Service' ? 'primary' : 'danger' }}-subtle text-{{ $service->jenis_layanan == 'Service' ? 'primary' : 'danger' }} rounded-pill">
                                    {{ $service->jenis_layanan }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach
        @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-tools fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Belum ada data service yang diproses</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@section('scripts')
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

    // Event listener for all zoomable images (fallback)
    document.querySelectorAll('img.cursor-zoom').forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            const parentLink = this.closest('a');
            if (parentLink && parentLink.onclick) return;
            showImageModal(this.src, 'Foto Barang');
        });
    });

    // Filter teknisi
    document.getElementById('teknisiFilter').addEventListener('change', function() {
        const teknisiId = this.value;
        if (teknisiId) {
            window.location.href = "{{ route('admin.service-proses') }}?teknisi_id=" + teknisiId;
        } else {
            window.location.href = "{{ route('admin.service-proses') }}";
        }
    });

    // Highlight hasil pencarian
    @if(request('cari'))
    const searchTerm = "{{ request('cari') }}";
    const regex = new RegExp(searchTerm, 'gi');
    document.querySelectorAll('.highlightable').forEach(element => {
        const text = element.textContent;
        element.innerHTML = text.replace(regex, match => 
            `<span class="bg-warning bg-opacity-50">${match}</span>`
        );
    });
    @endif
});
</script>
@endsection

@section('styles')
<style>
    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        width: 32px;
        height: 32px;
        line-height: 1;
    }
    
    .cursor-zoom {
        cursor: zoom-in;
        transition: transform 0.2s;
    }
    
    .cursor-zoom:hover {
        transform: scale(1.05);
    }
    
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    
    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .text-truncate-2-lines {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .card {
        border-radius: 0.5rem;
    }
    
    @media (max-width: 767.98px) {
        .container-fluid {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        
        .btn {
            padding: 0.375rem 0.75rem;
        }
        
        .avatar {
            width: 36px;
            height: 36px;
        }
    }
</style>
@endsection
@endsection