<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ServiceMasukController extends Controller
{
    const ITEMS_PER_PAGE = 10;

    public function index()
    {
        try {
            $services = ServiceMasuk::with(['user', 'teknisi'])
                ->where('status', 'Menunggu')
                ->orderBy('created_at', 'desc')
                ->paginate(self::ITEMS_PER_PAGE);

            return view('admin.service-masuk', compact('services'));

        } catch (\Exception $e) {
            Log::error('Error in Admin/ServiceMasukController@index: ' . $e->getMessage());
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
            Log::error('Error in Admin/ServiceMasukController@showImage: ' . $e->getMessage());
            abort(404, 'Gagal memuat gambar');
        }
    }
}