<?php

namespace App\Http\Controllers\Refil;

use App\Http\Controllers\Controller;
use App\Models\RefilMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class RefilMasukController extends Controller
{
    const ITEMS_PER_PAGE = 10;

    public function index(Request $request)
    {
        try {
            $query = RefilMasuk::where('status', 'Menunggu');
            
            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_pelanggan', 'like', "%$search%")
                      ->orWhere('jenis_kartrid', 'like', "%$search%")
                      ->orWhere('no_telepon', 'like', "%$search%")
                      ->orWhere('alamat', 'like', "%$search%");
                });
            }
            
            $refils = $query->orderBy('created_at', 'desc')
                           ->paginate(self::ITEMS_PER_PAGE)
                           ->appends($request->query());

            return view('refil.refil-masuk', compact('refils'));

        } catch (\Exception $e) {
            Log::error('Error di RefilMasukController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat data refil masuk');
        }
    }

    public function showImage($filename)
    {
        try {
            $path = storage_path('app/public/refil-images/' . $filename);
            
            if (!Storage::disk('public')->exists('refil-images/'.$filename)) {
                abort(404, 'Gambar tidak ditemukan');
            }

            $file = Storage::disk('public')->get('refil-images/'.$filename);
            $type = Storage::disk('public')->mimeType('refil-images/'.$filename);

            return Response::make($file, 200)
                ->header("Content-Type", $type)
                ->header("Cache-Control", "public, max-age=604800");

        } catch (\Exception $e) {
            Log::error('Error di RefilMasukController@showImage: ' . $e->getMessage());
            abort(404, 'Gagal memuat gambar');
        }
    }

    public function proses($id)
    {
        try {
            $refil = RefilMasuk::findOrFail($id);
            
            if ($refil->status !== 'Menunggu') {
                throw new \Exception('Refil tidak dalam status menunggu');
            }

            $refil->update([
                'status' => 'Diproses',
                'ditangani_oleh' => Auth::id()
            ]);

            Log::info('Refil diproses', [
                'refil_id' => $refil->id,
                'user_id' => Auth::id(),
                'action' => 'proses'
            ]);

            return redirect()->route('refil.refil-proses')
                ->with('success', 'Refil berhasil diproses!');

        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Refil tidak ditemukan');
                
        } catch (\Exception $e) {
            Log::error('Error di RefilMasukController@proses: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memproses refil: '.$e->getMessage());
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

            Log::info('Refil dibatalkan', [
                'refil_id' => $id,
                'user_id' => Auth::id(),
                'action' => 'cancel'
            ]);

            return redirect()->route('refil.refil-masuk')
                ->with('success', 'Refil berhasil dibatalkan!');

        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Refil tidak ditemukan');
                
        } catch (\Exception $e) {
            Log::error('Error di RefilMasukController@cancel: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal membatalkan refil: '.$e->getMessage());
        }
    }
}