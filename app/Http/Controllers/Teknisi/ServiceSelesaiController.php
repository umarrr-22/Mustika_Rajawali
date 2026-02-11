<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\ServiceMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ServiceSelesaiController extends Controller
{
    const ITEMS_PER_PAGE = 10;

    public function index(Request $request)
    {
        $teknisiId = Auth::id();
        
        // Get filter parameters
        $tahun = $request->get('tahun');
        $jenis_layanan = $request->get('jenis_layanan');
        $cari = $request->get('cari');
        
        // Get years with completed services for dropdown
        $tahunList = ServiceMasuk::where('status', 'Selesai')
            ->where('teknisi_id', $teknisiId)
            ->selectRaw('YEAR(tanggal_selesai) as tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
            
        // Base query
        $query = ServiceMasuk::with(['teknisi', 'user'])
            ->where('status', 'Selesai')
            ->where('teknisi_id', $teknisiId)
            ->orderBy('tanggal_selesai', 'desc');

        // Apply filters
        if ($tahun) {
            $query->whereYear('tanggal_selesai', $tahun);
        }
        
        if ($jenis_layanan) {
            $query->where('jenis_layanan', $jenis_layanan);
        }
        
        if ($cari) {
            $query->where(function($q) use ($cari) {
                $q->where('nama_pelanggan', 'like', '%'.$cari.'%')
                  ->orWhere('jenis_barang', 'like', '%'.$cari.'%')
                  ->orWhere('no_telepon', 'like', '%'.$cari.'%');
            });
        }

        // Get paginated results
        $services = $query->paginate(self::ITEMS_PER_PAGE);

        return view('teknisi.service-selesai', [
            'services' => $services,
            'tahunList' => $tahunList,
            'currentTahun' => $tahun,
            'currentJenisLayanan' => $jenis_layanan,
            'currentCari' => $cari,
            'bulan' => [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ]
        ]);
    }

    public function hapus($id)
    {
        $service = ServiceMasuk::findOrFail($id);
        
        if ($service->teknisi_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete associated photo if exists
        if ($service->foto_barang) {
            try {
                $relativePath = 'service_images/' . $service->foto_barang;
                $absolutePath = storage_path('app/public/' . $relativePath);
                
                Log::info("Attempting to delete service image", [
                    'service_id' => $service->id,
                    'file_name' => $service->foto_barang,
                    'relative_path' => $relativePath,
                    'absolute_path' => $absolutePath
                ]);

                // Delete using Storage facade
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                    Log::info("File deleted using Storage facade");
                }

                // Double check with direct file deletion
                if (file_exists($absolutePath)) {
                    unlink($absolutePath);
                    Log::info("File deleted using direct filesystem");
                }
            } catch (\Exception $e) {
                Log::error("Failed to delete service image: " . $e->getMessage(), [
                    'service_id' => $service->id,
                    'error' => $e
                ]);
            }
        }

        $service->delete();

        return back()->with('success', 'Data service beserta foto berhasil dihapus');
    }
}