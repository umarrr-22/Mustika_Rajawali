@extends('layouts.admin')

@section('title', 'Refil Masuk - Admin Mustika Rajawali')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">
    <!-- Header -->
    <div class="card shadow-sm mb-3 border-0 bg-primary">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <i class="fas fa-inbox text-white me-2 fs-4"></i>
                    <h4 class="fw-semibold mb-0 text-white">Daftar Refil Masuk</h4>
                    <span class="badge bg-white text-primary py-2 px-3 rounded-pill ms-3">
                        <i class="fas fa-box me-1"></i> {{ $refils->total() }} Total
                    </span>
                </div>
                
                <form action="{{ route('admin.refil-masuk') }}" method="GET" class="w-100 w-md-auto">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari pelanggan/kartrid/telepon/alamat..." 
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-light">
                            <i class="fas fa-search text-primary"></i>
                        </button>
                        @if(request('search'))
                        <a href="{{ route('admin.refil-masuk') }}" class="btn btn-outline-light">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-2"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Desktop Table -->
    <div class="card shadow-sm d-none d-md-block mb-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="15%">Pelanggan</th>
                        <th width="12%">Telepon</th>
                        <th width="10%">Layanan</th>
                        <th width="15%">Kartrid</th>
                        <th width="15%">Alamat</th>
                        <th width="8%">Foto</th>
                        <th width="8%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refils as $refil)
                    <tr>
                        <td class="text-muted">{{ ($refils->currentPage() - 1) * $refils->perPage() + $loop->iteration }}</td>
                        <td>
                            {{ $refil->tanggal_masuk->format('d/m/Y') }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle me-2" style="width:32px;height:32px;line-height:32px;">
                                    {{ strtoupper(substr($refil->nama_pelanggan, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $refil->nama_pelanggan }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="tel:{{ $refil->no_telepon }}" class="text-decoration-none">
                                {{ $refil->no_telepon }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-{{ $refil->jenis_layanan == 'Refil' ? 'primary' : 'danger' }}-subtle text-{{ $refil->jenis_layanan == 'Refil' ? 'primary' : 'danger' }} rounded-pill">
                                {{ $refil->jenis_layanan }}
                            </span>
                        </td>
                        <td>{{ $refil->jenis_kartrid }}</td>
                        <td>
                            <div class="text-truncate" style="max-width: 150px;" title="{{ $refil->alamat }}">
                                {{ $refil->alamat }}
                            </div>
                        </td>
                        <td>
                            @if($refil->foto_kartrid)
                            <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.refil-masuk.image', $refil->foto_kartrid) }}', 'Foto Kartrid {{ $refil->jenis_kartrid }}')">
                                <img src="{{ route('admin.refil-masuk.image', $refil->foto_kartrid) }}" 
                                     class="rounded border cursor-zoom" 
                                     style="width:40px;height:40px;object-fit:cover">
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {!! $refil->status_badge !!}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Tidak ada data refil masuk</p>
                                @if(request('search'))
                                <p class="text-muted">Silakan coba dengan kata kunci lain</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile View -->
    <div class="d-md-none">
        @forelse($refils as $refil)
        <div class="card mb-2 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle me-2" style="width:36px;height:36px;line-height:36px;">
                            {{ strtoupper(substr($refil->nama_pelanggan, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $refil->nama_pelanggan }}</h6>
                            <small class="text-muted">{{ $refil->no_telepon }}</small>
                        </div>
                    </div>
                    {!! $refil->status_badge !!}
                </div>

                @if($refil->foto_kartrid)
                <div class="text-center mb-2">
                    <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.refil-masuk.image', $refil->foto_kartrid) }}', 'Foto Kartrid {{ $refil->jenis_kartrid }}')">
                        <img src="{{ route('admin.refil-masuk.image', $refil->foto_kartrid) }}" 
                             class="img-fluid rounded border cursor-zoom" 
                             style="max-height: 120px;">
                    </a>
                </div>
                @endif

                <div class="mb-2">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-print text-muted me-2" style="width:20px"></i>
                        <span>{{ $refil->jenis_kartrid }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-calendar text-muted me-2" style="width:20px"></i>
                        <span>{{ $refil->tanggal_masuk->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex">
                        <i class="fas fa-map-marker-alt text-muted me-2 mt-1" style="width:20px"></i>
                        <span class="text-truncate-2-lines" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $refil->alamat }}
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-2">
                    <span class="badge bg-{{ $refil->jenis_layanan == 'Refil' ? 'primary' : 'danger' }}-subtle text-{{ $refil->jenis_layanan == 'Refil' ? 'primary' : 'danger' }} rounded-pill">
                        {{ $refil->jenis_layanan }}
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="card shadow-sm">
            <div class="card-body text-center py-4">
                <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada data refil masuk</h5>
                @if(request('search'))
                <p class="text-muted">Silakan coba dengan kata kunci lain</p>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $refils->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Modal untuk menampilkan gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Foto Kartrid</h5>
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

<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
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
    
    .text-truncate-2-lines {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .card {
        border-radius: 0.5rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    @media (max-width: 767.98px) {
        .container-fluid {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        
        .btn {
            padding: 0.375rem 0.75rem;
        }
    }
</style>

<script>
// Pastikan Bootstrap JS sudah dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi modal
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    
    // Fungsi untuk menampilkan modal gambar
    window.showImageModal = function(imageUrl, title) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModalTitle').textContent = title || 'Foto Kartrid';
        imageModal.show();
    };

    // Event listener untuk semua gambar (fallback)
    document.querySelectorAll('img.cursor-zoom').forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            showImageModal(this.src, 'Foto Kartrid');
        });
    });
});
</script>

<!-- Pastikan Bootstrap JS dimuat -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection