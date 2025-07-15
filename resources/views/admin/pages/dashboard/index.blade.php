@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Dashboard', // Dynamic title
        'breadcrumbs' => [['name' => 'Home', 'url' => route('mindo.home')], ['name' => 'Dashboard', 'url' => '#']],
    ])
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-info elevation-1"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Barang Masuk</span>
                        <span class="info-box-number">
                            {{ number_format($stockInQty, 0, ',', '.') }} pcs
                            <br>
                            Rp {{ number_format($stockInValue, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-newspaper"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Barang Keluar</span>
                        <span class="info-box-number">
                            {{ number_format($stockOutQty, 0, ',', '.') }} pcs
                            <br>
                            Rp {{ number_format($stockOutValue, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Content Statistics -->
            {{-- <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fa fa-newspaper"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Transaksi Barang Keluar</span>
                        <span class="info-box-number"></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-envelope"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Kontak</span>
                        <span class="info-box-number"></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary elevation-1"><i class="fa fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Kegiatan</span>
                        <span class="info-box-number"></span>
                    </div>
                </div>
            </div>
            </div> --}}

            <!-- Sales Statistics Row -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Monthly Sales</h3>
                            <div class="card-tools">
                                <div class="input-group">
                                    <select id="sales-month" class="form-control">
                                        @foreach (range(1, 12) as $month)
                                            <option value="{{ $month }}" {{ date('n') == $month ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select id="sales-year" class="form-control ml-2">
                                        @foreach (range(date('Y') - 2, date('Y')) as $year)
                                            <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button id="update-sales-chart" class="btn btn-primary">
                                            <i class="fas fa-sync-alt"></i> Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="chart-container" style="position: relative; height:300px;">
                                        <canvas id="monthlySalesQuantityChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="chart-container" style="position: relative; height:300px;">
                                        <canvas id="monthlySalesValueChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products Donut Chart Row -->
            <div class="row mt-4">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top 10 Best Selling Products (Qty)</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="topProductsDonutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top 5 Best Selling Categories (Qty)</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="topCategoriesDonutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Comparison Row -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Perbandingan Penjualan (12 Bulan Terakhir)</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:400px;">
                                <canvas id="salesComparisonChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/js/dashboard-charts.js'])
    @endpush
