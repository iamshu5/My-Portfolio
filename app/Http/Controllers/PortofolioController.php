<?php

namespace App\Http\Controllers;

use App\Models\portfolios;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PortofolioController extends Controller
{
    //
    public function index(Request $request){
        $portofolios = portfolios::All();

        if($request->ajax()) {
            $data = portfolios::select('*')
            ->orderBy('dateUpdate', 'desc');

            if($request->filled('form_date') && $request->filled('end_date')){
                $data = $data->whereBetween(DB::raw('CONVERT(DATE, dateUpdate)'), [$request->form_date, $request->end_date]);
            }

            return DataTables::of($data->get())->addIndexColumn()
            ->addColumn('image', function($data) {
                return '<img src="'.url("assets/images/{$data->image}") . '" alt=img_' . $data->title . '" width="100px" height="auto">';
            })
            ->editColumn('dateCreate', function($data) {
                return $data->formatdateCreate();
            })
            ->editColumn('lastUpdate', function($data) {
                return $data->formatlastUpdate();
            })
            ->addColumn('action', function($data) {
                $actionButton = 
                '<button class="btn btn-warning btn-sm shadow-sm rounded mt-1" title="edit" data-bs-toggle="modal" 
                    data-target="#editModal' .$data->id_portf.'">E
                 </button>
                 
                 <button class="btn btn-warning btn-sm shadow-sm rounded mt-1" title="edit" data-bs-toggle="modal" 
                    data-bs-target="#deleteModal' .$data->id_portf.'">E
                 </button>'
                ;

                return $actionButton;
            })
            ->rawColumns(['action', 'image'])
            ->make();
            
            return view('', compact('request', 'portofolios'));
        }
    }

    public function store(Request $request) {
        try{
            $this->validate($request, [
                'title' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg|max:2000',
            ]);

            $checkTitle = portfolios::where('title', $request->title)->count() > 0;
            if($checkTitle) {
                return redirect('/portofolio')->with('alert', ['bg' => 'warning', 'message' => 'Title Sudah ada men']);
            }

            $data = portfolios::create([
                'title' => $request->title,
                'image' => $request->image->store('portofolio', 'public'),
                'dateCreate' => now()->format('Y/m/d H:i:m'),
                'lastUpdate' => now()->format('Y/m/d H:i:m'),
            ]);

            $data->save();
            return redirect('/portofolio')->with(['success', 'message' => 'Title `' . $data->Title . '` berhasil ditambah!']);
        }catch(\Exception $e){
            return redirect('/portofolio')->with('alert', ['bg'=> 'danger', 'Message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Portfolios $portofolios) {
        try{
            $portofolios->title = $request->title;
            $portofolios->description = $request->description;

            $portofolios->save();
            return redirect('/portofolio')->with('alert', ['bg' => 'success', 'Message' => 'Portofolio `' . $portofolios->title . '` Berhasil diperbarui']);
        } catch(\Exception $e){
            return redirect('/portofolio')->with('alert', ['bg' => 'danger', 'Message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(Portfolios $portofolios) {
        try{
            $portofolios->delete();
            return redirect('/portofolio');
        }catch(\Exception $e) {
            return redirect('/portofolio')->with('alert', ['bg' => 'danger', '
            Message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
