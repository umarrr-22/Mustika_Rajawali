<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefilMasuk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RefilSelesaiController extends Controller
{
    private $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    public function index(Request $request)
    {
        try {
            $query = RefilMasuk::with(['user', 'penangan'])
                ->where('status', 'Selesai')
                ->orderBy('tanggal_selesai', 'desc');

            if ($request->filled('cari')) {
                $query->where(function($q) use ($request) {
                    $q->where('nama_pelanggan', 'like', '%'.$request->cari.'%')
                      ->orWhere('jenis_kartrid', 'like', '%'.$request->cari.'%')
                      ->orWhere('no_telepon', 'like', '%'.$request->cari.'%')
                      ->orWhere('alamat', 'like', '%'.$request->cari.'%')
                      ->orWhereHas('penangan', function($q) use ($request) {
                          $q->where('name', 'like', '%'.$request->cari.'%');
                      });
                });
            }

            if ($request->filled('tahun')) {
                $query->whereYear('tanggal_selesai', $request->tahun);
            }

            if ($request->filled('bulan')) {
                $query->whereMonth('tanggal_selesai', $request->bulan);
            }

            if ($request->filled('penangan_id')) {
                $query->where('ditangani_oleh', $request->penangan_id);
            }

            if ($request->filled('jenis_layanan')) {
                $query->where('jenis_layanan', $request->jenis_layanan);
            }

            $refils = $query->paginate($request->input('per_page', 15))
                ->withQueryString();

            $tahunList = RefilMasuk::selectRaw('YEAR(tanggal_selesai) as tahun')
                ->where('status', 'Selesai')
                ->groupBy('tahun')
                ->orderBy('tahun', 'desc')
                ->pluck('tahun');

            $penanganList = User::whereHas('refilDitangani')->get();

            return view('admin.refil-selesai', [
                'refils' => $refils,
                'bulan' => $this->bulan,
                'tahunList' => $tahunList,
                'penanganList' => $penanganList,
                'total_refil' => $refils->total()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in Admin RefilSelesaiController: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat data refil selesai');
        }
    }

    public function showImage($filename)
    {
        try {
            $path = 'refil-images/'.$filename;
            
            if (!Storage::disk('public')->exists($path)) {
                Log::warning('Gambar tidak ditemukan: '.$filename);
                return response()->file(public_path('images/default-image.jpg'), [
                    'Content-Type' => 'image/jpeg',
                    'Cache-Control' => 'public, max-age=604800'
                ]);
            }

            $file = Storage::disk('public')->get($path);
            $type = Storage::disk('public')->mimeType($path);

            return Response::make($file, 200)
                ->header("Content-Type", $type)
                ->header("Content-Disposition", 'inline; filename="'.$filename.'"')
                ->header("Cache-Control", "public, max-age=604800, immutable");

        } catch (\Exception $e) {
            Log::error('Error akses gambar: '.$e->getMessage());
            return response()->file(public_path('images/default-image.jpg'), [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=604800'
            ]);
        }
    }

    public function toggleVerifikasi(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:refil_masuk,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ID Refil tidak valid'
            ], 400);
        }

        try {
            $refil = RefilMasuk::findOrFail($id);
            
            if ($refil->status !== 'Selesai') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya refil dengan status Selesai yang bisa diverifikasi'
                ], 400);
            }

            $refil->verifikasi = !$refil->verifikasi;
            $refil->save();

            Log::info('Refil verification toggled', [
                'refil_id' => $id,
                'verified_status' => $refil->verifikasi,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'verifikasi' => $refil->verifikasi,
                'message' => $refil->verifikasi 
                    ? 'Refil berhasil diverifikasi' 
                    : 'Verifikasi refil dibatalkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling verification', [
                'error' => $e->getMessage(),
                'refil_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}