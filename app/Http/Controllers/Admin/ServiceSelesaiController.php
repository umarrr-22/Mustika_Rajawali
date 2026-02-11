<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceMasuk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ServiceSelesaiController extends Controller
{
    const ITEMS_PER_PAGE = 10;

    public function index(Request $request)
    {
        $currentTahun = $request->get('tahun');
        $currentBulan = $request->get('bulan');
        $currentJenisLayanan = $request->get('jenis_layanan');
        $currentTeknisi = $request->get('teknisi');
        $currentCari = $request->get('cari');
        
        $query = ServiceMasuk::with(['teknisi', 'user'])
            ->where('status', 'Selesai')
            ->orderBy('tanggal_selesai', 'desc');

        if ($currentTahun) {
            $query->whereYear('tanggal_selesai', $currentTahun);
        }

        if ($currentBulan) {
            $query->whereMonth('tanggal_selesai', $currentBulan);
        }

        if ($currentJenisLayanan) {
            $query->where('jenis_layanan', $currentJenisLayanan);
        }

        if ($currentTeknisi) {
            $query->where('teknisi_id', $currentTeknisi);
        }

        if ($currentCari) {
            $query->where(function($q) use ($currentCari) {
                $q->where('nama_pelanggan', 'like', "%{$currentCari}%")
                  ->orWhere('jenis_barang', 'like', "%{$currentCari}%")
                  ->orWhere('no_telepon', 'like', "%{$currentCari}%")
                  ->orWhere('alamat', 'like', "%{$currentCari}%");
            });
        }

        $tahunList = ServiceMasuk::where('status', 'Selesai')
            ->selectRaw('YEAR(tanggal_selesai) as tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $teknisiList = User::whereHas('role', function($query) {
            $query->where('name', 'teknisi');
        })->get();

        $services = $query->paginate(self::ITEMS_PER_PAGE);

        return view('admin.service-selesai', [
            'services' => $services,
            'tahunList' => $tahunList,
            'teknisiList' => $teknisiList,
            'bulan' => [
                '' => 'Semua Bulan',
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ],
            'currentTahun' => $currentTahun,
            'currentBulan' => $currentBulan,
            'currentJenisLayanan' => $currentJenisLayanan,
            'currentTeknisi' => $currentTeknisi,
            'currentCari' => $currentCari,
        ]);
    }

    public function toggleVerifikasi(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:service_masuk,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ID Service tidak valid'
            ], 400);
        }

        try {
            $service = ServiceMasuk::findOrFail($id);
            
            if ($service->status !== 'Selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya service dengan status Selesai yang bisa diverifikasi'
                ], 400);
            }

            $service->is_verified = !$service->is_verified;
            $service->save();

            Log::info('Service verification toggled', [
                'service_id' => $id,
                'verified_status' => $service->is_verified,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'is_verified' => $service->is_verified,
                'message' => $service->is_verified 
                    ? 'Service berhasil diverifikasi' 
                    : 'Verifikasi service dibatalkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling verification', [
                'error' => $e->getMessage(),
                'service_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showImage($filename)
    {
        try {
            if (!preg_match('/^[a-zA-Z0-9_\-.]+$/', $filename)) {
                abort(400, 'Nama file tidak valid');
            }

            $path = 'service_images/' . $filename;
            
            if (!Storage::disk('public')->exists($path)) {
                Log::warning("Image not found: {$filename}");
                abort(404);
            }

            $file = Storage::disk('public')->get($path);
            $type = Storage::disk('public')->mimeType($path);

            return Response::make($file, 200)
                ->header("Content-Type", $type)
                ->header("Cache-Control", "public, max-age=604800, immutable");

        } catch (\Exception $e) {
            Log::error('Error showing image: ' . $e->getMessage(), [
                'filename' => $filename,
                'error' => $e->getTraceAsString()
            ]);
            
            abort(404);
        }
    }
}