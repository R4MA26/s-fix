<?php

namespace App\Http\Controllers;

use App\Http\Requests\JurnalUmumRequest;
use App\Models\JurnalUmum;
use Illuminate\Http\Request;

class JurnalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $jurnal_umum = JurnalUmum::orderBy('tanggal', $request->tanggal == 1 ? 'desc' : 'asc')->paginate(10);
        $totaldebit = JurnalUmum::where('debit_atau_kredit', 1)->get();
        $totalkredit = JurnalUmum::where('debit_atau_kredit', 2)->get();
        return view('jurnal-umum.index', [
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
        return view('jurnal-umum.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\JurnalUmumRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JurnalUmumRequest $request)
    {
        // $total = JurnalUmum::where('debit_atau_kredit', $request->debit_atau_kredit)->latest()->get();
        $data = $request->validated();

        // dd($data);
        if($request->bukti) {
            $data['bukti']  = $request->bukti->store('public/bukti');
        }

        foreach($request->akun_id as $key => $akn)
        {
            $data['akun_id'] = $request->akun_id[$key];
            $data['debit_atau_kredit'] = $request->debit_atau_kredit[$key];
            // dump($data);
            JurnalUmum::create($data);
        }

        return response()->json([
                    'message'   => 'Jurnal umum berhasil ditambahkan',
                    'redirect'  => route('transaksi.index')
                ]);

        // return redirect()->back()->with(['message'   => 'Jurnal umum berhasil ditambahkan']);

        // rumus excel
        // $reqnilai = $request->nilai;
        // $debitnilai = $total[0]->nilai;
        // if($reqnilai && $request->debit_atau_kredit == 1){
        //     $data['nilai'] = $debitnilai  + $reqnilai;
        // } else if($reqnilai && $request->debit_atau_kredit == 2){
        //     $data['nilai'] =  $reqnilai - $debitnilai  ;
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JurnalUmum  $jurnal_umum
     * @return \Illuminate\Http\Response
     */
    public function show(JurnalUmum $jurnal_umum)
    {
        $judul = $jurnal_umum->keterangan.'.'.substr(strrchr(storage_path('app/'.$jurnal_umum->bukti),'.'),1);
        return response()->file(storage_path('app/' . $jurnal_umum->bukti),[
            'Content-Disposition'   => 'inline; filename="'.$judul.'"'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\JurnalUmum  $jurnal_umum
     * @return \Illuminate\Http\Response
     */
    public function edit(JurnalUmum $jurnal_umum)
    {
        return view('jurnal-umum.edit', compact('jurnal_umum'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\JurnalUmumRequest  $request
     * @param  \App\JurnalUmum  $jurnal_umum
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JurnalUmum $jurnal_umum)
    {
        $data = $request->all();
        // dd($data);
        if($request->bukti) {
            if ($jurnal_umum->bukti) {
                unlink(storage_path('app/' . $jurnal_umum->bukti));
            }

            $data['bukti']  = $request->bukti->store('public/bukti');
        }

        $jurnal_umum->orderBy('id', 'desc')->first();
        // dd($jurnal_umum->status);
        if($jurnal_umum != $data){
            $jurnal_umum->update([
                'status' => 'koreksi'
            ]);
            $create = new JurnalUmum;
            $create['akun_id'] = $data['akun_id'];
            $create['tanggal'] = $data['tanggal'];
            $create['keterangan'] = $data['keterangan'];
            $create['bukti'] = $request->bukti;
            $create['status'] = 'fix';
            $create['debit_atau_kredit'] = $request->debit_atau_kredit;
            $create['nilai'] = $data['nilai'];
            $create->save();
        }

        return response()->json([
            'message'   => 'Jurnal umum berhasil diperbarui'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JurnalUmum  $jurnal_umum
     * @return \Illuminate\Http\Response
     */
    public function destroy(JurnalUmum $jurnal_umum)
    {
        if ($jurnal_umum->bukti) {
            unlink(storage_path('app/' . $jurnal_umum->bukti));
        }

        $jurnal_umum->delete();
        return back()->with('success','Jurnal umum berhasil dihapus');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JurnalUmum  $jurnal_umum
     * @return \Illuminate\Http\Response
     */
    public function destroys(Request $request)
    {
        foreach ($request->id as $item) {
            $jurnal_umum = JurnalUmum::find($item);

            if ($jurnal_umum->bukti) {
                unlink(storage_path('app/' . $jurnal_umum->bukti));
            }

            $jurnal_umum->delete();
        }

        return response()->json([
            'message'   => 'Jurnal umum berhasil dihapus'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JurnalUmum  $jurnal_umum
     * @return \Illuminate\Http\Response
     */
    public function delete(JurnalUmum $jurnal_umum)
    {
        if ($jurnal_umum->bukti) {
            unlink(storage_path('app/' . $jurnal_umum->bukti));
        }

        $jurnal_umum->bukti = null;
        $jurnal_umum->save();
        return back()->with('success','Bukti berhasil dihapus');
    }

    public function laporan(Request $request)
    {
        $request->validate([
            'awal'  => ['required','date'],
            'akhir' => ['required','date'],
        ]);

        if (!$request->awal || !$request->akhir) {
            return redirect()->route('jurnal-umum.index');
        }

        $from = $request->awal;
        $to = $request->akhir;
        $jurnal_umum = JurnalUmum::whereBetween('tanggal', [$from, $to])->orderBy('tanggal', $request->tanggal == 1 ? 'desc' : 'asc')->get();

        return view('jurnal-umum.laporan', compact('jurnal_umum'));
    }

}
