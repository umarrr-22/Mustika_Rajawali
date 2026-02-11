<?php

namespace App\Http\Controllers\Refil;

use App\Http\Controllers\Controller;
use App\Models\RefilMasuk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            // Base query with relationships
            $query = RefilMasuk::with(['user', 'penangan'])
                ->where('status', 'Selesai')
                ->orderBy('tanggal_selesai', 'desc');

            // Apply filters
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

            if ($request->filled('bulan') && $request->bulan != 'all') {
                $query->whereMonth('tanggal_selesai', $request->bulan);
            }

            if ($request->filled('penangan_id')) {
                $query->where('ditangani_oleh', $request->penangan_id);
            }

            if ($request->filled('jenis_layanan') && $request->jenis_layanan != 'all') {
                if ($request->jenis_layanan == 'Komplain/Perbaikan') {
                    $query->where('jenis_layanan', '!=', 'Refil');
                } else {
                    $query->where('jenis_layanan', $request->jenis_layanan);
                }
            }

            // Get paginated results
            $refils = $query->paginate($request->input('per_page', 15))
                ->withQueryString();

            // Get distinct years and technicians
            $tahunList = RefilMasuk::selectRaw('YEAR(tanggal_selesai) as tahun')
                ->where('status', 'Selesai')
                ->groupBy('tahun')
                ->orderBy('tahun', 'desc')
                ->pluck('tahun');

            $penanganList = User::whereHas('refilDitangani')->get();

            // Define service types
            $layananList = ['Refil', 'Komplain/Perbaikan'];

            return view('refil.refil-selesai', [
                'refils' => $refils,
                'bulan' => $this->bulan,
                'tahunList' => $tahunList,
                'layananList' => $layananList,
                'penanganList' => $penanganList
            ]);

        } catch (\Exception $e) {
            Log::error('Error in RefilSelesaiController: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat data refil selesai');
        }
    }

    public function hapus($id)
    {
        try {
            $refil = RefilMasuk::findOrFail($id);
            
            // Delete image from storage
            if ($refil->foto_kartrid) {
                Storage::disk('public')->delete('refil-images/'.$refil->foto_kartrid);
            }
            
            $refil->delete();
            
            return back()->with('success', 'Data refil beserta foto berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting refil: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data refil');
        }
    }
}