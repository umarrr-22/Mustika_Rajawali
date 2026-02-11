@extends('layouts.admin')

@section('title', 'Refil Selesai - Admin')

@section('content')
<div class="container-fluid px-0 px-md-3">
    <!-- Header Section -->
    <div class="card shadow-sm mb-3 border-0 bg-success text-white">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-lg me-3"></i>
                    <div>
                        <h4 class="mb-0">Refil Selesai</h4>
                        <small class="opacity-75">Total: {{ $refils->total() }} Refil</small>
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
        <div class="card-body p-3">
            <form class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-0">Tahun</label>
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
                    <label class="form-label small text-muted mb-0">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm">
                        <option value="">Semua Bulan</option>
                        @foreach($bulan as $key => $namaBulan)
                            <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                {{ $namaBulan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-0">Penangan</label>
                    <select name="penangan_id" class="form-select form-select-sm">
                        <option value="">Semua Penangan</option>
                        @foreach($penanganList as $penangan)
                            <option value="{{ $penangan->id }}" {{ request('penangan_id') == $penangan->id ? 'selected' : '' }}>
                                {{ $penangan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-0">Jenis Layanan</label>
                    <select name="jenis_layanan" class="form-select form-select-sm">
                        <option value="">Semua Layanan</option>
                        <option value="Refil" {{ request('jenis_layanan') == 'Refil' ? 'selected' : '' }}>Refil</option>
                        <option value="Komplain" {{ request('jenis_layanan') == 'Komplain' ? 'selected' : '' }}>Komplain</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-0">Pencarian</label>
                    <div class="input-group">
                        <input type="text" name="cari" class="form-control form-control-sm" 
                               placeholder="Cari pelanggan/kartrid/alamat..." 
                               value="{{ request('cari') }}">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    @if(request()->anyFilled(['tahun', 'bulan', 'penangan_id', 'jenis_layanan', 'cari']))
                    <a href="{{ route('admin.refil-selesai') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Reset Filter
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Content Section -->
    @if($refils->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada refil selesai</h5>
            @if(request()->anyFilled(['tahun', 'bulan', 'penangan_id', 'jenis_layanan', 'cari']))
            <p class="text-muted">Silakan coba dengan filter yang berbeda</p>
            @endif
        </div>
    </div>
    @else
    <!-- Grouped by Year/Month/Layanan -->
    <div class="accordion" id="accordionRefil">
        @php
            $groupedByYear = $refils->groupBy(function($item) {
                return $item->tanggal_selesai->format('Y');
            })->sortKeysDesc();
        @endphp

        @foreach($groupedByYear as $year => $yearRefils)
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white p-0">
                <h2 class="mb-0">
                    <button class="btn btn-block text-left px-4 py-3 d-flex justify-content-between align-items-center collapsed" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapseYear{{ $year }}"
                            aria-expanded="false">
                        <span class="h5 mb-0 text-success">
                            <i class="fas fa-calendar-alt me-2"></i>Tahun {{ $year }}
                        </span>
                        <span class="badge bg-success rounded-pill">
                            {{ $yearRefils->count() }} Refil
                        </span>
                    </button>
                </h2>
            </div>

            <div id="collapseYear{{ $year }}" class="collapse" data-parent="#accordionRefil">
                <div class="card-body p-0">
                    <div class="accordion" id="accordionMonth{{ $year }}">
                        @php
                            $groupedByMonth = $yearRefils->groupBy(function($item) {
                                return $item->tanggal_selesai->format('m');
                            })->sortKeysDesc();
                        @endphp

                        @foreach($groupedByMonth as $month => $monthRefils)
                        @php
                            $monthName = $bulan[(int)$month];
                            $monthId = $year . $month;
                        @endphp
                        <div class="card border-0">
                            <div class="card-header bg-light p-0">
                                <h2 class="mb-0">
                                    <button class="btn btn-block text-left px-4 py-2 d-flex justify-content-between align-items-center collapsed" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapseMonth{{ $monthId }}"
                                            aria-expanded="false">
                                        <span class="fw-medium">
                                            <i class="fas fa-calendar me-2"></i>{{ $monthName }}
                                        </span>
                                        <span class="badge bg-success-subtle text-success rounded-pill">
                                            {{ $monthRefils->count() }} Refil
                                        </span>
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseMonth{{ $monthId }}" class="collapse" data-parent="#accordionMonth{{ $year }}">
                                <div class="card-body p-0">
                                    <!-- Group by Layanan -->
                                    <div class="accordion" id="accordionLayanan{{ $monthId }}">
                                        @php
                                            $groupedByLayanan = $monthRefils->groupBy('jenis_layanan');
                                        @endphp

                                        @foreach($groupedByLayanan as $layanan => $layananRefils)
                                        @php
                                            $layananId = $monthId . $layanan;
                                            $badgeColor = $layanan == 'Refil' ? 'success' : 'danger';
                                        @endphp
                                        <div class="card border-0">
                                            <div class="card-header bg-light p-0">
                                                <h2 class="mb-0">
                                                    <button class="btn btn-block text-left px-4 py-2 d-flex justify-content-between align-items-center collapsed" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#collapseLayanan{{ $layananId }}"
                                                            aria-expanded="false">
                                                        <span class="fw-medium">
                                                            <i class="fas fa-{{ $layanan == 'Refil' ? 'fill-drip' : 'tools' }} me-2"></i>{{ $layanan }}
                                                        </span>
                                                        <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} rounded-pill">
                                                            {{ $layananRefils->count() }}
                                                        </span>
                                                    </button>
                                                </h2>
                                            </div>

                                            <div id="collapseLayanan{{ $layananId }}" class="collapse" data-parent="#accordionLayanan{{ $monthId }}">
                                                <div class="card-body p-0">
                                                    <!-- Desktop Table View -->
                                                    <div class="d-none d-md-block">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th width="5%">No</th>
                                                                        <th width="10%">Tgl Masuk</th>
                                                                        <th width="15%">Pelanggan</th>
                                                                        <th width="10%">Telepon</th>
                                                                        <th width="10%">Layanan</th>
                                                                        <th width="12%">Kartrid</th>
                                                                        <th width="12%">Penangan</th>
                                                                        <th width="10%">Sparepart</th>
                                                                        <th width="10%">Tgl Selesai</th>
                                                                        <th width="6%">Foto</th>
                                                                        <th width="10%">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($layananRefils as $refil)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $refil->tanggal_masuk->format('d/m/Y') }}</td>
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2" style="width:28px;height:28px;line-height:28px;">
                                                                                    {{ strtoupper(substr($refil->nama_pelanggan, 0, 1)) }}
                                                                                </div>
                                                                                <span>{{ $refil->nama_pelanggan }}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ $refil->no_telepon }}</td>
                                                                        <td>
                                                                            <span class="badge bg-{{ $refil->jenis_layanan == 'Refil' ? 'success' : 'danger' }}-subtle text-{{ $refil->jenis_layanan == 'Refil' ? 'success' : 'danger' }}">
                                                                                {{ $refil->jenis_layanan }}
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ $refil->jenis_kartrid }}</td>
                                                                        <td>
                                                                            @if($refil->penangan)
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle me-2" style="width:28px;height:28px;line-height:28px;">
                                                                                        {{ strtoupper(substr($refil->penangan->name, 0, 1)) }}
                                                                                    </div>
                                                                                    <span>{{ $refil->penangan->name }}</span>
                                                                                </div>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $refil->sparepart ?? '-' }}</td>
                                                                        <td>{{ $refil->tanggal_selesai->format('d/m/Y H:i') }}</td>
                                                                        <td>
                                                                            @if($refil->foto_kartrid)
                                                                            <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.refil-selesai.image', $refil->foto_kartrid) }}', 'Foto Kartrid {{ $refil->jenis_kartrid }}')">
                                                                                <img src="{{ route('admin.refil-selesai.image', $refil->foto_kartrid) }}" 
                                                                                     class="rounded border cursor-zoom" 
                                                                                     style="width:40px;height:40px;object-fit:cover">
                                                                            </a>
                                                                            @else
                                                                            <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <form class="toggle-verifikasi-form" method="POST" action="{{ route('admin.refil-selesai.toggle-verifikasi', $refil->id) }}">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-sm {{ $refil->verifikasi ? 'btn-success' : 'btn-outline-danger' }}" 
                                                                                        title="{{ $refil->verifikasi ? 'Terverifikasi' : 'Belum Verifikasi' }}">
                                                                                    <i class="fas fa-check"></i>
                                                                                </button>
                                                                            </form>
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
                                                            @foreach($layananRefils as $refil)
                                                            <div class="list-group-item border-0 py-3">
                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar bg-success bg-opacity-10 text-success rounded-circle me-2" style="width:36px;height:36px;line-height:36px;">
                                                                            {{ strtoupper(substr($refil->nama_pelanggan, 0, 1)) }}
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-0">{{ $refil->nama_pelanggan }}</h6>
                                                                            <small class="text-muted">{{ $refil->no_telepon }}</small>
                                                                        </div>
                                                                    </div>
                                                                    <span class="badge bg-{{ $layanan == 'Refil' ? 'success' : 'danger' }}-subtle text-{{ $layanan == 'Refil' ? 'success' : 'danger' }} rounded-pill">
                                                                        {{ $layanan }}
                                                                    </span>
                                                                </div>

                                                                @if($refil->foto_kartrid)
                                                                <div class="text-center mb-2">
                                                                    <a href="javascript:void(0)" onclick="showImageModal('{{ route('admin.refil-selesai.image', $refil->foto_kartrid) }}', 'Foto Kartrid {{ $refil->jenis_kartrid }}')">
                                                                        <img src="{{ route('admin.refil-selesai.image', $refil->foto_kartrid) }}" 
                                                                             class="img-fluid rounded border cursor-zoom" 
                                                                             style="max-height: 120px;">
                                                                    </a>
                                                                </div>
                                                                @endif

                                                                <div class="row g-2">
                                                                    <div class="col-6">
                                                                        <div class="text-muted small">Kartrid</div>
                                                                        <div>{{ $refil->jenis_kartrid }}</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="text-muted small">Penangan</div>
                                                                        <div>{{ $refil->penangan ? $refil->penangan->name : '-' }}</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="text-muted small">Tgl Masuk</div>
                                                                        <div>{{ $refil->tanggal_masuk->format('d/m/Y') }}</div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="text-muted small">Tgl Selesai</div>
                                                                        <div>{{ $refil->tanggal_selesai->format('d/m/Y H:i') }}</div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="text-muted small">Sparepart</div>
                                                                        <div>{{ $refil->sparepart ?? '-' }}</div>
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex justify-content-end mt-2">
                                                                    <form class="toggle-verifikasi-form" method="POST" action="{{ route('admin.refil-selesai.toggle-verifikasi', $refil->id) }}">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm {{ $refil->verifikasi ? 'btn-success' : 'btn-outline-danger' }}" 
                                                                                title="{{ $refil->verifikasi ? 'Terverifikasi' : 'Belum Verifikasi' }}">
                                                                            <i class="fas fa-check me-1"></i>
                                                                            {{ $refil->verifikasi ? 'Terverifikasi' : 'Verifikasi' }}
                                                                        </button>
                                                                    </form>
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

    <!-- Pagination -->
    @if($refils->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $refils->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
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
    
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1);
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
        border-left-color: var(--bs-success);
        background-color: rgba(25, 135, 84, 0.03);
    }
    
    .cursor-zoom {
        cursor: zoom-in;
        transition: transform 0.2s;
    }
    
    .cursor-zoom:hover {
        transform: scale(1.05);
    }
    
    .toggle-verifikasi-form button {
        transition: all 0.3s ease;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-width: 2px;
    }
    
    .toggle-verifikasi-form button.btn-success {
        background-color: #198754;
        border-color: #198754;
        color: white;
    }
    
    .toggle-verifikasi-form button.btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
        background-color: transparent;
    }
    
    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .toggle-verifikasi-form button {
            width: auto;
            padding: 0.25rem 0.75rem;
        }
    }

    /* Loading spinner style */
    .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
