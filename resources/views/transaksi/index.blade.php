@extends('layouts.app')
@section('judul','Jurnal Umum - Sistem Informasi Akuntansi')
@section('form-search')
    @include('layouts.navbars.form-search')
@endsection

@section('form-search-mobile')
    @include('layouts.navbars.form-search-mobile')
@endsection

@section('content')
<div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card shadow h-100">
                    <div class="card-header border-0">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-between text-center text-md-left">
                            <div class="mb-3">
                                <h2 class="mb-0">Jurnal Umum</h2>
                                <p class="mb-0 text-sm">Kelola Jurnal Umum</p>
                                <div class="mb-3">
                                    <a href="{{ route('jurnal-umum.create') }}?page={{ request('page') }}" class="btn btn-success" title="Tambah" data-toggle="tooltip"><i class="fas fa-plus"></i></a>
                                    {{-- <a href="#laporan" id="btn-laporan" data-toggle="tooltip" class="btn btn-primary" title="Laporan"><i class="fas fa-calendar"></i></a> --}}
                                    {{-- <button type="button" data-toggle="tooltip" title="Hapus data terpilih" class="btn btn-danger" id="delete" name="delete" >
                                        <i class="fas fa-trash"></i>
                                    </button> --}}
                                </div>
                            </div>
                        </div>
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
                <table class="mb-3 table table-hover table-sm table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="20px">
                                <input type="checkbox" id="check_all">
                            </th>
                            <th class="text-center" width="20px">Opsi</th>
                            <th class="text-center"><a href="{{ request('tanggal') == 1 ? URL::current() . '?tanggal=0' : URL::current() . '?tanggal=1' }}">Tanggal {!! request('tanggal') == 1 ? '<i class="fas fa-caret-up"></i>' : '<i class="fas fa-caret-down"></i>' !!}</a></th>
                            <th class="text-center">Keterangan</th>
                            <th class="text-center">Akun</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Debit</th>
                            <th class="text-center">Kredit</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jurnal_umum as $item)
                            <tr>
                                <td style="vertical-align: middle">
                                    <input type="checkbox" class="jurnal-umum-checkbox" id="delete{{ $item->id }}" name="delete[]" value="{{ $item->id }}">
                                </td>
                                <td style="vertical-align: middle">
                                    <a href="{{ route('jurnal-umum.edit', $item) }}?page={{ request('page') }}" class="btn btn-sm btn-success" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
                                    @if($item->bukti)<a target="_blank" href="{{ route('jurnal-umum.show', $item) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Download Bukti"><i class="fas fa-download"></i></a>@endif
                                </td>
                                <td style="vertical-align: middle; text-align: center">{{ tgl($item->tanggal) }}</td>
                                <td style="vertical-align: middle; white-space: normal">{{ $item->keterangan }}</td>
                                <td style="vertical-align: middle; white-space: normal">
                                    @if ($item->status == 'fix')
                                    <p class="text-center bg-success text-white">{{ $item->status }}</p>
                                    @else
                                    <p class="text-center bg-danger text-white">{{ $item->status }}</p>
                                    @endif

                                </td>
                                <td style="vertical-align: middle; text-align: center"><a href="{{ url('/buku-besar?kode_akun=' . $item->akun->kode . '&kriteria=periode&periode=1-bulan-terakhir') }}" data-toggle="tooltip" title="{{ $item->akun->nama }}">{{ $item->akun->kode }}</a></td>
                                <td style="vertical-align: middle; text-align: right">{{ $item->debit_atau_kredit == 1 ? 'Rp. ' . substr(number_format($item->nilai, 2, ',', '.'),0,-3) : '-' }}</td>
                                <td style="vertical-align: middle; text-align: right">{{ $item->debit_atau_kredit == 2 ? 'Rp. ' . substr(number_format($item->nilai, 2, ',', '.'),0,-3) : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" align="center">Data tidak tersedia</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td  scope="col" colspan="6" align="center">Total </td>
                            <td style=" text-align: right">{{ 'Rp. ' . substr(number_format($totaldebit->sum('nilai'), 2, ',', '.'),0,-3) }}</td>
                            <td style=" text-align: right">{{ 'Rp. ' . substr(number_format($totalkredit->sum('nilai'), 2, ',', '.'),0,-3) }}</td>
                        </tr>
                    </tbody>
                </table>
                {{ $jurnal_umum->links('layouts.footers.pagination') }}
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        $('#btn-laporan').click(function (e) {
            e.preventDefault();
            $("#laporanModal").modal('show');
        });

        $(document).on('click', '#delete', function(){
            let id = [];
            if (confirm("Apakah anda yakin ingin menghapus data ini?")) {
                $(".jurnal-umum-checkbox:checked").each(function () {
                    id.push($(this).val());
                });
                if (id.length > 0) {
                    $.ajax({
                        url     : "{{ route('jurnal-umum.destroys') }}",
                        method  : 'delete',
                        data    : {
                            _token  : "{{ csrf_token() }}",
                            id      : id,
                        },
                        success : function(data){
                            alertSuccess(data.message);
                            location.reload();
                        }
                    });
                } else {
                    alertFail('Harap pilih salah satu jurnal umum');
                }
            }
        });

        $("#check_all").click(function(){
            if (this.checked) {
                $(".jurnal-umum-checkbox").prop('checked',true);
            } else {
                $(".jurnal-umum-checkbox").prop('checked',false);
            }
        });
    });
</script>
@endpush
