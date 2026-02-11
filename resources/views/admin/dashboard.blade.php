@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div>
            <span class="badge bg-primary text-white">
                <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Jadwal Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Jadwal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['jadwal']['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress progress-sm mb-2">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                style="width: {{ $stats['jadwal']['total'] ? ($stats['jadwal']['completed']/$stats['jadwal']['total'])*100 : 0 }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">
                                <i class="fas fa-check-circle me-1 text-success"></i> 
                                {{ $stats['jadwal']['completed'] }} Selesai
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-calendar-day me-1 text-info"></i> 
                                {{ $stats['jadwal']['today'] }} Hari Ini
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Service</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['service']['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress progress-sm mb-2">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: {{ $stats['service']['total'] ? ($stats['service']['completed']/$stats['service']['total'])*100 : 0 }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">
                                <i class="fas fa-clock me-1 text-warning"></i> 
                                {{ $stats['service']['pending'] }} Diproses
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-check-circle me-1 text-success"></i> 
                                {{ $stats['service']['completed'] }} Selesai
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refil Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Refil</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['refil']['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tint fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress progress-sm mb-2">
                            <div class="progress-bar bg-info" role="progressbar" 
                                style="width: {{ $stats['refil']['total'] ? ($stats['refil']['completed']/$stats['refil']['total'])*100 : 0 }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">
                                <i class="fas fa-clock me-1 text-warning"></i> 
                                {{ $stats['refil']['pending'] }} Diproses
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-check-circle me-1 text-success"></i> 
                                {{ $stats['refil']['completed'] }} Selesai
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Pengguna</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['users']['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">
                                <i class="fas fa-user-plus me-1 text-primary"></i> 
                                {{ $stats['users']['new_this_month'] }} Bulan Ini
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Service Activity Chart -->
        <div class="col-lg-6">
            <div class="card shadow mb-4 chart-card">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Aktivitas Service 12 Bulan Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="serviceChart"></canvas>
                    </div>
                    <div class="text-center mt-3 small text-muted">
                        Total: <strong>{{ array_sum($charts['service_monthly']['data']) }}</strong> Service
                    </div>
                </div>
            </div>
        </div>

        <!-- Refil Activity Chart -->
        <div class="col-lg-6">
            <div class="card shadow mb-4 chart-card">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Aktivitas Refil 12 Bulan Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="refilChart"></canvas>
                    </div>
                    <div class="text-center mt-3 small text-muted">
                        Total: <strong>{{ array_sum($charts['refil_monthly']['data']) }}</strong> Refil
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                    <h6 class="m-0 font-weight-bold" id="statusChartTitle">Distribusi Status Service</h6>
                    <div class="btn-group btn-group-sm" id="statusToggle">
                        <button type="button" class="btn btn-light active" data-type="service">Service</button>
                        <button type="button" class="btn btn-light" data-type="refil">Refil</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-4" id="statusLegend">
                                @foreach($charts['status_distribution']['service'] as $status => $data)
                                <div class="mb-3">
                                    <i class="{{ $data['icon'] }} me-2" style="color: {{ $data['color'] }}"></i>
                                    <span class="font-weight-bold">{{ $status }}</span>: 
                                    {{ $data['count'] }} ({{ $data['percentage'] }}%)
                                    <div class="progress mt-1" style="height: 8px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $data['percentage'] }}%; background-color: {{ $data['color'] }}"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3 small text-muted">
                        Total: <strong id="statusTotal">{{ $charts['status_distribution']['service_total'] }}</strong> data
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <!-- Jadwal Terbaru -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Jadwal Terbaru</h6>
                    <a href="{{ route('admin.jadwal-selesai') }}" class="btn btn-sm btn-light">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($latest['jadwals'] as $jadwal)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="font-weight-bold mb-0">{{ $jadwal->lokasi_tujuan }}</h6>
                                <span class="badge bg-{{ $jadwal->status == 'Selesai' ? 'success' : 'primary' }}">
                                    {{ $jadwal->status }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span><i class="far fa-calendar-alt me-1"></i> {{ $jadwal->tanggal->format('d M Y') }}</span>
                            </div>
                            <div class="small">
                                <i class="fas fa-user-tie me-1"></i> 
                                {{ $jadwal->kurir ? $jadwal->kurir->name : 'Belum ditugaskan' }}
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center py-4">
                            <i class="fas fa-calendar-times fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted mb-0">Tidak ada jadwal terbaru</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Terbaru -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Service Diproses</h6>
                    <a href="{{ route('admin.service-proses') }}" class="btn btn-sm btn-light">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($latest['services'] as $service)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="font-weight-bold mb-0">{{ $service->nama_pelanggan }}</h6>
                                <span class="badge bg-warning text-dark">Diproses</span>
                            </div>
                            <div class="small text-muted mb-1">
                                <i class="fas fa-box me-1"></i> {{ $service->jenis_barang }}
                            </div>
                            <div class="small text-truncate mb-1">
                                <i class="fas fa-exclamation-triangle me-1"></i> 
                                {{ $service->kerusakan }}
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">
                                    <i class="fas fa-user-cog me-1"></i> 
                                    {{ $service->teknisi ? $service->teknisi->name : '-' }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center py-4">
                            <i class="fas fa-tools fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted mb-0">Tidak ada service diproses</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Refil Terbaru -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-info text-white">
                    <h6 class="m-0 font-weight-bold">Refil Diproses</h6>
                    <a href="{{ route('admin.refil-proses') }}" class="btn btn-sm btn-light">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($latest['refils'] as $refil)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="font-weight-bold mb-0">{{ $refil->nama_pelanggan }}</h6>
                                <span class="badge bg-warning text-dark">Diproses</span>
                            </div>
                            <div class="small text-muted mb-1">
                                <i class="fas fa-print me-1"></i> {{ $refil->jenis_kartrid }}
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">
                                    <i class="fas fa-user-cog me-1"></i> 
                                    {{ $refil->penangan ? $refil->penangan->name : '-' }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center py-4">
                            <i class="fas fa-tint fa-2x text-gray-300 mb-2"></i>
                            <p class="text-muted mb-0">Tidak ada refil diproses</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    .chart-card:hover {
        transform: translateY(-5px);
    }
    
    .card-header {
        border-top-left-radius: 0.5rem !important;
        border-top-right-radius: 0.5rem !important;
    }
    
    .list-group-item {
        border-left: 0;
        border-right: 0;
        padding: 1rem 1.25rem;
        transition: all 0.2s;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .list-group-item:first-child {
        border-top: 0;
    }
    
    .progress {
        height: 0.5rem;
        border-radius: 0.25rem;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .text-truncate {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Status Distribution */
    #statusToggle .btn {
        border-radius: 0.25rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    #statusToggle .btn.active {
        background-color: #fff;
        color: #4e73df;
    }
    
    #statusLegend div {
        padding: 0.25rem 0;
    }
    
    #statusChart {
        max-height: 250px;
    }
    
    .chart-area, .chart-pie {
        position: relative;
        height: 300px;
    }
    
    /* Progress bars in legend */
    #statusLegend .progress {
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem;
        }
        
        .list-group-item {
            padding: 0.75rem 1rem;
        }
        
        .chart-area, .chart-pie {
            height: 250px;
        }
        
        #statusLegend {
            margin-top: 2rem;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Service Activity Chart
var serviceCtx = document.getElementById("serviceChart");
var serviceChart = new Chart(serviceCtx, {
    type: 'bar',
    data: {
        labels: @json($charts['service_monthly']['labels']),
        datasets: [{
            label: "Service",
            backgroundColor: "rgba(28, 200, 138, 0.7)",
            borderColor: "rgba(28, 200, 138, 1)",
            borderWidth: 1,
            data: @json($charts['service_monthly']['data']),
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    display: true,
                    drawBorder: false
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw}`;
                    }
                }
            }
        }
    }
});

// Refil Activity Chart
var refilCtx = document.getElementById("refilChart");
var refilChart = new Chart(refilCtx, {
    type: 'bar',
    data: {
        labels: @json($charts['refil_monthly']['labels']),
        datasets: [{
            label: "Refil",
            backgroundColor: "rgba(54, 185, 204, 0.7)",
            borderColor: "rgba(54, 185, 204, 1)",
            borderWidth: 1,
            data: @json($charts['refil_monthly']['data']),
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    display: true,
                    drawBorder: false
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.dataset.label}: ${context.raw}`;
                    }
                }
            }
        }
    }
});

// Status Chart Data
var statusData = {
    service: {
        title: "Distribusi Status Service",
        labels: @json(array_keys($charts['status_distribution']['service'])),
        data: @json(array_column($charts['status_distribution']['service'], 'count')),
        colors: @json(array_column($charts['status_distribution']['service'], 'color')),
        icons: @json(array_column($charts['status_distribution']['service'], 'icon')),
        percentages: @json(array_column($charts['status_distribution']['service'], 'percentage')),
        total: {{ $charts['status_distribution']['service_total'] }}
    },
    refil: {
        title: "Distribusi Status Refil",
        labels: @json(array_keys($charts['status_distribution']['refil'])),
        data: @json(array_column($charts['status_distribution']['refil'], 'count')),
        colors: @json(array_column($charts['status_distribution']['refil'], 'color')),
        icons: @json(array_column($charts['status_distribution']['refil'], 'icon')),
        percentages: @json(array_column($charts['status_distribution']['refil'], 'percentage')),
        total: {{ $charts['status_distribution']['refil_total'] }}
    }
};

// Initialize Status Chart
var statusCtx = document.getElementById("statusChart");
var statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusData.service.labels,
        datasets: [{
            data: statusData.service.data,
            backgroundColor: statusData.service.colors,
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const percentage = statusData[currentStatusType].percentages[context.dataIndex];
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Current active status type
let currentStatusType = 'service';

// Function to update status chart
function updateStatusChart(type) {
    currentStatusType = type;
    
    // Update chart data
    statusChart.data.labels = statusData[type].labels;
    statusChart.data.datasets[0].data = statusData[type].data;
    statusChart.data.datasets[0].backgroundColor = statusData[type].colors;
    statusChart.update();

    // Update title
    document.getElementById('statusChartTitle').textContent = statusData[type].title;
    
    // Update total
    document.getElementById('statusTotal').textContent = statusData[type].total;
    
    // Update legend
    updateLegend(type);
}

// Function to update legend
function updateLegend(type) {
    let legendHtml = '';
    
    statusData[type].labels.forEach((label, index) => {
        const count = statusData[type].data[index];
        const percentage = statusData[type].percentages[index];
        const color = statusData[type].colors[index];
        const icon = statusData[type].icons[index];
        
        legendHtml += `
            <div class="mb-3">
                <i class="${icon} me-2" style="color: ${color}"></i>
                <span class="font-weight-bold">${label}</span>: 
                ${count} (${percentage}%)
                <div class="progress mt-1" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: ${percentage}%; background-color: ${color}"></div>
                </div>
            </div>
        `;
    });
    
    document.getElementById('statusLegend').innerHTML = legendHtml;
}

// Toggle button event listeners
document.querySelectorAll('#statusToggle button').forEach(button => {
    button.addEventListener('click', function() {
        // Update button active state
        document.querySelectorAll('#statusToggle button').forEach(btn => {
            btn.classList.remove('active');
        });
        this.classList.add('active');
        
        // Update chart
        const type = this.dataset.type;
        updateStatusChart(type);
    });
});

// Initialize with service data
updateStatusChart('service');
</script>
@endsection