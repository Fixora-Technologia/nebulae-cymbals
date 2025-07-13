@extends('admin.layouts.app')

@section('title', 'Item Transaksi')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Item Transaksi',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Item Transaksi', 'url' => route('mindo.transaction-items.index')],
            ['name' => 'Daftar Item Transaksi', 'url' => route('mindo.transaction-items.index')],
        ],
    ])
@endsection

@section('content')
    @include('admin.components.flash-message')

    <!--begin::Row-->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Filter Item Transaksi</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('mindo.transaction-items.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="transaction_code">Kode Transaksi</label>
                                    <input type="text" class="form-control" id="transaction_code" name="transaction_code" 
                                           value="{{ request('transaction_code') }}" placeholder="Kode transaksi">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="transaction_type">Tipe Transaksi</label>
                                    <select class="form-select" name="transaction_type" id="transaction_type">
                                        <option value="">Semua</option>
                                        <option value="in" {{ request('transaction_type') == 'in' ? 'selected' : '' }}>Masuk</option>
                                        <option value="out" {{ request('transaction_type') == 'out' ? 'selected' : '' }}>Keluar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="product_id">Produk</label>
                                    <select class="form-select" name="product_id" id="product_id">
                                        <option value="">Semua</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} ({{ $product->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="date_range">Rentang Tanggal</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                                        <span class="input-group-text">-</span>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('mindo.transaction-items.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Item Transaksi</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Kode Transaksi</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Produk</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                                <tr class="align-middle">
                                    <td class="text-center">{{ ++$i }}</td>
                                    <td>
                                        <a href="{{ route('mindo.transactions.show', $item->transaction_id) }}">
                                            {{ $item->transaction->transaction_code }}
                                        </a>
                                    </td>
                                    <td>{{ $item->transaction->transaction_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($item->transaction->transaction_type == 'in')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('mindo.products.show', $item->product_id) }}">
                                            {{ $item->product->name }}
                                        </a>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->quantity }} {{ $item->product->unit->abbreviation }}</td>
                                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <a class="btn btn-sm btn-info" href="{{ route('mindo.transactions.show', $item->transaction_id) }}">
                                            <i class="fa fa-eye"></i> Lihat Transaksi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->

                <div class="card-footer clearfix">
                    <div class="text-muted float-start">
                        Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} results
                    </div>
                    @if ($data->hasPages())
                        <ul class="pagination pagination-sm m-0 float-end">
                            @if ($data->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                                <li class="page-item"><a class="page-link"
                                        href="{{ $data->previousPageUrl() }}">&laquo;</a></li>
                            @endif

                            @foreach ($data->getUrlRange(1, $data->lastPage()) as $page => $url)
                                <li class="page-item {{ $data->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach

                            @if ($data->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $data->nextPageUrl() }}">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
    <!-- /.col -->
    </div>
    <!--end::Row-->
@endsection
