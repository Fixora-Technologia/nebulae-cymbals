@extends('admin.layouts.app')

@section('title', 'Detail Produk')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Detail Produk',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Produk', 'url' => route('mindo.products.index')],
            ['name' => 'Detail Produk', 'url' => ''],
        ],
    ])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Detail Produk</h3>
                    <div>
                        @can('PRODUCT_EDIT')
                            <a href="{{ route('mindo.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        @endcan
                        <a href="{{ route('mindo.products.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">Kode Produk</th>
                                    <td>{{ $product->code }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Produk</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>{{ $product->category->name }}</td>
                                </tr>
                                <tr>
                                    <th>Harga</th>
                                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Stok</th>
                                    <td class="{{ $product->isLowStock() ? 'text-danger fw-bold' : '' }}">
                                        {{ $product->stock }} {{ $product->unit->abbreviation }}
                                        @if ($product->isLowStock())
                                            <span class="badge bg-danger">Stok Rendah</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Stok Minimum</th>
                                    <td>{{ $product->minimum_stock }} {{ $product->unit->abbreviation }}</td>
                                </tr>
                                <tr>
                                    <th>Satuan</th>
                                    <td>{{ $product->unit->name }} ({{ $product->unit->abbreviation }})</td>
                                </tr>
                                <tr>
                                    <th>Deskripsi</th>
                                    <td>{{ $product->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td>{{ $product->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Diperbarui Pada</th>
                                    <td>{{ $product->updated_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Gambar Produk</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid"
                                            alt="{{ $product->name }}" style="max-height: 300px;">
                                    @else
                                        <div class="alert alert-light">
                                            <i class="fa fa-image fa-3x mb-3"></i>
                                            <p>Tidak ada gambar tersedia untuk produk ini</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
