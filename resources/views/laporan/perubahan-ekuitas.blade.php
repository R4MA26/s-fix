@extends('layouts.app')
@section('judul','Laporan Perubahan Modal - Sistem Informasi Akuntansi')

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card shadow h-100">
                    <div class="card-header border-0">
                        <h2 class="mb-0">Laporan Perubahan Modal</h2>
                        <p class="mb-0 text-sm">Kelola Laporan Perubahan Modal</p>
                        <form class="mt-3" action="{{ url()->current() }}" method="get">
                            <div class="form-group row">
                                <label class="form-control-label col-md-3 col-form-label" for="kriteria">Kriteria</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="kriteria" id="kriteria">
                                        <option value="periode" {{ request('kriteria') == 'periode' ? 'selected' : '' }}>Periode</option>
                                        <option value="rentang-waktu" {{ request('kriteria') == 'rentang-waktu' ? 'selected' : '' }}>Rentang Waktu (tanggal awal s/d tanggal akhir)</option>
                                        <option value="bulan" {{ request('kriteria') == 'bulan' ? 'selected' : '' }}>Bulan</option>
                                    </select>
                                    <span class="invalid-feedback font-weight-bold"></span>
                                </div>
                            </div>
                            <div id="periode" class="form-group row">
                                <label class="form-control-label col-md-3 col-form-label" for="periode">Periode</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="periode" id="periode">
                                        <option value="1-bulan-terakhir" {{ request('periode') == '1-bulan-terakhir' ? 'selected' : '' }}>1 Bulan Terakhir</option>
                                        <option value="1-minggu-terakhir" {{ request('periode') == '1-minggu-terakhir' ? 'selected' : '' }}>1 Minggu Terakhir</option>
                                    </select>
                                    <span class="invalid-feedback font-weight-bold"></span>
                                </div>
                            </div>
                            <div id="rentang-waktu">
                                <div class="form-group row">
                                    <label class="form-control-label col-md-3 col-form-label" for="tanggal_awal">Tanggal Awal</label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="form-control-label col-md-3 col-form-label" for="tanggal_akhir">Tanggal Akhir</label>
                                    <div class="col-md-9">
                                        <input class="form-control" type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                                    </div>
                                </div>
                            </div>
                            <div id="bulan" class="form-group row">
                                <label class="form-control-label col-md-3 col-form-label" for="bulan">Bulan</label>
                                <div class="col-md-9">
                                    <input class="form-control" type="month" name="bulan" value="{{ request('bulan') }}">
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm table-striped table-bordered">
                    <tbody>
                        @foreach ($akun->where('kelompok_akun_id',3) as $item)
                            @php
                                $data = neraca(request('kriteria'), request('periode'), request('tanggal_awal'), request('tanggal_akhir'), request('bulan'), $item);
                            @endphp
                            <tr>
                                @if ($item->nama == 'Modal')
                                <td>{{$item->nama}}</td>
                                <td></td>
                                <td></td>
                                <td class="text-left modal_neraca_saldo_debit">{{ $item->post_saldo == 2 ? 'Rp. ' . substr(number_format($data['disesuaikan'], 2, ',', '.'),0,-3) : '-' }}</td>
                                @endif
                            </tr>
                        @endforeach
                    <tr>
                            @php
                                $pendapatan = neraca_akun(request('kriteria'), request('periode'), request('tanggal_awal'), request('tanggal_akhir'), request('bulan'), $akun->where('kelompok_akun_id', 4));
                                $pembiayaan = neraca_akun(request('kriteria'), request('periode'), request('tanggal_awal'), request('tanggal_akhir'), request('bulan'), $akun->where('kelompok_akun_id', 6));
                                $prive = neraca_akun(request('kriteria'), request('periode'), request('tanggal_awal'), request('tanggal_akhir'), request('bulan'), $akun->where('kelompok_akun_id',3)->where('kode', '3120'));
                                $modal = neraca_akun(request('kriteria'), request('periode'), request('tanggal_awal'), request('tanggal_akhir'), request('bulan'), $akun->where('kelompok_akun_id',3)->where('kode', '3110'));
                                $laba = $pendapatan - $pembiayaan;
                            @endphp
                        <tr>
                            <td class="text-center">Laba Bersih </td>
                                <td class="text-left laba-bersih">{{ 'Rp. ' . substr(number_format($laba, 2, ',', '.'),0,-3) }}
                                </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-center">Prive</td>
                            <td class="text-left modal_neraca_saldo_kredit">{{ $item->post_saldo == 1 ? 'Rp. ' . substr(number_format($data['disesuaikan']  * -1, 2, ',', '.'),0,-3) : '-' }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tr>
                        <tr>
                            <th class="text-left" colspan="3">Penambahan Modal</th>
                            <th class="text-left penambahan-modal">{{ 'Rp. ' . substr(number_format($laba + $prive , 2, ',', '.'),0,-3) }}</th>
                            {{-- <th class="text-right" id="tes"></th> --}}
                            {{-- <th class="text-right" id="jumlah_modal"></th> --}}
                        </tr>
                    </tbody>
                    <tfoot class="bg-primary text-white">
                        <tr>
                            <th colspan="3" class="text-center">Modal Akhir</th>
                            <th class="text-left" id="total_modal"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        $("#jumlah_modal_debit").html('Rp. ' + new Intl.NumberFormat('id-ID').format(jumlah('modal_neraca_saldo_debit')));

        let none = 0;
        $("#tes").val(none);

        $("#totmodal").html('Rp. ' + new Intl.NumberFormat('id-ID').format(jumlah('total-modal')));

        $("#jumlah_modal_kredit").val(none);

        $("#total_modal").html('Rp. ' + new Intl.NumberFormat('id-ID').format(jumlah('modal_neraca_saldo_debit') + jumlah('penambahan-modal')));
        kriteria();
        $("#kriteria").change(function () {
            kriteria();
        });
    });
</script>
@endpush
