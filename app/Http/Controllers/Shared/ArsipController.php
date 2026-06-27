<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\PengajuanAnggaran;
use Illuminate\Http\Request;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanAnggaran::with(['programAnggaran', 'unitKerja', 'user'])
            ->whereIn('status', ['dana_cair', 'ditolak']);

        if (auth()->user()->hasRole('staf_unit')) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('search')) {
            $query->where('judul_usulan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $pengajuans = $query->latest('updated_at')->paginate(15)->withQueryString();

        return view('shared.arsip.index', compact('pengajuans'));
    }
}
