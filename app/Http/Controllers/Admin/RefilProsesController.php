<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefilMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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

            return view('admin.refil-proses', compact('refils'));

        } catch (\Exception $e) {
            Log::error('Error di Admin/RefilProsesController@index: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat data refil: ' . $e->getMessage());
        }
    }

    public function showImage($filename)
    {
        $path = storage_path('app/public/refil_images/' . $filename);
        
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        return response($file, 200)->header('Content-Type', $type);
    }
}