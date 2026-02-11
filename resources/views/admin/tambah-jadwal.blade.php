@extends('layouts.admin')

@section('title', 'Tambah Jadwal Kurir')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Form Section -->
        <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>
                        @isset($jadwal) Edit Jadwal Kurir @else Buat Jadwal Kurir @endisset
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($jadwal) ? route('admin.tambah-jadwal.update', $jadwal->id) : route('admin.tambah-jadwal.store') }}" method="POST">
                        @csrf
                        @isset($jadwal) @method('PUT') @endisset

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="{{ old('tanggal', isset($jadwal) ? $jadwal->tanggal->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi Tujuan</label>
                            <input type="text" name="lokasi_tujuan" class="form-control"
                                   placeholder="Contoh: Gudang Pusat" 
                                   value="{{ old('lokasi_tujuan', $jadwal->lokasi_tujuan ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', $jadwal->alamat ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Daerah</label>
                            <select name="daerah" class="form-select" required>
                                <option value="">Pilih Daerah</option>
                                @foreach(['Semarang Barat', 'Semarang Timur', 'Semarang Kota', 'Ungaran'] as $daerah)
                                    <option value="{{ $daerah }}" 
                                        {{ (old('daerah', $jadwal->daerah ?? '') == $daerah ? 'selected' : '') }}>
                                        {{ $daerah }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Keperluan</label>
                            <textarea name="keperluan" class="form-control" rows="3" 
                                      placeholder="Deskripsi pengiriman" required>{{ old('keperluan', $jadwal->keperluan ?? '') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-1"></i> 
                                @isset($jadwal) Simpan Perubahan @else Simpan Jadwal @endisset
                            </button>
                            
                            @isset($jadwal)
                            <a href="{{ route('admin.tambah-jadwal') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            @endisset
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Jadwal -->
        <div class="col-lg-6 col-md-12">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Jadwal Kurir</h5>
                        <span class="badge bg-light text-dark rounded-pill">{{ $jadwals->count() }} Jadwal</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success') && !str_contains(session('success'), 'dikirim'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Mobile View - Card Layout -->
                    <div class="d-block d-md-none">
                        @forelse($jadwals as $item)
                        <div class="card mb-3 {{ isset($jadwal) && $jadwal->id == $item->id ? 'border-warning border-2' : '' }}">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-secondary me-2">{{ $loop->iteration }}</span>
                                    <strong>{{ $item->lokasi_tujuan }}</strong>
                                </div>
                                <span class="badge bg-{{ $item->warna_daerah }} rounded-pill">{{ $item->daerah }}</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Tanggal:</small>
                                        <div>{{ $item->tanggal->format('d/m/Y') }}</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Daerah:</small>
                                        <div>{{ $item->daerah }}</div>
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <small class="text-muted">Alamat:</small>
                                    <div class="text-break">{{ $item->alamat }}</div>
                                </div>
                                
                                <div class="mb-2">
                                    <small class="text-muted">Keperluan:</small>
                                    <div>{{ $item->keperluan }}</div>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-1 mt-2">
                                    <a href="{{ route('admin.tambah-jadwal.edit', $item->id) }}" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-sm btn-success" 
                                            data-send-route="{{ route('admin.tambah-jadwal.kirim', $item->id) }}" 
                                            title="Kirim ke Kurir">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                    
                                    <form action="{{ route('admin.tambah-jadwal.destroy', $item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Hapus jadwal ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                <p>Tidak ada jadwal tersedia</p>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Desktop View - Full Table -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Lokasi</th>
                                        <th scope="col">Alamat</th>
                                        <th scope="col">Daerah</th>
                                        <th scope="col">Keperluan</th>
                                        <th scope="col" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwals as $item)
                                    <tr class="{{ isset($jadwal) && $jadwal->id == $item->id ? 'table-warning' : '' }}">
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                                        <td>{{ Str::limit($item->lokasi_tujuan, 15) }}</td>
                                        <td>{{ Str::limit($item->alamat, 30) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item->warna_daerah }} rounded-pill">
                                                {{ $item->daerah }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($item->keperluan, 30) }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('admin.tambah-jadwal.edit', $item->id) }}" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        data-send-route="{{ route('admin.tambah-jadwal.kirim', $item->id) }}" 
                                                        title="Kirim ke Kurir">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                                
                                                <form action="{{ route('admin.tambah-jadwal.destroy', $item->id) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Hapus jadwal ini?')" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                                <p>Tidak ada jadwal tersedia</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if($jadwals instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $jadwals->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Kirim -->
<div class="modal fade" id="confirmSendModal" tabindex="-1" aria-labelledby="confirmSendModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 8px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15); background-color: white;">
            <div class="modal-header" style="border-bottom: none; padding: 20px 20px 0;">
                <h5 class="modal-title text-center w-100 fw-bold" style="font-size: 1.3rem; color: #333;">
                    Kirim Jadwal ke Kurir?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding: 15px 30px;">
                <div class="mb-3" style="font-size: 5rem; color: #ff6b35; line-height: 1;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <p style="font-size: 1rem; color: #666; margin-bottom: 25px;">
                    Jadwal yang sudah dikirim tidak dapat diubah kembali
                </p>
                
                <div class="d-flex justify-content-center gap-3" style="margin-top: 20px;">
                    <button type="button" class="btn btn-danger px-4 py-2" 
                            style="min-width: 100px; border-radius: 6px; font-weight: 500;"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <form id="sendForm" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary px-4 py-2"
                                style="min-width: 100px; border-radius: 6px; font-weight: 500;">
                            Kirim
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses Kirim -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg text-center py-4">
            <div class="modal-body">
                <div class="checkmark-animation mb-3">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                <h5 class="fw-bold text-success mb-2">Berhasil Dikirim!</h5>
                <p class="text-muted small">Jadwal telah dikirim ke kurir</p>
                <button type="button" class="btn btn-success rounded-pill px-4 mt-3" data-bs-dismiss="modal">
                    <i class="fas fa-check me-1"></i> Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Gaya khusus untuk modal konfirmasi */
    #confirmSendModal .modal-content {
        max-width: 400px;
        margin: 0 auto;
    }
    
    /* Animasi checkmark */
    .checkmark-animation {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
    }
    
    .checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #198754;
        fill: none;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    
    .checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        stroke-width: 2;
        stroke: #198754;
        stroke-miterlimit: 10;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }
    
    @keyframes stroke {
        100% { stroke-dashoffset: 0; }
    }

    /* Efek hover untuk tombol */
    #confirmSendModal .btn-primary:hover {
        background-color: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
    }
    
    #confirmSendModal .btn-danger:hover {
        background-color: #bb2d3b;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
    }
    
    /* Transisi halus */
    #confirmSendModal .btn {
        transition: all 0.3s ease;
    }

    /* Card styling */
    .card {
        border-radius: 0.5rem;
        transition: transform 0.2s;
    }
    
    .card-header {
        border-top-left-radius: 0.5rem !important;
        border-top-right-radius: 0.5rem !important;
    }
    
    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }
    
    .badge.bg-primary {
        background-color: #0d6efd !important;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    
    .badge.bg-success {
        background-color: #198754 !important;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    .badge.bg-secondary {
        background-color: #6c757d !important;
    }
    
    .badge.me-2 {
        margin-right: 0.5rem !important;
        min-width: 24px;
        display: inline-flex;
        justify-content: center;
    }
    
    .rounded-pill {
        border-radius: 50rem !important;
    }
    
    /* Table styling */
    .table {
        font-size: 0.9rem;
    }
    
    .table>:not(caption)>*>* {
        padding: 0.75rem 0.5rem;
    }
    
    /* Button spacing */
    .d-flex.gap-1 {
        gap: 0.25rem !important;
    }
    
    /* Action buttons consistent size */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }
    
    /* Mobile card view */
    @media (max-width: 767.98px) {
        .card.mb-3 {
            margin-bottom: 1rem !important;
            border: 1px solid #e0e0e0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .card.mb-3.border-warning {
            border-color: #ffc107;
            border-width: 2px !important;
        }
        
        .card-header.bg-light {
            background-color: #f8f9fa!important;
        }
        
        .text-break {
            word-break: break-word;
            overflow-wrap: break-word;
        }
        
        small.text-muted {
            font-size: 0.75rem;
            color: #6c757d!important;
            display: block;
            margin-bottom: 0.25rem;
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .form-control, .form-select {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
        
        textarea.form-control {
            min-height: 100px;
        }
    }
    
    /* Animation for alerts */
    .alert {
        animation: fadeIn 0.5s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Handle send confirmation with animation
    const sendButtons = document.querySelectorAll('[data-send-route]');
    const confirmSendModal = new bootstrap.Modal(document.getElementById('confirmSendModal'));
    const sendForm = document.getElementById('sendForm');
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));

    sendButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const route = this.getAttribute('data-send-route');
            sendForm.setAttribute('action', route);
            confirmSendModal.show();
        });
    });

    // Show success modal with animation if session has success message about sending
    @if(session('success') && str_contains(session('success'), 'dikirim'))
        setTimeout(() => {
            successModal.show();
        }, 300);
    @endif
    
    // Play sound when showing success modal
    successModal._element.addEventListener('show.bs.modal', function() {
        // Uncomment jika ingin ada sound effect
        // const audio = new Audio('{{ asset("sounds/success-notification.mp3") }}');
        // audio.play().catch(e => console.log("Audio play failed:", e));
    });
});
</script>
@endsection