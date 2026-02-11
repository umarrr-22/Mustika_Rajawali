<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefilMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class RefilMasukController extends Controller
{
    const ITEMS_PER_PAGE = 10;

    public function index(Request $request)
    {
        try {
            $query = RefilMasuk::where('status', 'Menunggu');
            
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

            return view('admin.refil-masuk', compact('refils'));

        } catch (\Exception $e) {
            Log::error('Error di Admin/RefilMasukController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat data refil masuk');
        }
    }

    public function showImage($filename)
    {
        try {
            if (!Storage::disk('public')->exists('refil-images/'.$filename)) {
                Log::error('File gambar tidak ditemukan: '.$filename);
                
                // Return gambar placeholder jika file tidak ada
                $placeholder = public_path('images/image-not-found.jpg');
                $file = file_get_contents($placeholder);
                $type = mime_content_type($placeholder);
                
                return Response::make($file, 200)
                    ->header("Content-Type", $type);
            }

            $file = Storage::disk('public')->get('refil-images/'.$filename);
            $type = Storage::disk('public')->mimeType('refil-images/'.$filename);

            return Response::make($file, 200)
                ->header("Content-Type", $type)
                ->header("Cache-Control", "public, max-age=604800");

        } catch (\Exception $e) {
            Log::error('Error menampilkan gambar: '.$e->getMessage());
            abort(404, 'Gagal memuat gambar');
        }
    }
}