<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\JadwalKurir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalSelesaiController extends Controller
{
    private $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    public function index()
    {
        // Get distinct years from completed schedules
        $tahunList = JadwalKurir::where('status', 'selesai')
            ->where('kurir_id', auth()->id())
            ->selectRaw('YEAR(completed_at) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $query = JadwalKurir::with('pengirim')
            ->where('status', 'selesai')
            ->where('kurir_id', auth()->id())
            ->orderBy('completed_at', 'desc');

        // Apply filters
        $this->applyFilters($query);

        $jadwals = $query->get();

        return view('kurir.jadwal-selesai', [
            'jadwals' => $jadwals,
            'bulan' => $this->bulan,
            'tahunList' => $tahunList
        ]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $jadwal = JadwalKurir::where('id', $id)
                ->where('kurir_id', auth()->id())
                ->where('status', 'selesai')
                ->firstOrFail();

            $jadwal->delete();

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal'
            ], 500);
        }
    }

    private function applyFilters($query)
    {
        if (request()->filled('tahun')) {
            $query->whereYear('completed_at', request('tahun'));
        }

        if (request()->filled('bulan')) {
            $query->whereMonth('completed_at', request('bulan'));
        }

        if (request()->filled('cari')) {
            $searchTerm = '%' . request('cari') . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('lokasi_tujuan', 'like', $searchTerm)
                  ->orWhere('alamat', 'like', $searchTerm)
                  ->orWhere('daerah', 'like', $searchTerm);
            });
        }

        return $query;
    }
}