</style>

<script>
// Wait for DOM and all dependencies to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize image modal
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    
    // Function to show image in modal
    window.showImageModal = function(imageUrl, title) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModalTitle').textContent = title || 'Foto Kartrid';
        imageModal.show();
    };

    // Event listener for all zoomable images (fallback)
    document.querySelectorAll('img.cursor-zoom').forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            const parentLink = this.closest('a');
            if (parentLink && parentLink.onclick) return;
            showImageModal(this.src, 'Foto Kartrid');
        });
    });

    // Handle verification toggle form submission
    document.querySelectorAll('.toggle-verifikasi-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const button = this.querySelector('button');
            const originalHTML = button.innerHTML;
            
            // Show loading state
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
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
                    // Toggle button appearance
                    button.classList.toggle('btn-success');
                    button.classList.toggle('btn-outline-danger');
                    
                    // Update title attribute
                    const isVerified = button.classList.contains('btn-success');
                    button.setAttribute('title', isVerified ? 'Terverifikasi' : 'Belum Verifikasi');
                    
                    // Update button content
                    if (button.textContent.trim() !== '') {
                        // Mobile view with text
                        button.innerHTML = `<i class="fas fa-check me-1"></i> ${isVerified ? 'Terverifikasi' : 'Verifikasi'}`;
                    } else {
                        // Desktop view icon only
                        button.innerHTML = '<i class="fas fa-check"></i>';
                    }

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

    // Accordion state management - MODIFIED VERSION (all initially collapsed)
    const accordionState = {
        init: function() {
            // Set up event listeners for accordion buttons
            document.querySelectorAll('.accordion-button').forEach(button => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-bs-target');
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    
                    // Save state for all accordion levels
                    if (target) {
                        localStorage.setItem(target, isExpanded ? 'false' : 'true');
                    }
                });
            });

            // Restore accordion state from localStorage
            document.querySelectorAll('.accordion-collapse').forEach(collapse => {
                const id = '#' + collapse.id;
                const storedState = localStorage.getItem(id);
                const button = document.querySelector(`[data-bs-target="${id}"]`);
                
                if (storedState === 'true' && button) {
                    button.classList.remove('collapsed');
                    button.setAttribute('aria-expanded', 'true');
                    collapse.classList.add('show');
                }
            });

            // Auto close other year accordions when one is opened
            document.querySelectorAll('[data-bs-target^="#collapseYear"]').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-bs-target');
                    
                    // Close other year accordions
                    document.querySelectorAll('[data-bs-target^="#collapseYear"]').forEach(otherButton => {
                        if (otherButton !== this) {
                            const otherTarget = otherButton.getAttribute('data-bs-target');
                            const otherCollapse = document.querySelector(otherTarget);
                            const bsCollapse = bootstrap.Collapse.getInstance(otherCollapse);
                            
                            if (bsCollapse && otherCollapse.classList.contains('show')) {
                                bsCollapse.hide();
                                otherButton.classList.add('collapsed');
                                otherButton.setAttribute('aria-expanded', 'false');
                                localStorage.setItem(otherTarget, 'false');
                            }
                        }
                    });
                });
            });
        }
    };

    // Initialize accordion state management
    if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
        accordionState.init();
    } else {
        console.warn('Bootstrap Collapse not loaded');
    }
});
</script>
@endsection