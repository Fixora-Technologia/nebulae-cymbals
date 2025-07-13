@extends('admin.layouts.app')

@section('title', 'Detail Satuan')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Detail Satuan',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Satuan', 'url' => route('mindo.units.index')],
            ['name' => 'Detail Satuan', 'url' => ''],
        ],
    ])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Detail Satuan</h3>
                    <div>
                        @can('UNIT_EDIT')
                            <a href="{{ route('mindo.units.edit', $unit->id) }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endcan
                        <a href="{{ route('mindo.units.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px">ID</th>
                            <td>{{ $unit->id }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $unit->name }}</td>
                        </tr>
                        <tr>
                            <th>Singkatan</th>
                            <td>{{ $unit->abbreviation }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $unit->created_at->format('d M Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Diperbarui Pada</th>
                            <td>{{ $unit->updated_at->format('d M Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
