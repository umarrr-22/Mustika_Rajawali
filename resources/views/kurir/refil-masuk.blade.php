@extends('layouts.kurir')

@section('title', 'Refil Masuk - Mustika Rajawali')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Form Section -->
        <div class="col-12 col-md-4 mb-4 order-1 order-md-1">
            <div class="card shadow-sm h-100">
                <div class="card-header {{ $refilEdit ? 'bg-warning text-dark' : 'bg-primary text-white' }}">
                    <h5 class="mb-0">
                        <i class="fas {{ $refilEdit ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i>
                        {{ $refilEdit ? 'Edit Data Refil' : 'Tambah Refil Baru' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form id="refilForm" action="{{ $refilEdit ? route('kurir.refil-masuk.update', $refilEdit->id) : route('kurir.refil-masuk.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($refilEdit) @method('PUT') @endif
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" 
                                   value="{{ $refilEdit ? $refilEdit->tanggal_masuk->format('Y-m-d') : old('tanggal_masuk', date('Y-m-d')) }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" class="form-control" 
                                   value="{{ $refilEdit ? $refilEdit->nama_pelanggan : old('nama_pelanggan') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" 
                                   value="{{ $refilEdit ? $refilEdit->no_telepon : old('no_telepon') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" required>{{ $refilEdit ? $refilEdit->alamat : old('alamat') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jenis Layanan</label>
                            <select name="jenis_layanan" class="form-select" required>
                                <option value="Refil" {{ ($refilEdit && $refilEdit->jenis_layanan == 'Refil') || old('jenis_layanan') == 'Refil' ? 'selected' : '' }}>Refil</option>
                                <option value="Komplain" {{ ($refilEdit && $refilEdit->jenis_layanan == 'Komplain') || old('jenis_layanan') == 'Komplain' ? 'selected' : '' }}>Komplain</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jenis Kartrid</label>
                            <input type="text" name="jenis_kartrid" class="form-control" 
                                   value="{{ $refilEdit ? $refilEdit->jenis_kartrid : old('jenis_kartrid') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Detail Kerusakan</label>
                            <textarea name="kerusakan" class="form-control" rows="3" required>{{ $refilEdit ? $refilEdit->kerusakan : old('kerusakan') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Foto Kartrid</label>
                            
                            <input type="file" name="foto_kartrid" id="foto_kartrid" class="form-control d-none" accept="image/*">
                            
                            <div class="btn-group w-100 mb-2">
                                <button type="button" class="btn btn-primary" id="btn-kamera">
                                    <i class="fas fa-camera me-1"></i> Ambil Foto
                                </button>
                                <button type="button" class="btn btn-secondary" id="btn-file">
                                    <i class="fas fa-folder-open me-1"></i> Pilih dari Galeri
                                </button>
                            </div>
                            
                            <div id="preview-container" class="text-center mb-2" style="display: none;">
                                <img id="preview-image" src="#" alt="Preview Foto" class="img-thumbnail" style="max-height: 150px; display: none;">
                                
                                <div id="webcam-ui" style="display: none;">
                                    <div class="position-relative">
                                        <video id="webcam-stream" autoplay playsinline class="img-thumbnail" style="max-height: 300px;"></video>
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <button id="switch-camera" class="btn btn-sm btn-dark" title="Ganti Kamera">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-center">
                                        <button id="capture-btn" class="btn btn-success mx-1">
                                            <i class="fas fa-camera me-1"></i> Ambil Foto
                                        </button>
                                        <button id="retake-btn" class="btn btn-warning mx-1" style="display: none;">
                                            <i class="fas fa-redo me-1"></i> Ambil Ulang
                                        </button>
                                        <button id="cancel-webcam" class="btn btn-danger mx-1">
                                            <i class="fas fa-times me-1"></i> Batal
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">Pastikan foto jelas dan kartrid terlihat utuh</small>
                                    </div>
                                </div>
                                
                                <button id="hapus-preview" class="btn btn-sm btn-danger mt-2" style="display: none;">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                            
                            @if($refilEdit && $refilEdit->foto_kartrid)
                                <div class="mt-2">
                                    @if(Storage::disk('public')->exists('refil-images/'.$refilEdit->foto_kartrid))
                                        <a href="{{ route('kurir.refil-masuk.image', $refilEdit->foto_kartrid) }}" target="_blank" data-lightbox="refil-image">
                                            <img src="{{ route('kurir.refil-masuk.image', $refilEdit->foto_kartrid) }}" 
                                                 alt="Foto Kartrid" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 150px;">
                                        </a>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="hapus_foto" id="hapus_foto">
                                            <label class="form-check-label" for="hapus_foto">
                                                Hapus foto saat update
                                            </label>
                                        </div>
                                    @else
                                        <span class="text-danger">File tidak ditemukan (refil-images/{{ $refilEdit->foto_kartrid }})</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" id="submit-btn" class="btn {{ $refilEdit ? 'btn-warning' : 'btn-primary' }}">
                                <i class="fas fa-save me-1"></i> {{ $refilEdit ? 'Update' : 'Simpan' }}
                            </button>
                            @if($refilEdit)
                                <a href="{{ route('kurir.refil-masuk') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- List Section -->
        <div class="col-12 col-md-8 order-2 order-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i> Daftar Refil Masuk</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('kurir.refil-masuk') }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari refil..." value="{{ $search }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            @if($search)
                                <a href="{{ route('kurir.refil-masuk') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="3%">No</th>
                                    <th width="8%">Tanggal</th>
                                    <th width="12%">Pelanggan</th>
                                    <th width="10%">Telepon</th>
                                    <th width="12%">Alamat</th>
                                    <th width="8%">Layanan</th>
                                    <th width="10%">Kartrid</th>
                                    <th width="10%">Foto</th>
                                    <th width="10%">Detail Kerusakan</th>
                                    <th width="12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($refils as $refil)
                                <tr id="refil-{{ $refil->id }}">
                                    <td>{{ ($refils->currentPage() - 1) * $refils->perPage() + $loop->iteration }}</td>
                                    <td>{{ $refil->tanggal_masuk->format('d/m/Y') }}</td>
                                    <td>{{ $refil->nama_pelanggan }}</td>
                                    <td>{{ $refil->no_telepon }}</td>
                                    <td>
                                        <span data-toggle="tooltip" title="{{ $refil->alamat }}">
                                            {{ Str::limit($refil->alamat, 20) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $refil->jenis_layanan == 'Refil' ? 'primary' : 'danger' }}">
                                            {{ $refil->jenis_layanan }}
                                        </span>
                                    </td>
                                    <td>{{ $refil->jenis_kartrid }}</td>
                                    <td style="padding: 5px; min-width: 100px;">
                                        @if($refil->foto_kartrid)
                                            @if(Storage::disk('public')->exists('refil-images/'.$refil->foto_kartrid))
                                                <a href="{{ route('kurir.refil-masuk.image', $refil->foto_kartrid) }}" 
                                                   target="_blank" 
                                                   data-lightbox="refil-image-{{ $refil->id }}"
                                                   data-title="Foto Kartrid {{ $refil->nama_pelanggan }}">
                                                    <img src="{{ route('kurir.refil-masuk.image', $refil->foto_kartrid) }}" 
                                                         alt="Foto Kartrid" 
                                                         class="img-fluid rounded"
                                                         style="max-height: 80px; width: auto; display: block; margin: 0 auto;">
                                                </a>
                                            @else
                                                <span class="badge bg-danger small" title="Path: refil-images/{{ $refil->foto_kartrid }}">
                                                    <i class="fas fa-exclamation-triangle me-1"></i> File Hilang
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span data-toggle="tooltip" title="{{ $refil->kerusakan }}">
                                            {{ Str::limit($refil->kerusakan, 30) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('kurir.refil-masuk') }}?edit={{ $refil->id }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('kurir.refil-masuk.destroy', $refil->id) }}" method="POST" class="delete-form">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('kurir.refil-masuk.kirim', $refil->id) }}" 
                                                  method="POST" 
                                                  class="kirim-refil-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-info" 
                                                        title="Kirim ke Teknisi">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data refil</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $refils->withQueryString()->links() }}
                        </div>
                    </div>

                    <div class="d-md-none">
                        @forelse($refils as $refil)
                        <div class="card mb-3 shadow-sm" id="refil-{{ $refil->id }}">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $refil->nama_pelanggan }}</h6>
                                    <span class="badge bg-secondary">Draft</span>
                                </div>
                                <div class="small text-muted">{{ $refil->tanggal_masuk->format('d/m/Y') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Telepon:</strong> {{ $refil->no_telepon }}</p>
                                        <p class="mb-1"><strong>Layanan:</strong> 
                                            <span class="badge bg-{{ $refil->jenis_layanan == 'Refil' ? 'primary' : 'danger' }}">
                                                {{ $refil->jenis_layanan }}
                                            </span>
                                        </p>
                                        <p class="mb-1"><strong>Kartrid:</strong> {{ $refil->jenis_kartrid }}</p>
                                    </div>
                                    <div class="col-6">
                                        @if($refil->foto_kartrid)
                                            @if(Storage::disk('public')->exists('refil-images/'.$refil->foto_kartrid))
                                                <a href="{{ route('kurir.refil-masuk.image', $refil->foto_kartrid) }}" 
                                                   target="_blank" 
                                                   data-lightbox="refil-image-{{ $refil->id }}"
                                                   data-title="Foto Kartrid {{ $refil->nama_pelanggan }}">
                                                    <img src="{{ route('kurir.refil-masuk.image', $refil->foto_kartrid) }}" 
                                                         alt="Foto Kartrid" 
                                                         class="img-fluid rounded"
                                                         style="max-height: 80px; width: auto; display: block; margin-left: auto;">
                                                </a>
                                            @else
                                                <span class="badge bg-danger small">
                                                    <i class="fas fa-exclamation-triangle me-1"></i> File Hilang
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <p class="mb-1"><strong>Alamat:</strong> {{ Str::limit($refil->alamat, 50) }}</p>
                                    <p class="mb-1"><strong>Kerusakan:</strong> {{ Str::limit($refil->kerusakan, 50) }}</p>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <div class="btn-group">
                                        <a href="{{ route('kurir.refil-masuk') }}?edit={{ $refil->id }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('kurir.refil-masuk.destroy', $refil->id) }}" method="POST" class="delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <form action="{{ route('kurir.refil-masuk.kirim', $refil->id) }}" 
                                          method="POST" 
                                          class="kirim-refil-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info" 
                                                title="Kirim ke Teknisi">
                                            <i class="fas fa-paper-plane me-1"></i> Kirim
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info text-center">
                            Tidak ada data refil
                        </div>
                        @endforelse
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $refils->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Lightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap Tooltip -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // ==================== VARIABLES ====================
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    let stream = null;
    let currentPhoto = null;
    let isManualSubmit = false;
    let facingMode = "environment"; // Default kamera belakang
    const fotoKartridInput = document.getElementById('foto_kartrid');
    const flashElement = document.createElement('div');
    flashElement.className = 'flash-effect';
    document.body.appendChild(flashElement);

    // ==================== CAMERA FUNCTIONALITY ====================
    // Initialize webcam
    async function initWebcam() {
        try {
            // Stop any existing stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            
            // Get video constraints based on device
            const constraints = {
                video: {
                    facingMode: facingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };
            
            // Add torch/flash support for mobile if available
            if (isMobile) {
                constraints.video.torch = true;
            }
            
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Show webcam UI
            document.getElementById('preview-container').style.display = 'block';
            document.getElementById('webcam-ui').style.display = 'block';
            document.getElementById('preview-image').style.display = 'none';
            document.getElementById('hapus-preview').style.display = 'none';
            
            const video = document.getElementById('webcam-stream');
            video.srcObject = stream;
            video.play();
            
            // Hide retake button initially
            document.getElementById('retake-btn').style.display = 'none';
            
        } catch (err) {
            console.error("Error accessing camera:", err);
            Swal.fire({
                title: 'Error',
                text: 'Tidak dapat mengakses kamera. Silakan gunakan upload file.',
                icon: 'error'
            });
            // Fallback to file selection
            document.getElementById('foto_kartrid').removeAttribute('capture');
            document.getElementById('foto_kartrid').click();
        }
    }

    // Switch between front and back camera
    document.getElementById('switch-camera').addEventListener('click', function(e) {
        e.preventDefault();
        facingMode = facingMode === "user" ? "environment" : "user";
        initWebcam();
    });

    // Flash effect
    function triggerFlash() {
        flashElement.style.display = 'block';
        flashElement.style.animation = 'none';
        void flashElement.offsetWidth; // Trigger reflow
        flashElement.style.animation = 'flash 300ms ease-out';
        
        setTimeout(() => {
            flashElement.style.display = 'none';
        }, 300);
    }

    // Capture from webcam
    function captureFromWebcam() {
        triggerFlash();
        
        const video = document.getElementById('webcam-stream');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        
        canvas.toBlob(function(blob) {
            currentPhoto = new File([blob], 'webcam-capture-' + new Date().getTime() + '.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });
            
            // Set file to input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(currentPhoto);
            fotoKartridInput.files = dataTransfer.files;
            
            // Show preview
            document.getElementById('preview-image').src = canvas.toDataURL('image/jpeg');
            document.getElementById('preview-image').style.display = 'block';
            document.getElementById('hapus-preview').style.display = 'block';
            document.getElementById('webcam-ui').style.display = 'none';
            
            // Show retake button
            document.getElementById('retake-btn').style.display = 'inline-block';
            
        }, 'image/jpeg', 0.9);
    }

    // Retake photo
    document.getElementById('retake-btn').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('preview-image').style.display = 'none';
        document.getElementById('hapus-preview').style.display = 'none';
        document.getElementById('webcam-ui').style.display = 'block';
        document.getElementById('retake-btn').style.display = 'none';
    });

    // Camera button click
    document.getElementById('btn-kamera').addEventListener('click', function(e) {
        e.preventDefault();
        if (isMobile) {
            fotoKartridInput.setAttribute('capture', 'environment');
            initWebcam();
        } else {
            initWebcam();
        }
    });

    // File button click
    document.getElementById('btn-file').addEventListener('click', function(e) {
        e.preventDefault();
        fotoKartridInput.removeAttribute('capture');
        fotoKartridInput.click();
    });

    // Capture button
    document.getElementById('capture-btn').addEventListener('click', function(e) {
        e.preventDefault();
        captureFromWebcam();
    });

    // Cancel webcam
    document.getElementById('cancel-webcam').addEventListener('click', function(e) {
        e.preventDefault();
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        document.getElementById('webcam-ui').style.display = 'none';
        document.getElementById('preview-container').style.display = 'none';
        document.getElementById('retake-btn').style.display = 'none';
    });

    // File input change
    fotoKartridInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            currentPhoto = this.files[0];
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('preview-container').style.display = 'block';
                document.getElementById('preview-image').src = event.target.result;
                document.getElementById('preview-image').style.display = 'block';
                document.getElementById('hapus-preview').style.display = 'block';
                document.getElementById('webcam-ui').style.display = 'none';
                document.getElementById('retake-btn').style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Delete preview
    document.getElementById('hapus-preview').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Foto?',
            text: 'Anda yakin ingin menghapus foto yang sudah diambil?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('preview-image').src = '#';
                document.getElementById('preview-image').style.display = 'none';
                document.getElementById('hapus-preview').style.display = 'none';
                document.getElementById('preview-container').style.display = 'none';
                document.getElementById('retake-btn').style.display = 'none';
                fotoKartridInput.value = '';
                currentPhoto = null;
                
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            }
        });
    });

    // ==================== FORM HANDLING ====================
    // Submit button click
    document.getElementById('submit-btn').addEventListener('click', function(e) {
        isManualSubmit = true;
        
        // Validate form
        const requiredFields = ['tanggal_masuk', 'nama_pelanggan', 'no_telepon', 'alamat', 'jenis_layanan', 'jenis_kartrid', 'kerusakan'];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const element = document.querySelector(`[name="${field}"]`);
            if (!element.value) {
                isValid = false;
                element.classList.add('is-invalid');
            } else {
                element.classList.remove('is-invalid');
            }
        });
        
        // Check if photo exists
        if (!fotoKartridInput.files.length && !{{ $refilEdit && $refilEdit->foto_kartrid ? 'true' : 'false' }}) {
            isValid = false;
            Swal.fire({
                title: 'Foto Kartrid Diperlukan',
                text: 'Silakan ambil atau unggah foto kartrid terlebih dahulu',
                icon: 'warning'
            });
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });
    
    // Form submission
    document.getElementById('refilForm').addEventListener('submit', function(e) {
        if (!isManualSubmit) {
            e.preventDefault();
            return false;
        }
    });
    
    // ==================== OTHER FUNCTIONALITY ====================
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Lightbox configuration
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'disableScrolling': true,
        'albumLabel': 'Gambar %1 dari %2',
        'fadeDuration': 300,
        'imageFadeDuration': 300,
        'showImageNumberLabel': true
    });
    
    // Handle kirim refil
    $('body').on('submit', '.kirim-refil-form', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        const refilName = form.closest('tr, .card').find('td:nth-child(3), h6.mb-0').text().trim();
        
        Swal.fire({
            title: 'Kirim Refil ke Teknisi!',
            html: `Anda akan mengirim refil untuk pelanggan: <strong>${refilName}</strong><br><br>
                  Data akan dikirim dan status berubah menjadi 'Menunggu'. Anda tidak bisa mengeditnya lagi.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim Sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mengirim...',
                    html: 'Sedang mengirim data refil ke teknisi',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() }
                });
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Terkirim!',
                                html: `Refil untuk <strong>${refilName}</strong> berhasil dikirim ke teknisi`,
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                willClose: () => { window.location.reload(); }
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Gagal!',
                            html: `Gagal mengirim refil untuk <strong>${refilName}</strong><br><br>
                                  ${xhr.responseJSON?.message || 'Terjadi kesalahan saat mengirim refil'}`,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
    
    // Handle delete confirmation
    $('body').on('submit', '.delete-form', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const refilName = form.closest('tr, .card').find('td:nth-child(3), h6.mb-0').text().trim();
        
        Swal.fire({
            title: 'Hapus Refil!',
            html: `Anda akan menghapus refil untuk pelanggan: <strong>${refilName}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    html: 'Sedang menghapus data refil',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        $.ajax({
                            url: form.attr('action'),
                            type: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Data refil berhasil dihapus',
                                    icon: 'success',
                                    timer: 1500,
                                    willClose: () => { window.location.reload(); }
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data', 'error');
                            }
                        });
                    }
                });
            }
        });
    });
});
</script>

