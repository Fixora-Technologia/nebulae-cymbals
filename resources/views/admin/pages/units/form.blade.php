@extends('admin.layouts.app')

@section('title', isset($unit) ? 'Edit Satuan' : 'Buat Satuan')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => isset($unit) ? 'Edit Satuan' : 'Buat Satuan',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Satuan', 'url' => route('mindo.units.index')],
            ['name' => isset($unit) ? 'Edit Satuan' : 'Buat Satuan', 'url' => '']
        ],
    ])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($unit) ? 'Edit Satuan' : 'Buat Satuan' }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ isset($unit) ? route('mindo.units.update', $unit->id) : route('mindo.units.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($unit))
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" placeholder="Nama satuan"
                                           value="{{ old('name', isset($unit) ? $unit->name : '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="abbreviation">Singkatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" 
                                           id="abbreviation" name="abbreviation" placeholder="Singkatan satuan"
                                           value="{{ old('abbreviation', isset($unit) ? $unit->abbreviation : '') }}" required>
                                    @error('abbreviation')
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
                        <a href="{{ route('mindo.units.index') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
