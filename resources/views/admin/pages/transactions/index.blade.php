@extends('admin.layouts.app')

@section('title', 'Transaksi')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Transaksi',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Transaksi', 'url' => route('mindo.transactions.index')],
            ['name' => 'Daftar Transaksi', 'url' => route('mindo.transactions.index')],
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Filter Transaksi</h3>
                        <div>
                            @can('TRANSACTION_ADD')
                                <a href="{{ route('mindo.transactions.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> Buat Transaksi
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('mindo.transactions.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="transaction_type">Tipe Transaksi</label>
                                    <select class="form-select" name="transaction_type" id="transaction_type">
                                        <option value="">Semua</option>
                                        <option value="in" {{ request('transaction_type') == 'in' ? 'selected' : '' }}>
                                            Masuk</option>
                                        <option value="out" {{ request('transaction_type') == 'out' ? 'selected' : '' }}>
                                            Keluar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="customer_id">Pelanggan</label>
                                    <select class="form-select" name="customer_id" id="customer_id">
                                        <option value="">Semua</option>
                                        @foreach ($customers as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ request('customer_id') == $id ? 'selected' : '' }}>{{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="date_from">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from"
                                        value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="date_to">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to"
                                        value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('mindo.transactions.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Transaksi</h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item"
                                    href="{{ route('mindo.transactions.export.excel', request()->query()) }}">
                                    <i class="fas fa-file-excel me-2"></i> Excel</a>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('mindo.transactions.export.csv', request()->query()) }}">
                                    <i class="fas fa-file-csv me-2"></i> CSV</a>
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('mindo.transactions.export.pdf', request()->query()) }}">
                                    <i class="fas fa-file-pdf me-2"></i> PDF</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item"
                                    href="{{ route('mindo.transactions.export.items.excel', request()->query()) }}">
                                    <i class="fas fa-file-excel me-2"></i> Export Items (Excel)</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Pelanggan</th>
                                <th>Qty</th>
                                <th>Total Nilai</th>
                                <th>Dibuat Oleh</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $transaction)
                                <tr class="align-middle">
                                    <td class="text-center">{{ ++$i }}</td>
                                    <td>{{ $transaction->transaction_code }}</td>
                                    <td>{{ $transaction->created_at ? date('d F Y', strtotime($transaction->created_at)) : '-' }}
                                    </td>
                                    <td>
                                        @if ($transaction->transaction_type == 'in')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->customer ? $transaction->customer->name : '-' }}</td>
                                    <td>{{ $transaction->items->sum('quantity') ?? 0 }} pcs</td>
                                    <td>Rp {{ number_format($transaction->total_value, 0, ',', '.') }}</td>
                                    <td>{{ $transaction->user ? $transaction->user->name : '-' }}</td>
                                    <td class="text-center">
                                        @can('TRANSACTION_LIST')
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('mindo.transactions.show', $transaction->id) }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('TRANSACTION_DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmationModal" data-item-id="{{ $transaction->id }}"
                                                data-item-name="{{ $transaction->transaction_code }}"
                                                data-delete-route="{{ route('mindo.transactions.destroy', $transaction->id) }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endcan
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

    <!-- Delete Confirmation Modal -->
    @include('admin.components.delete-confirmation-modal')
@endsection
