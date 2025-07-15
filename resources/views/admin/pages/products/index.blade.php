@extends('admin.layouts.app')

@section('title', 'Produk')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Produk',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Produk', 'url' => route('mindo.products.index')],
            ['name' => 'Daftar Produk', 'url' => route('mindo.products.index')],
        ],
    ])
@endsection

@section('content')
    @include('admin.components.flash-message')

    <!--begin::Row-->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Daftar Produk</h3>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div>
                        <form action="{{ route('mindo.products.index') }}" method="GET"
                            class="form-inline d-inline-block mr-2">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" name="search"
                                    placeholder="Cari produk..." value="{{ request('search') }}">
                                <button class="btn btn-sm btn-outline-secondary" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('mindo.products.export.excel') }}">
                                    <i class="fas fa-file-excel me-2"></i> Excel</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('mindo.products.export.csv') }}">
                                    <i class="fas fa-file-csv me-2"></i> CSV</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('mindo.products.export.pdf') }}">
                                    <i class="fas fa-file-pdf me-2"></i> PDF</a>
                                </li>
                            </ul>
                        </div>
                        
                        @can('PRODUCT_ADD')
                            <a href="{{ route('mindo.products.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i>
                                Buat Baru
                            </a>
                        @endcan
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $product)
                                <tr class="align-middle">
                                    <td class="text-center">{{ ++$i }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="{{ $product->isLowStock() ? 'text-danger fw-bold' : '' }}">
                                        {{ $product->stock }}</td>
                                    <td>{{ $product->unit->abbreviation }}</td>
                                    <td class="text-center">
                                        @can('PRODUCT_LIST')
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('mindo.products.show', $product->id) }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('PRODUCT_EDIT')
                                            <a class="btn btn-sm btn-warning"
                                                href="{{ route('mindo.products.edit', $product->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('PRODUCT_DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmationModal" data-item-id="{{ $product->id }}"
                                                data-item-name="{{ $product->name }}"
                                                data-delete-route="{{ route('mindo.products.destroy', $product->id) }}">
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
