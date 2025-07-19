@extends('admin.layouts.app')

@section('title', isset($productCategory) ? 'Edit Kategori Produk' : 'Buat Kategori Produk')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => isset($productCategory) ? 'Edit Kategori Produk' : 'Buat Kategori Produk',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Kategori Produk', 'url' => route('mindo.product-categories.index')],
            ['name' => isset($productCategory) ? 'Edit Kategori Produk' : 'Buat Kategori Produk', 'url' => ''],
        ],
    ])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($productCategory) ? 'Edit Kategori Produk' : 'Buat Kategori Produk' }}
                    </h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form
                    action="{{ isset($productCategory) ? route('mindo.product-categories.update', $productCategory->id) : route('mindo.product-categories.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($productCategory))
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="name">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Nama kategori"
                                        value="{{ old('name', isset($productCategory) ? $productCategory->name : '') }}"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="description">Deskripsi</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        placeholder="Deskripsi kategori" rows="3">{{ old('description', isset($productCategory) ? $productCategory->description : '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('mindo.product-categories.index') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
