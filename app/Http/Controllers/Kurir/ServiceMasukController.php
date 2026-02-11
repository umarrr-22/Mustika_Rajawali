<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\ServiceMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class ServiceMasukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ServiceMasuk::where('user_id', Auth::id())
            ->where('status', 'Draft');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%")
                  ->orWhere('jenis_barang', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(10);
        $serviceEdit = $request->has('edit') ? ServiceMasuk::find($request->edit) : null;

        if ($serviceEdit && $serviceEdit->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('kurir.service-masuk', compact('services', 'serviceEdit', 'search'));
    }

    public function showImage($filename)
    {
        $path = storage_path('app/public/service_images/' . $filename);
        
        if (!file_exists($path)) {
            abort(404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return Response::make($file, 200)->header("Content-Type", $type);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_masuk' => 'required|date',
            'nama_pelanggan' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'jenis_layanan' => 'required|in:Service,Komplain',
            'jenis_barang' => 'required|string|max:50',
            'kerusakan' => 'required|string',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'foto_barang.max' => 'Ukuran foto maksimal 2MB',
            'foto_barang.mimes' => 'Format foto harus jpeg, png, atau jpg'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terdapat kesalahan dalam input data.');
        }

        try {
            if (!Storage::disk('public')->exists('service_images')) {
                Storage::disk('public')->makeDirectory('service_images');
            }
            
            $data = $request->except('foto_barang');
            $data['user_id'] = Auth::id();
            $data['status'] = 'Draft';
            $data['diambil_oleh'] = Auth::user()->name;

            if ($request->hasFile('foto_barang')) {
                $filename = 'service_'.time().'_'.Str::random(5).'.'.$request->file('foto_barang')->extension();
                $path = $request->file('foto_barang')->storeAs('service_images', $filename, 'public');
                
                if (!Storage::disk('public')->exists('service_images/'.$filename)) {
                    throw new \Exception('Gagal menyimpan file foto');
                }

                $data['foto_barang'] = $filename;
            }

            ServiceMasuk::create($data);

            return redirect()->route('kurir.service-masuk')
                ->with('success', 'Data service berhasil disimpan!');
                
        } catch (\Exception $e) {
            Log::error('Error menyimpan service: '.$e->getMessage());
            
            if (isset($filename) && Storage::disk('public')->exists('service_images/'.$filename)) {
                Storage::disk('public')->delete('service_images/'.$filename);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        $service = ServiceMasuk::findOrFail($id);
        
        if ($service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'tanggal_masuk' => 'required|date',
            'nama_pelanggan' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'jenis_layanan' => 'required|in:Service,Komplain',
            'jenis_barang' => 'required|string|max:50',
            'kerusakan' => 'required|string',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'hapus_foto' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terdapat kesalahan dalam input data.');
        }

        try {
            $data = $request->except(['foto_barang', 'hapus_foto']);
            $oldFilename = $service->foto_barang;

            if ($request->has('hapus_foto') && $oldFilename) {
                if (Storage::disk('public')->exists('service_images/'.$oldFilename)) {
                    Storage::disk('public')->delete('service_images/'.$oldFilename);
                }
                $data['foto_barang'] = null;
            }

            if ($request->hasFile('foto_barang')) {
                if ($oldFilename && Storage::disk('public')->exists('service_images/'.$oldFilename)) {
                    Storage::disk('public')->delete('service_images/'.$oldFilename);
                }
                
                $filename = 'service_'.time().'_'.Str::random(5).'.'.$request->file('foto_barang')->extension();
                $path = $request->file('foto_barang')->storeAs('service_images', $filename, 'public');
                
                if (!Storage::disk('public')->exists('service_images/'.$filename)) {
                    throw new \Exception('Gagal menyimpan file foto baru');
                }
                
                $data['foto_barang'] = $filename;
            }

            $service->update($data);

            return redirect()->route('kurir.service-masuk')
                ->with('success', 'Data service berhasil diperbarui!');
                
        } catch (\Exception $e) {
            Log::error('Error update service: '.$e->getMessage());
            
            if (isset($filename) && Storage::disk('public')->exists('service_images/'.$filename)) {
                Storage::disk('public')->delete('service_images/'.$filename);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        $service = ServiceMasuk::findOrFail($id);
        
        if ($service->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        try {
            if ($service->foto_barang) {
                $filePath = 'service_images/'.$service->foto_barang;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data service berhasil dihapus!'
            ]);
                
        } catch (\Exception $e) {
            Log::error('Error hapus service: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data. Silakan coba lagi.'
            ], 500);
        }
    }

    public function kirim($id)
    {
        $service = ServiceMasuk::findOrFail($id);
        
        if ($service->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        try {
            $service->update([
                'status' => 'Menunggu',
                'tanggal_kirim' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service berhasil dikirim ke teknisi!'
            ]);
                
        } catch (\Exception $e) {
            Log::error('Error kirim service: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim service. Silakan coba lagi.'
            ], 500);
        }
    }
}  