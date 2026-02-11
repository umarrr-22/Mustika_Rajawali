<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\RefilMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class RefilMasukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = RefilMasuk::where('user_id', Auth::id())
                 ->where('status', 'Draft');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%")
                  ->orWhere('jenis_kartrid', 'like', "%{$search}%");
            });
        }

        $refils = $query->orderBy('created_at', 'desc')->paginate(10);
        $refilEdit = $request->has('edit') ? RefilMasuk::find($request->edit) : null;

        return view('kurir.refil-masuk', compact('refils', 'refilEdit', 'search'));
    }

    public function showImage($filename)
    {
        $path = 'refil-images/' . $filename;
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $type = Storage::disk('public')->mimeType($path);

        return Response::make($file, 200)->header("Content-Type", $type);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_masuk' => 'required|date',
            'nama_pelanggan' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'jenis_layanan' => 'required|in:Refil,Komplain',
            'jenis_kartrid' => 'required|string|max:50',
            'kerusakan' => 'required|string',
            'foto_kartrid' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'foto_kartrid.max' => 'Ukuran foto maksimal 2MB',
            'foto_kartrid.mimes' => 'Format foto harus jpeg, png, atau jpg',
            'foto_kartrid.required' => 'Foto kartrid wajib diupload'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terdapat kesalahan dalam input data.');
        }

        try {
            // Buat direktori jika belum ada
            $directory = 'refil-images';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
            
            $data = $request->except('foto_kartrid');
            $data['user_id'] = Auth::id();
            $data['status'] = 'Draft';

            // Simpan file ke storage/app/public/refil-images/
            $extension = $request->file('foto_kartrid')->extension();
            $filename = 'refil_'.time().'_'.Str::random(5).'.'.$extension;
            $path = $request->file('foto_kartrid')->storeAs(
                $directory,
                $filename,
                'public'
            );
            
            $data['foto_kartrid'] = $filename;

            RefilMasuk::create($data);

            return redirect()->route('kurir.refil-masuk')
                ->with('success', 'Data refil berhasil disimpan!');
                
        } catch (\Exception $e) {
            Log::error('Error menyimpan refil: '.$e->getMessage());
            
            if (isset($filename) && Storage::disk('public')->exists($directory.'/'.$filename)) {
                Storage::disk('public')->delete($directory.'/'.$filename);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        $refil = RefilMasuk::findOrFail($id);
        
        if ($refil->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $validator = Validator::make($request->all(), [
            'tanggal_masuk' => 'required|date',
            'nama_pelanggan' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'jenis_layanan' => 'required|in:Refil,Komplain',
            'jenis_kartrid' => 'required|string|max:50',
            'kerusakan' => 'required|string',
            'foto_kartrid' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'hapus_foto' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terdapat kesalahan dalam input data.');
        }

        try {
            $data = $request->except(['foto_kartrid', 'hapus_foto']);
            $oldFilename = $refil->foto_kartrid;
            $directory = 'refil-images';

            if ($request->has('hapus_foto') && $oldFilename) {
                if (Storage::disk('public')->exists($directory.'/'.$oldFilename)) {
                    Storage::disk('public')->delete($directory.'/'.$oldFilename);
                }
                $data['foto_kartrid'] = null;
            }

            if ($request->hasFile('foto_kartrid')) {
                if ($oldFilename && Storage::disk('public')->exists($directory.'/'.$oldFilename)) {
                    Storage::disk('public')->delete($directory.'/'.$oldFilename);
                }
                
                $extension = $request->file('foto_kartrid')->extension();
                $filename = 'refil_'.time().'_'.Str::random(5).'.'.$extension;
                $path = $request->file('foto_kartrid')->storeAs(
                    $directory,
                    $filename,
                    'public'
                );
                
                $data['foto_kartrid'] = $filename;
            }

            $refil->update($data);

            return redirect()->route('kurir.refil-masuk')
                ->with('success', 'Data refil berhasil diperbarui!');
                
        } catch (\Exception $e) {
            Log::error('Error update refil: '.$e->getMessage());
            
            if (isset($filename) && Storage::disk('public')->exists($directory.'/'.$filename)) {
                Storage::disk('public')->delete($directory.'/'.$filename);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        $refil = RefilMasuk::findOrFail($id);
        
        if ($refil->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        try {
            if ($refil->foto_kartrid) {
                $filePath = 'refil-images/'.$refil->foto_kartrid;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            $refil->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data refil berhasil dihapus!',
                'refil_id' => $refil->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error hapus refil: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data. Silakan coba lagi.'
            ], 500);
        }
    }

    public function kirim($id)
    {
        $refil = RefilMasuk::findOrFail($id);
        
        if ($refil->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $refil->update([
                'status' => 'Menunggu',
                'diambil_oleh' => Auth::user()->name,
                'tanggal_kirim' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refil berhasil dikirim ke teknisi!',
                'refil_id' => $refil->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error kirim refil: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim refil. Silakan coba lagi.'
            ], 500);
        }
    }
}