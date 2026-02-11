<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\ServiceMasuk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ServiceMasukController extends Controller
{
    const ITEMS_PER_PAGE = 10;

    public function index()
    {
        try {
            $services = ServiceMasuk::with(['user'])
                ->where('status', 'Menunggu')
                ->whereNull('teknisi_id')
                ->orderBy('created_at', 'desc')
                ->paginate(self::ITEMS_PER_PAGE);

            $teknisis = User::whereHas('role', function($query) {
                $query->where('name', 'teknisi');
            })->get();

            return view('teknisi.service-masuk', compact('services', 'teknisis'));

        } catch (\Exception $e) {
            Log::error('Error in ServiceMasukController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat data service masuk');
        }
    }

    public function showImage($filename)
    {
        try {
            $path = storage_path('app/public/service_images/' . $filename);
            
            if (!Storage::disk('public')->exists('service_images/'.$filename)) {
                abort(404, 'Gambar tidak ditemukan');
            }

            $file = Storage::disk('public')->get('service_images/'.$filename);
            $type = Storage::disk('public')->mimeType('service_images/'.$filename);

            return Response::make($file, 200)
                ->header("Content-Type", $type)
                ->header("Cache-Control", "public, max-age=604800");

        } catch (\Exception $e) {
            Log::error('Error in ServiceMasukController@showImage: ' . $e->getMessage());
            abort(404, 'Gagal memuat gambar');
        }
    }

    public function terima(Request $request, $id)
    {
        $validated = $request->validate([
            'teknisi_id' => 'required|exists:users,id'
        ]);

        try {
            $service = ServiceMasuk::findOrFail($id);
            
            if ($service->status !== 'Menunggu') {
                throw new \Exception('Service tidak dalam status menunggu');
            }

            if ($service->teknisi_id !== null) {
                throw new \Exception('Service sudah ditangani teknisi lain');
            }

            $service->update([
                'status' => 'Diproses',
                'teknisi_id' => $validated['teknisi_id']
            ]);

            Log::info('Service diterima', [
                'service_id' => $service->id,
                'teknisi_id' => Auth::id(),
                'action' => 'terima'
            ]);

            return redirect()->route('teknisi.service-proses')
                ->with('success', 'Service berhasil diproses!');

        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Service tidak ditemukan');
                
        } catch (\Exception $e) {
            Log::error('Error in ServiceMasukController@terima: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memproses service: '.$e->getMessage());
        }
    }

    public function hapus(Request $request, $id)
    {
        try {
            $service = ServiceMasuk::findOrFail($id);
            
            // Simpan data untuk log sebelum dihapus
            $serviceData = $service->toArray();

            // Hapus foto jika ada
            if ($service->foto_barang) {
                $filePath = 'service_images/'.$service->foto_barang;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            // Hapus permanen dari database
            $service->delete();

            Log::info('Service dihapus', [
                'service_id' => $id,
                'user_id' => Auth::id(),
                'service_data' => $serviceData,
                'action' => 'hapus'
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Service berhasil dihapus'
                ]);
            }

            return redirect()->route('teknisi.service-masuk')
                ->with('success', 'Service berhasil dihapus');

        } catch (ModelNotFoundException $e) {
            Log::error('Service tidak ditemukan: '.$e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service tidak ditemukan'
                ], 404);
            }

            return redirect()->route('teknisi.service-masuk')
                ->with('error', 'Service tidak ditemukan');
                
        } catch (\Exception $e) {
            Log::error('Error in ServiceMasukController@hapus: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus service: '.$e->getMessage()
                ], 500);
            }

            return redirect()->route('teknisi.service-masuk')
                ->with('error', 'Gagal menghapus service: '.$e->getMessage());
        }
    }
}