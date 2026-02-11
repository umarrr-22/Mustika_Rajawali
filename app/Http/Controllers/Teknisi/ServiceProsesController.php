<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\ServiceMasuk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ServiceProsesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('cari');
        
        $services = ServiceMasuk::where('status', 'Diproses')
                      ->where('teknisi_id', auth()->id())
                      ->when($search, function($query) use ($search) {
                          $query->where(function($q) use ($search) {
                              $q->where('nama_pelanggan', 'like', "%{$search}%")
                                ->orWhere('jenis_barang', 'like', "%{$search}%")
                                ->orWhere('no_telepon', 'like', "%{$search}%")
                                ->orWhere('alamat', 'like', "%{$search}%");
                          });
                      })
                      ->latest()
                      ->paginate(10)
                      ->withQueryString();

        return view('teknisi.service-proses', compact('services'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'sparepart_diganti' => 'nullable|string|max:255',
            'tanggal_selesai_date' => 'required|date',
            'tanggal_selesai_time' => 'required|date_format:H:i'
        ]);

        try {
            $service = ServiceMasuk::findOrFail($id);
            
            if ($service->teknisi_id != auth()->id()) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengubah service ini');
            }

            // Combine date and time
            $tanggalSelesai = Carbon::createFromFormat(
                'Y-m-d H:i',
                $validated['tanggal_selesai_date'] . ' ' . $validated['tanggal_selesai_time']
            );

            $updateData = [
                'sparepart_diganti' => $validated['sparepart_diganti'],
                'tanggal_selesai' => $tanggalSelesai
            ];

            $service->update($updateData);

            return back()->with('success', 'Data service berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Error updating service', [
                'service_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal memperbarui data service');
        }
    }

    public function selesai($id)
    {
        try {
            $service = ServiceMasuk::findOrFail($id);
            
            if ($service->teknisi_id != auth()->id()) {
                Log::warning('Unauthorized attempt to complete service', [
                    'user_id' => auth()->id(),
                    'service_id' => $id,
                    'service_teknisi' => $service->teknisi_id
                ]);
                return back()->with('error', 'Anda tidak memiliki akses untuk menyelesaikan service ini');
            }
            
            if ($service->status != 'Diproses') {
                return back()->with('error', 'Hanya service dengan status Diproses yang bisa diselesaikan');
            }

            $updateData = [
                'status' => 'Selesai',
                'tanggal_selesai' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            
            $service->update($updateData);

            Log::info('Service completed successfully', [
                'service_id' => $id,
                'user_id' => auth()->id(),
                'new_status' => 'Selesai'
            ]);

            return redirect()->route('teknisi.service-selesai')
                   ->with('success', 'Service berhasil diselesaikan!');

        } catch (\Exception $e) {
            Log::error('Error completing service', [
                'service_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal menyelesaikan service: '.$e->getMessage());
        }
    }

    public function hapus($id)
    {
        try {
            $service = ServiceMasuk::findOrFail($id);
            
            if ($service->teknisi_id != auth()->id()) {
                Log::warning('Unauthorized attempt to delete service', [
                    'user_id' => auth()->id(),
                    'service_id' => $id,
                    'service_teknisi' => $service->teknisi_id
                ]);
                return back()->with('error', 'Anda tidak memiliki akses untuk menghapus service ini');
            }

            $service->update([
                'alasan_penghapusan' => 'Dihapus oleh teknisi saat proses service',
                'updated_at' => Carbon::now()
            ]);

            $service->delete();

            Log::info('Service deleted by technician', [
                'service_id' => $id,
                'user_id' => auth()->id()
            ]);

            return back()->with('success', 'Service berhasil dihapus');
            
        } catch (\Exception $e) {
            Log::error('Failed to delete service', [
                'service_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal menghapus service');
        }
    }
}