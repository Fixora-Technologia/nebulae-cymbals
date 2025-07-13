@extends('admin.layouts.app')

@section('title', 'Detail Transaksi')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Detail Transaksi',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Transaksi', 'url' => route('mindo.transactions.index')],
            ['name' => 'Detail Transaksi', 'url' => ''],
        ],
    ])
@endsection

@section('content')
    @include('admin.components.flash-message')

    <!-- Transaction Details -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Informasi Transaksi</h3>
                    <div>
                        <a href="{{ route('mindo.transactions.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        @can('TRANSACTION_DELETE')
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteConfirmationModal" 
                                data-item-id="{{ $transaction->id }}"
                                data-item-name="{{ $transaction->transaction_code }}"
                                data-delete-route="{{ route('mindo.transactions.destroy', $transaction->id) }}">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        @endcan
                        <a href="#" class="btn btn-sm btn-info" onclick="window.print()">
                            <i class="fa fa-print"></i> Cetak
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">Kode Transaksi</th>
                                    <td><strong>{{ $transaction->transaction_code }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Transaksi</th>
                                    <td>{{ $transaction->transaction_date ? date('d M Y', strtotime($transaction->transaction_date)) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe Transaksi</th>
                                    <td>
                                        @if ($transaction->transaction_type == 'in')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Nilai</th>
                                    <td><strong>Rp {{ number_format($transaction->total_value, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">Pelanggan</th>
                                    <td>
                                        @if ($transaction->customer)
                                            <a href="{{ route('mindo.customers.show', $transaction->customer_id) }}">
                                                {{ $transaction->customer->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>{{ $transaction->user ? $transaction->user->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Catatan</th>
                                    <td>{{ $transaction->notes ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td>{{ $transaction->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Items -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Item Transaksi</h3>
                    @can('TRANSACTION_ITEM_ADD')
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="fa fa-plus"></i> Tambah Item
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Produk</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Subtotal</th>
                                @canany(['TRANSACTION_ITEM_DELETE'])
                                    <th class="text-center">Aksi</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaction->items as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('mindo.products.show', $item->product_id) }}">
                                            {{ $item->product->name }} ({{ $item->product->code }})
                                        </a>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->quantity }} {{ $item->product->unit->abbreviation }}</td>
                                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    @canany(['TRANSACTION_ITEM_DELETE'])
                                        <td class="text-center">
                                            @can('TRANSACTION_ITEM_DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteItemModal" data-item-id="{{ $item->id }}"
                                                    data-product-name="{{ $item->product->name }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('TRANSACTION_ITEM_DELETE') ? '6' : '5' }}" class="text-center">
                                        Tidak ada item transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="{{ Auth::user()->can('TRANSACTION_ITEM_DELETE') ? '4' : '4' }}" class="text-end">Total:</th>
                                <th class="text-end">Rp {{ number_format($transaction->total_value, 0, ',', '.') }}</th>
                                @canany(['TRANSACTION_ITEM_DELETE'])
                                    <th></th>
                                @endcanany
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Transaction Modal -->
    @include('admin.components.delete-confirmation-modal')

    <!-- Delete Item Modal -->
    <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteItemModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus item "<span id="itemName"></span>"?</p>
                    <p class="text-danger"><small>Tindakan ini akan mempengaruhi stok dan tidak dapat dikembalikan.</small></p>
                </div>
                <div class="modal-footer">
                    <form id="deleteItemForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Tambah Item Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('mindo.transaction-items.store') }}" method="POST" id="addItemForm">
                    @csrf
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="product_id">Produk <span class="text-danger">*</span></label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Pilih Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}" data-unit="{{ $product->unit->abbreviation }}">
                                        {{ $product->name }} ({{ $product->sku }}) - Stok: {{ $product->stock }} {{ $product->unit->abbreviation }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="unit_price">Harga Satuan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="unit_price" name="unit_price" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="quantity">Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                                <span class="input-group-text unit-text">Unit</span>
                            </div>
                            <small class="text-danger stock-warning" style="display: none;">
                                Stok tidak mencukupi!
                            </small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="subtotal">Subtotal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="subtotal" name="subtotal" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="saveItemBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete item modal
        const deleteItemModal = document.getElementById('deleteItemModal');
        if (deleteItemModal) {
            deleteItemModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const itemId = button.getAttribute('data-item-id');
                const productName = button.getAttribute('data-product-name');
                
                document.getElementById('itemName').textContent = productName;
                document.getElementById('deleteItemForm').action = `{{ url('mindo/transaction-items') }}/${itemId}`;
            });
        }
        
        // Add item modal
        const addItemModal = document.getElementById('addItemModal');
        if (addItemModal) {
            const productSelect = document.getElementById('product_id');
            const unitPriceInput = document.getElementById('unit_price');
            const quantityInput = document.getElementById('quantity');
            const subtotalInput = document.getElementById('subtotal');
            const unitText = document.querySelector('.unit-text');
            const stockWarning = document.querySelector('.stock-warning');
            const saveItemBtn = document.getElementById('saveItemBtn');
            const transactionType = '{{ $transaction->transaction_type }}';
            
            // Show product price when selected
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const price = selectedOption.getAttribute('data-price');
                    const unit = selectedOption.getAttribute('data-unit');
                    unitPriceInput.value = price;
                    unitText.textContent = unit;
                    calculateSubtotal();
                    
                    // Check stock for outgoing transactions
                    if (transactionType === 'out') {
                        const stock = parseInt(selectedOption.getAttribute('data-stock'));
                        const quantity = parseInt(quantityInput.value);
                        
                        if (quantity > stock) {
                            stockWarning.style.display = 'block';
                            saveItemBtn.disabled = true;
                        } else {
                            stockWarning.style.display = 'none';
                            saveItemBtn.disabled = false;
                        }
                    }
                } else {
                    unitPriceInput.value = '';
                    unitText.textContent = 'Unit';
                    subtotalInput.value = '';
                    stockWarning.style.display = 'none';
                    saveItemBtn.disabled = false;
                }
            });
            
            // Calculate subtotal when price or quantity changes
            [unitPriceInput, quantityInput].forEach(input => {
                input.addEventListener('input', function() {
                    calculateSubtotal();
                    
                    // Check stock for outgoing transactions
                    if (transactionType === 'out' && productSelect.value) {
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        const stock = parseInt(selectedOption.getAttribute('data-stock'));
                        const quantity = parseInt(quantityInput.value);
                        
                        if (quantity > stock) {
                            stockWarning.style.display = 'block';
                            saveItemBtn.disabled = true;
                        } else {
                            stockWarning.style.display = 'none';
                            saveItemBtn.disabled = false;
                        }
                    }
                });
            });
            
            function calculateSubtotal() {
                if (unitPriceInput.value && quantityInput.value) {
                    const price = parseFloat(unitPriceInput.value);
                    const quantity = parseFloat(quantityInput.value);
                    subtotalInput.value = (price * quantity).toFixed(0);
                } else {
                    subtotalInput.value = '';
                }
            }
        }
    });
</script>
@endpush
