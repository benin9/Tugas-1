<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get medicines that are out of stock
        $obatHabis = Obat::where('stok', 0)->get();
        
        // Get medicines that have low stock (between 1 and 9)
        $obatMenipis = Obat::where('stok', '>', 0)->where('stok', '<', 10)->get();

        return view('admin.dashboard', compact('obatHabis', 'obatMenipis'));
    }
}
