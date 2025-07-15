@extends('admin.layouts.app')

@section('title', 'Detail Pelanggan')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Detail Pelanggan',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Pelanggan', 'url' => route('mindo.customers.index')],
            ['name' => 'Detail Pelanggan', 'url' => ''],
        ],
    ])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Detail Pelanggan</h3>
                    <div>
                        @can('CUSTOMER_EDIT')
                            <a href="{{ route('mindo.customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endcan
                        <a href="{{ route('mindo.customers.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">ID</th>
                                    <td>{{ $customer->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $customer->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Telepon</th>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $customer->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Catatan</th>
                                    <td>{{ $customer->notes ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td>{{ $customer->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Diperbarui Pada</th>
                                    <td>{{ $customer->updated_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Transaction History -->
                    @if($transactions && $transactions->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">Riwayat Transaksi</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Kode</th>
                                                <th>Tanggal</th>
                                                <th>Tipe</th>
                                                <th>Total</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transactions as $key => $transaction)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $transaction->transaction_code }}</td>
                                                <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('d M Y') : '-' }}</td>
                                                <td>
                                                    @if($transaction->transaction_type == 'in')
                                                        <span class="badge bg-success">Masuk</span>
                                                    @else
                                                        <span class="badge bg-danger">Keluar</span>
                                                    @endif
                                                </td>
                                                <td>Rp {{ number_format($transaction->total_value, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('mindo.transactions.show', $transaction->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
