<?php

namespace App\Http\Controllers;

use App\Models\portfolios;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PortofolioController extends Controller
{
    //
    public function index(request $Request){
        $portofolios = portfolios::All();
        $roleName = Auth::user()->UserRole->RoleName();

        if($Request->ajax()) {
            $data = portfolios::select('*');

            return DataTables::of($data->get())->addIndexColumn()->rawColumns(['images'])->make();

            
            return response()->json($portofolios);
        }
    }

    private function store(request $Request) {
        try{
            
            return redirect('/dashboard')->with(['success', 'message' => 'Data Berhasil ditambah!']);
        }catch(\Exception $e){
            return redirect('/dashboard')->withErrors(['error'=>$e->getMessage()]);
        }
    }
}
