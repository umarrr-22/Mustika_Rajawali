<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\JadwalKurir;
use Illuminate\Http\Request;

class JadwalHariIniController extends Controller
{
    public function index()
    {
        $query = JadwalKurir::with('pengirim')
            ->whereDate('tanggal', today())
            ->where('status', 'dikirim')
            ->orderBy('daerah')
            ->orderBy('created_at', 'desc');

        if (request()->filled('cari')) {
            $searchTerm = '%' . request('cari') . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('lokasi_tujuan', 'like', $searchTerm)
                  ->orWhere('alamat', 'like', $searchTerm)
                  ->orWhere('daerah', 'like', $searchTerm)
                  ->orWhere('keperluan', 'like', $searchTerm);
            });
        }

        $jadwals = $query->get();

        return view('kurir.jadwal-hari-ini', [
            'jadwals' => $jadwals
        ]);
    }

    public function markAsDone(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:255'
        ]);

        $jadwal = JadwalKurir::findOrFail($id);
        
        $jadwal->update([
            'status' => 'selesai',
            'completed_at' => now(),
            'catatan' => $request->catatan,
            'kurir_id' => auth()->id()
        ]);

        return redirect()
            ->route('kurir.jadwal-selesai')
            ->with([
                'success' => 'Jadwal telah ditandai sebagai selesai',
                'completed_schedule' => $jadwal->lokasi_tujuan
            ]);
    }

    public function hapus(Request $request, $id)
    {
        $request->validate([
            'alasan_hapus' => 'nullable|string|max:255'
        ]);

        $jadwal = JadwalKurir::findOrFail($id);
        $jadwal->delete();

        return redirect()
            ->route('kurir.jadwal-hari-ini')
            ->with('success', 'Jadwal berhasil dihapus');
    }
}