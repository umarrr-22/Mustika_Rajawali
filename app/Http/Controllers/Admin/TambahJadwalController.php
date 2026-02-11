<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKurir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TambahJadwalController extends Controller
{
    public function index()
    {
        $jadwals = $this->getJadwals();
        return view('admin.tambah-jadwal', compact('jadwals'));
    }

    public function edit($id)
    {
        $jadwal = JadwalKurir::findOrFail($id);
        $jadwals = $this->getJadwals();
        
        return view('admin.tambah-jadwal', compact('jadwal', 'jadwals'));
    }

    private function getJadwals()
    {
        $query = Schema::hasColumn('jadwal_kurir', 'status') 
            ? JadwalKurir::where('status', 'draft')
            : JadwalKurir::query();
            
        $jadwals = $query->latest()->paginate(10);
        
        $jadwals->map(function ($item) {
            $daerahColors = [
                'Semarang Barat' => 'danger',
                'Semarang Timur' => 'primary',
                'Semarang Kota' => 'success',
                'Ungaran' => 'warning'
            ];
            $item->warna_daerah = $daerahColors[$item->daerah] ?? 'secondary';
            return $item;
        });
        
        return $jadwals;
    }

    public function store(Request $request)
    {
        $validated = $this->validateJadwal($request);
        $data = $validated + ['user_id' => Auth::id()];
        
        if (Schema::hasColumn('jadwal_kurir', 'status')) {
            $data['status'] = 'draft';
        }

        JadwalKurir::create($data);

        return redirect()->route('admin.tambah-jadwal')
            ->with('success', 'Jadwal kurir berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $this->validateJadwal($request);
        $jadwal = JadwalKurir::findOrFail($id);
        $jadwal->update($validated);

        return redirect()->route('admin.tambah-jadwal')
            ->with('success', 'Jadwal berhasil diperbarui!');
    }

    private function validateJadwal(Request $request)
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'lokasi_tujuan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'daerah' => 'required|in:Semarang Barat,Semarang Timur,Semarang Kota,Ungaran',
            'keperluan' => 'required|string'
        ]);
    }

    public function kirim($id)
    {
        $jadwal = JadwalKurir::findOrFail($id);
        
        $updateData = [];
        if (Schema::hasColumn('jadwal_kurir', 'status')) {
            $updateData['status'] = 'dikirim';
        }
        if (Schema::hasColumn('jadwal_kurir', 'tanggal_kirim')) {
            $updateData['tanggal_kirim'] = now();
        }

        if (!empty($updateData)) {
            $jadwal->update($updateData);
        }

        return redirect()->route('admin.tambah-jadwal')
            ->with('success', 'Jadwal berhasil dikirim ke kurir');
    }

    public function destroy($id)
    {
        $jadwal = JadwalKurir::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus');
    }
}