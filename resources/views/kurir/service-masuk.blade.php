@extends('layouts.kurir')

@section('title', 'Service Masuk - Mustika Rajawali')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Form Section -->
        <div class="col-12 col-md-4 mb-4 order-1 order-md-1">
            <div class="card shadow-sm h-100">
                <div class="card-header {{ $serviceEdit ? 'bg-warning text-dark' : 'bg-primary text-white' }}">
                    <h5 class="mb-0">
                        <i class="fas {{ $serviceEdit ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i>
                        {{ $serviceEdit ? 'Edit Data Service' : 'Tambah Service Baru' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form id="serviceForm" action="{{ $serviceEdit ? route('kurir.service-masuk.update', $serviceEdit->id) : route('kurir.service-masuk.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($serviceEdit) @method('PUT') @endif
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" 
                                   value="{{ $serviceEdit ? $serviceEdit->tanggal_masuk->format('Y-m-d') : old('tanggal_masuk', date('Y-m-d')) }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" class="form-control" 
                                   value="{{ $serviceEdit ? $serviceEdit->nama_pelanggan : old('nama_pelanggan') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" 
                                   value="{{ $serviceEdit ? $serviceEdit->no_telepon : old('no_telepon') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" required>{{ $serviceEdit ? $serviceEdit->alamat : old('alamat') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jenis Layanan</label>
                            <select name="jenis_layanan" class="form-select" required>
                                <option value="Service" {{ ($serviceEdit && $serviceEdit->jenis_layanan == 'Service') || old('jenis_layanan') == 'Service' ? 'selected' : '' }}>Service</option>
                                <option value="Komplain" {{ ($serviceEdit && $serviceEdit->jenis_layanan == 'Komplain') || old('jenis_layanan') == 'Komplain' ? 'selected' : '' }}>Komplain</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jenis Barang</label>
                            <input type="text" name="jenis_barang" class="form-control" 
                                   value="{{ $serviceEdit ? $serviceEdit->jenis_barang : old('jenis_barang') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Detail Kerusakan</label>
                            <textarea name="kerusakan" class="form-control" rows="3" required>{{ $serviceEdit ? $serviceEdit->kerusakan : old('kerusakan') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Foto Barang</label>
                            
                            <input type="file" name="foto_barang" id="foto_barang" class="form-control d-none" accept="image/*">
                            
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
                                        <small class="text-muted">Pastikan foto jelas dan barang terlihat utuh</small>
                                    </div>
                                </div>
                                
                                <button id="hapus-preview" class="btn btn-sm btn-danger mt-2" style="display: none;">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                            
                            @if($serviceEdit && $serviceEdit->foto_barang)
                                <div class="mt-2">
                                    @php
                                        $fileExists = Storage::disk('public')->exists('service_images/'.$serviceEdit->foto_barang);
                                    @endphp
                                    
                                    @if($fileExists)
                                        <a href="{{ route('kurir.service-masuk.image', $serviceEdit->foto_barang) }}" target="_blank" data-lightbox="service-image">
                                            <img src="{{ route('kurir.service-masuk.image', $serviceEdit->foto_barang) }}" 
                                                 alt="Foto Barang" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 150px;">
                                        </a>
                                    @else
                                        <span class="text-danger">File tidak ditemukan</span>
                                    @endif
                                    
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="hapus_foto" id="hapus_foto">
                                        <label class="form-check-label" for="hapus_foto">
                                            Hapus foto saat update
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" id="submit-btn" class="btn {{ $serviceEdit ? 'btn-warning' : 'btn-primary' }}">
                                <i class="fas fa-save me-1"></i> {{ $serviceEdit ? 'Update' : 'Simpan' }}
                            </button>
                            @if($serviceEdit)
                                <a href="{{ route('kurir.service-masuk') }}" class="btn btn-secondary">
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
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i> Daftar Service Masuk</h5>
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

                    <form method="GET" action="{{ route('kurir.service-masuk') }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari service..." value="{{ $search }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            @if($search)
                                <a href="{{ route('kurir.service-masuk') }}" class="btn btn-secondary">
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
                                    <th width="10%">Barang</th>
                                    <th width="10%">Foto</th>
                                    <th width="15%">Detail Kerusakan</th>
                                    <th width="12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                <tr id="service-{{ $service->id }}">
                                    <td>{{ ($services->currentPage() - 1) * $services->perPage() + $loop->iteration }}</td>
                                    <td>{{ $service->tanggal_masuk->format('d/m/Y') }}</td>
                                    <td>{{ $service->nama_pelanggan }}</td>
                                    <td>{{ $service->no_telepon }}</td>
                                    <td>
                                        <span data-toggle="tooltip" title="{{ $service->alamat }}">
                                            {{ Str::limit($service->alamat, 20) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $service->jenis_layanan == 'Service' ? 'primary' : 'danger' }}">
                                            {{ $service->jenis_layanan }}
                                        </span>
                                    </td>
                                    <td>{{ $service->jenis_barang }}</td>
                                    <td style="padding: 5px; min-width: 100px;">
                                        @if($service->foto_barang)
                                            @php
                                                $fileExists = Storage::disk('public')->exists('service_images/'.$service->foto_barang);
                                            @endphp
                                            
                                            @if($fileExists)
                                                <a href="{{ route('kurir.service-masuk.image', $service->foto_barang) }}" 
                                                   target="_blank" 
                                                   data-lightbox="service-image-{{ $service->id }}"
                                                   data-title="Foto Barang {{ $service->nama_pelanggan }}">
                                                    <img src="{{ route('kurir.service-masuk.image', $service->foto_barang) }}" 
                                                         alt="Foto Barang" 
                                                         class="img-fluid rounded"
                                                         style="max-height: 80px; width: auto; display: block; margin: 0 auto;">
                                                </a>
                                            @else
                                                <span class="text-danger small">File tidak ditemukan</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span data-toggle="tooltip" title="{{ $service->kerusakan }}">
                                            {{ Str::limit($service->kerusakan, 30) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('kurir.service-masuk') }}?edit={{ $service->id }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('kurir.service-masuk.destroy', $service->id) }}" method="POST" class="delete-form">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('kurir.service-masuk.kirim', $service->id) }}" 
                                                  method="POST" 
                                                  class="kirim-service-form">
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
                                    <td colspan="10" class="text-center">Tidak ada data service</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $services->withQueryString()->links() }}
                        </div>
                    </div>

                    <div class="d-md-none">
                        @forelse($services as $service)
                        <div class="card mb-3 shadow-sm" id="service-{{ $service->id }}">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $service->nama_pelanggan }}</h6>
                                    <span class="badge bg-secondary">Draft</span>
                                </div>
                                <div class="small text-muted">{{ $service->tanggal_masuk->format('d/m/Y') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Telepon:</strong> {{ $service->no_telepon }}</p>
                                        <p class="mb-1"><strong>Layanan:</strong> 
                                            <span class="badge bg-{{ $service->jenis_layanan == 'Service' ? 'primary' : 'danger' }}">
                                                {{ $service->jenis_layanan }}
                                            </span>
                                        </p>
                                        <p class="mb-1"><strong>Barang:</strong> {{ $service->jenis_barang }}</p>
                                    </div>
                                    <div class="col-6">
                                        @if($service->foto_barang)
                                            <a href="{{ route('kurir.service-masuk.image', $service->foto_barang) }}" 
                                               target="_blank" 
                                               data-lightbox="service-image-{{ $service->id }}"
                                               data-title="Foto Barang {{ $service->nama_pelanggan }}">
                                                <img src="{{ route('kurir.service-masuk.image', $service->foto_barang) }}" 
                                                     alt="Foto Barang" 
                                                     class="img-fluid rounded"
                                                     style="max-height: 80px; width: auto; display: block; margin-left: auto;">
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <p class="mb-1"><strong>Alamat:</strong> {{ Str::limit($service->alamat, 50) }}</p>
                                    <p class="mb-1"><strong>Kerusakan:</strong> {{ Str::limit($service->kerusakan, 50) }}</p>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <div class="btn-group">
                                        <a href="{{ route('kurir.service-masuk') }}?edit={{ $service->id }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('kurir.service-masuk.destroy', $service->id) }}" method="POST" class="delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <form action="{{ route('kurir.service-masuk.kirim', $service->id) }}" 
                                          method="POST" 
                                          class="kirim-service-form">
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
                            Tidak ada data service
                        </div>
                        @endforelse
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $services->links() }}
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
    const fotoBarangInput = document.getElementById('foto_barang');
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
            document.getElementById('foto_barang').removeAttribute('capture');
            document.getElementById('foto_barang').click();
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
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        canvas.toBlob(function(blob) {
            currentPhoto = new File([blob], 'webcam-capture-' + new Date().getTime() + '.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });
            
            // Set file to input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(currentPhoto);
            fotoBarangInput.files = dataTransfer.files;
            
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
            fotoBarangInput.setAttribute('capture', 'environment');
            initWebcam();
        } else {
            initWebcam();
        }
    });

    // File button click
    document.getElementById('btn-file').addEventListener('click', function(e) {
        e.preventDefault();
        fotoBarangInput.removeAttribute('capture');
        fotoBarangInput.click();
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
    fotoBarangInput.addEventListener('change', function(e) {
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
                fotoBarangInput.value = '';
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
        const requiredFields = ['tanggal_masuk', 'nama_pelanggan', 'no_telepon', 'alamat', 'jenis_layanan', 'jenis_barang', 'kerusakan'];
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
        if (!fotoBarangInput.files.length) {
            isValid = false;
            Swal.fire({
                title: 'Foto Barang Diperlukan',
                text: 'Silakan ambil atau unggah foto barang terlebih dahulu',
                icon: 'warning'
            });
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });
    
    // Form submission
    document.getElementById('serviceForm').addEventListener('submit', function(e) {
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
        'albumLabel': 'Gambar %1 dari %2'
    });
    
    // Handle kirim service
    $('body').on('submit', '.kirim-service-form', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        const serviceName = form.closest('tr, .card').find('td:nth-child(3), h6.mb-0').text().trim();
        
        Swal.fire({
            title: 'Kirim Service ke Teknisi!',
            html: `Anda akan mengirim service untuk pelanggan: <strong>${serviceName}</strong><br><br>
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
                    html: 'Sedang mengirim data service ke teknisi',
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
                                html: `Service untuk <strong>${serviceName}</strong> berhasil dikirim ke teknisi`,
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
                            html: `Gagal mengirim service untuk <strong>${serviceName}</strong><br><br>
                                  ${xhr.responseJSON?.message || 'Terjadi kesalahan saat mengirim service'}`,
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
        const serviceName = form.closest('tr, .card').find('td:nth-child(3), h6.mb-0').text().trim();
        
        Swal.fire({
            title: 'Hapus Service!',
            html: `Anda akan menghapus service untuk pelanggan: <strong>${serviceName}</strong>`,
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
                    html: 'Sedang menghapus data service',
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
                                    text: 'Data service berhasil dihapus',
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
</style>
@endsection