<style>
    /* =============== CAMERA STYLES =============== */
    #webcam-ui {
        margin-bottom: 15px;
        background: #000;
        padding: 10px;
        border-radius: 8px;
    }
    
    #webcam-stream {
        width: 100%;
        max-height: 300px;
        border-radius: 5px;
        background: #000;
        display: block;
        margin: 0 auto;
    }
    
    #capture-btn, #cancel-webcam, #retake-btn {
        width: 150px;
        font-size: 14px;
    }
    
    #switch-camera {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Camera controls container */
    #webcam-ui > div:last-child {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #webcam-stream {
            max-height: 250px;
        }
        
        #capture-btn, #cancel-webcam, #retake-btn {
            width: 120px;
            padding: 5px 10px;
            font-size: 13px;
        }
    }
    
    /* Flash animation when capturing */
    @keyframes flash {
        0% { opacity: 0; }
        50% { opacity: 1; }
        100% { opacity: 0; }
    }
    
    .flash-effect {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        animation: flash 300ms ease-out;
    }
    
    /* =============== GENERAL STYLES =============== */
    .animate__animated {
        animation-duration: 1s;
    }

    .btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
    }

    .btn-danger {
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        transform: scale(1.1);
        box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
    }

    .img-thumbnail {
        padding: 0.25rem;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        max-width: 100%;
        height: auto;
        transition: transform 0.2s;
    }
    
    .img-thumbnail:hover {
        transform: scale(1.05);
    }
    
    .badge {
        font-size: 0.85em;
        font-weight: 500;
        padding: 5px 8px;
    }
    
    .table td, .table th {
        vertical-align: middle !important;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
    }

    /* Style untuk foto di tabel */
    .table td img.img-fluid {
        max-height: 80px;
        width: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        transition: transform 0.2s;
        cursor: zoom-in;
    }

    .table td img.img-fluid:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    /* Style untuk badge file hilang */
    .badge.bg-danger.small {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    /* Responsive table */
    @media (max-width: 991.98px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table td, .table th {
            white-space: nowrap;
        }
        
        .table td img.img-fluid {
            max-height: 60px;
        }
    }
</style>
@endsection