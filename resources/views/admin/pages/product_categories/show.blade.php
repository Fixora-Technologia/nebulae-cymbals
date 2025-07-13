@extends('admin.layouts.app')

@section('title', 'Detail Kategori Produk')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Detail Kategori Produk',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Kategori Produk', 'url' => route('mindo.product-categories.index')],
            ['name' => 'Detail Kategori Produk', 'url' => ''],
        ],
    ])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Detail Kategori Produk</h3>
                    <div>
                        @can('PRODUCT_CATEGORY_EDIT')
                            <a href="{{ route('mindo.product-categories.edit', $productCategory->id) }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endcan
                        <a href="{{ route('mindo.product-categories.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px">ID</th>
                            <td>{{ $productCategory->id }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $productCategory->name }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $productCategory->created_at->format('d M Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Diperbarui Pada</th>
                            <td>{{ $productCategory->updated_at->format('d M Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
