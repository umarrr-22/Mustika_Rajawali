<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKurir;
use App\Models\User;
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

    public function index(Request $request)
    {
        $query = JadwalKurir::with(['pengirim', 'kurir'])
            ->where('status', 'selesai')
            ->orderBy('completed_at', 'desc');

        // Filter Tahun
        if ($request->filled('tahun')) {
            $query->whereYear('completed_at', $request->tahun);
        }

        // Filter Bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('completed_at', $request->bulan);
        }

        // Filter Kurir
        if ($request->filled('kurir_id')) {
            $query->where('kurir_id', $request->kurir_id);
        }

        // Filter Daerah
        if ($request->filled('daerah')) {
            $query->where('daerah', $request->daerah);
        }

        // Pencarian
        if ($request->filled('cari')) {
            $searchTerm = '%' . $request->cari . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('lokasi_tujuan', 'like', $searchTerm)
                  ->orWhere('alamat', 'like', $searchTerm)
                  ->orWhere('keperluan', 'like', $searchTerm)
                  ->orWhere('catatan', 'like', $searchTerm);
            });
        }

        $jadwals = $query->get();
        
        // Daftar Tahun untuk dropdown filter
        $tahunList = JadwalKurir::selectRaw('YEAR(completed_at) as tahun')
            ->where('status', 'selesai')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Daftar Kurir yang pernah menyelesaikan jadwal
        $kurirList = User::whereHas('role', function($q) {
                $q->where('name', 'kurir');
            })
            ->whereHas('jadwalKurir', function($q) {
                $q->where('status', 'selesai');
            })
            ->orderBy('name')
            ->get();

        return view('admin.jadwal-selesai', [
            'jadwals' => $jadwals,
            'bulan' => $this->bulan,
            'tahunList' => $tahunList,
            'kurirList' => $kurirList,
            'total_jadwal' => $jadwals->count()
        ]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $jadwal = JadwalKurir::where('id', $id)
                ->where('status', 'selesai')
                ->firstOrFail();

            $jadwal->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        // Logika untuk export data ke Excel/PDF
        // Bisa diimplementasikan menggunakan Laravel Excel
    }

    public function print(Request $request)
    {
        // Logika untuk mencetak laporan
        $filter = [
            'tahun' => $request->tahun,
            'bulan' => $request->bulan,
            'kurir_id' => $request->kurir_id,
            'daerah' => $request->daerah
        ];

        $query = JadwalKurir::with(['pengirim', 'kurir'])
            ->where('status', 'selesai');

        if ($filter['tahun']) {
            $query->whereYear('completed_at', $filter['tahun']);
        }

        if ($filter['bulan']) {
            $query->whereMonth('completed_at', $filter['bulan']);
        }

        if ($filter['kurir_id']) {
            $query->where('kurir_id', $filter['kurir_id']);
        }

        if ($filter['daerah']) {
            $query->where('daerah', $filter['daerah']);
        }

        $jadwals = $query->orderBy('completed_at', 'desc')->get();

        return view('admin.print.jadwal-selesai', [
            'jadwals' => $jadwals,
            'filter' => $filter,
            'bulan' => $this->bulan
        ]);
    }
}