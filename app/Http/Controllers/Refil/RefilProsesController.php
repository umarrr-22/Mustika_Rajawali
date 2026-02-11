<?php

namespace App\Http\Controllers\Refil;

use App\Http\Controllers\Controller;
use App\Models\RefilMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RefilProsesController extends Controller
{
    const ITEM_PER_PAGE = 15;

    public function index(Request $request)
    {
        try {
            $query = RefilMasuk::with(['user', 'penangan'])
                ->where('status', 'Diproses')
                ->orderBy('tanggal_masuk', 'desc');

            if ($request->has('cari') && !empty($request->cari)) {
                $keyword = $request->cari;
                $query->where(function($q) use ($keyword) {
                    $q->where('nama_pelanggan', 'like', "%{$keyword}%")
                      ->orWhere('jenis_kartrid', 'like', "%{$keyword}%")
                      ->orWhere('alamat', 'like', "%{$keyword}%")
                      ->orWhereHas('penangan', function($q) use ($keyword) {
                          $q->where('name', 'like', "%{$keyword}%");
                      });
                });
            }

            $refils = $query->paginate(self::ITEM_PER_PAGE);

            return view('refil.refil-proses', compact('refils'));

        } catch (\Exception $e) {
            Log::error('Error di RefilProsesController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat data refil: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'sparepart' => 'nullable|string|max:255',
                'tanggal_selesai_date' => 'required|date',
                'tanggal_selesai_time' => 'required|date_format:H:i'
            ]);

            $refil = RefilMasuk::findOrFail($id);
            
            $tanggalSelesai = $request->tanggal_selesai_date . ' ' . $request->tanggal_selesai_time;

            $refil->update([
                'sparepart' => $request->sparepart,
                'tanggal_selesai' => $tanggalSelesai,
                'ditangani_oleh' => Auth::id()
            ]);

            return redirect()->route('refil.refil-proses')
                ->with('success', 'Data refil berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error update refil: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data: '.$e->getMessage())
                ->withInput();
        }
    }

    public function complete($id)
    {
        try {
            $refil = RefilMasuk::findOrFail($id);
            
            // Pertahankan tanggal_selesai yang sudah ada jika sudah diisi
            // Jika belum diisi, baru gunakan waktu sekarang
            $tanggalSelesai = $refil->tanggal_selesai ?: now();
            
            $refil->update([
                'status' => 'Selesai',
                'tanggal_selesai' => $tanggalSelesai, // Gunakan tanggal yang sudah ada atau sekarang
                'ditangani_oleh' => Auth::id()
            ]);

            return redirect()->route('refil.refil-selesai')
                ->with('success', 'Refil berhasil ditandai sebagai selesai!');

        } catch (\Exception $e) {
            Log::error('Error complete refil: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menyelesaikan refil: '.$e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            $refil = RefilMasuk::findOrFail($id);
            
            if ($refil->foto_kartrid) {
                Storage::disk('public')->delete('refil-images/'.$refil->foto_kartrid);
            }

            $refil->delete();

            return redirect()->route('refil.refil-proses')
                ->with('success', 'Refil berhasil dibatalkan!');

        } catch (\Exception $e) {
            Log::error('Error cancel refil: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal membatalkan refil: '.$e->getMessage());
        }
    }
}