<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use Illuminate\Http\Request;

class JurnalUmumTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $jurnal_umum = JurnalUmum::orderBy('tanggal', $request->tanggal == 1 ? 'desc' : 'asc')->paginate(10);
        $totaldebit = JurnalUmum::where('debit_atau_kredit', 1)
        ->where('status', 'fix')
        ->get();
        $totalkredit = JurnalUmum::where('debit_atau_kredit', 2)
        ->where('status', 'fix')
        ->get();
        if ($request->cari) {
            $jurnal_umum = JurnalUmum::where('keterangan','like',"%{$request->cari}%")
                            ->orWhere('tanggal','like',"%{$request->cari}%")
                            ->orWhereHas('akun', function ($jurnal_umum) use ($request) {
                                $jurnal_umum->where('nama','like',"%{$request->cari}%");
                                $jurnal_umum->orWhere('kode','like',"%{$request->cari}%");
                            })
                            ->orderBy('tanggal', $request->tanggal == 1 ? 'desc' : 'asc')->paginate(10);
        }

        $jurnal_umum->appends($request->all());

        return view('transaksi.index', [
            'jurnal_umum' => $jurnal_umum,
            'totaldebit' => $totaldebit,
            'totalkredit' => $totalkredit
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
