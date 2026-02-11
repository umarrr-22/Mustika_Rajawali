<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceMasuk;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceProsesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('cari');
        $teknisiId = $request->input('teknisi_id');
        
        // Query untuk mendapatkan teknisi dengan service yang diproses
        $query = User::with(['serviceProsesDitangani' => function($query) use ($search) {
                $query->when($search, function($q) use ($search) {
                    $q->where(function($subQuery) use ($search) {
                        $subQuery->where('nama_pelanggan', 'like', "%{$search}%")
                                ->orWhere('jenis_barang', 'like', "%{$search}%")
                                ->orWhere('no_telepon', 'like', "%{$search}%")
                                ->orWhere('alamat', 'like', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc');
            }])
            ->whereHas('serviceProsesDitangani')
            ->teknisi();

        // Filter teknisi tertentu jika dipilih
        if ($teknisiId) {
            $query->where('id', $teknisiId);
        }

        $technicians = $query->get();

        // Hitung total service untuk semua teknisi
        $totalServices = $technicians->sum(function($tech) {
            return $tech->serviceProsesDitangani->count();
        });

        return view('admin.service-proses', [
            'technicians' => $technicians,
            'totalServices' => $totalServices,
            'search' => $search,
            'selectedTeknisi' => $teknisiId
        ]);
    }

    public function byTechnician($teknisiId)
    {
        $technician = User::with(['serviceProsesDitangani' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])
        ->teknisi()
        ->findOrFail($teknisiId);

        return view('admin.service-proses', [
            'technicians' => collect([$technician]),
            'totalServices' => $technician->serviceProsesDitangani->count(),
            'selectedTeknisi' => $technician->id
        ]);
    }
}