@extends('admin.layouts.app')

@section('title', isset($customer) ? 'Edit Pelanggan' : 'Buat Pelanggan')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => isset($customer) ? 'Edit Pelanggan' : 'Buat Pelanggan',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Pelanggan', 'url' => route('mindo.customers.index')],
            ['name' => isset($customer) ? 'Edit Pelanggan' : 'Buat Pelanggan', 'url' => ''],
        ],
    ])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($customer) ? 'Edit Pelanggan' : 'Buat Pelanggan' }}</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form
                    action="{{ isset($customer) ? route('mindo.customers.update', $customer->id) : route('mindo.customers.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($customer))
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Nama <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Nama pelanggan"
                                        value="{{ old('name', isset($customer) ? $customer->name : '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="contact">Kontak</label>
                                    <input type="text" class="form-control @error('contact') is-invalid @enderror"
                                        id="contact" name="contact" placeholder="Kontak pelanggan"
                                        value="{{ old('contact', isset($customer) ? $customer->contact : '') }}">
                                    @error('contact')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone">Telepon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" placeholder="Nomor telepon"
                                        value="{{ old('phone', isset($customer) ? $customer->phone : '') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="Email"
                                        value="{{ old('email', isset($customer) ? $customer->email : '') }}">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="address">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                        placeholder="Alamat pelanggan">{{ old('address', isset($customer) ? $customer->address : '') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="notes">Catatan</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                        placeholder="Catatan tambahan">{{ old('notes', isset($customer) ? $customer->notes : '') }}</textarea>
                                    @error('notes')
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
                        <a href="{{ route('mindo.customers.index') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
