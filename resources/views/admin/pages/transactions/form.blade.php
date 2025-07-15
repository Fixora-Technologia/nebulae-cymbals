@extends('admin.layouts.app')

@section('title', 'Buat Transaksi')

@section('subheader')
    @include('admin.partials.subheader', [
        'title' => 'Buat Transaksi',
        'breadcrumbs' => [
            ['name' => 'Dashboard', 'url' => route('mindo.home')],
            ['name' => 'Transaksi', 'url' => route('mindo.transactions.index')],
            ['name' => 'Buat Transaksi', 'url' => route('mindo.transactions.create')],
        ],
    ])
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Transaction Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Detail Transaksi</h3>
                </div>
                <form id="transactionForm" action="{{ route('mindo.transactions.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="transaction_date">Tanggal Transaksi <span
                                            class="text-danger">*</span></label>
                                    <input type="date"
                                        class="form-control @error('transaction_date') is-invalid @enderror"
                                        id="transaction_date" name="transaction_date"
                                        value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                    @error('transaction_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="transaction_code">Kode Transaksi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('transaction_code') is-invalid @enderror" 
                                               id="transaction_code" name="transaction_code" placeholder="Kode transaksi"
                                               value="{{ old('transaction_code', isset($defaultTransactionCode) ? $defaultTransactionCode : '') }}" required>
                                        <button class="btn btn-outline-secondary" type="button" id="regenerate-code">Regenerate</button>
                                    </div>
                                    <small class="form-text text-muted">Format: TRX-YYXDD-NNNN (X=bulan dalam huruf A-L)</small>
                                    @error('transaction_code')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="transaction_type">Tipe Transaksi <span class="text-danger">*</span></label>
                                    <select class="form-select @error('transaction_type') is-invalid @enderror"
                                        id="transaction_type" name="transaction_type" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="in" {{ old('transaction_type') == 'in' ? 'selected' : '' }}>Masuk
                                        </option>
                                        <option value="out" {{ old('transaction_type') == 'out' ? 'selected' : '' }}>
                                            Keluar</option>
                                    </select>
                                    @error('transaction_type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3 customer-section" style="display: none;">
                                    <label for="customer_id">Pelanggan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id"
                                        name="customer_id">
                                        <option value="">Pilih Pelanggan</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="notes">Catatan</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                        placeholder="Catatan transaksi">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End Transaction Form -->

            <!-- Transaction Items -->
            <div class="card mb-4 transaction-items-section" style="display: none;">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Item Transaksi</h3>
                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                        <i class="fa fa-plus"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="items-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga Satuan</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be added here dynamically -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th id="total-amount">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- End Transaction Items -->

            <!-- Submit Button -->
            <div class="card mb-4 transaction-items-section" style="display: none;">
                <div class="card-body text-center">
                    <button type="button" id="submitTransactionBtn" class="btn btn-primary btn-lg">
                        <i class="fa fa-save"></i> Simpan Transaksi
                    </button>
                    <a href="{{ route('mindo.transactions.index') }}" class="btn btn-default btn-lg">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <!-- End Submit Button -->
        </div>
    </div>

    <!-- Item Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Tambah Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="itemForm">
                        <div class="form-group mb-3">
                            <label for="product_id">Produk <span class="text-danger">*</span></label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Pilih Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock }}" data-unit="{{ $product->unit->abbreviation }}">
                                        {{ $product->name }} ({{ $product->sku }}) - Stok: {{ $product->stock }}
                                        {{ $product->unit->abbreviation }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback product-feedback"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="unit_price">Harga Satuan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="unit_price" name="unit_price" required>
                                <div class="invalid-feedback price-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="quantity">Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                    min="1" value="1" required>
                                <span class="input-group-text unit-text">Unit</span>
                                <div class="invalid-feedback quantity-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="subtotal">Subtotal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="subtotal" name="subtotal" readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveItemBtn">Simpan Item</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const transactionType = document.getElementById('transaction_type');
            const customerSection = document.querySelector('.customer-section');
            const transactionItemsSection = document.querySelectorAll('.transaction-items-section');
            const itemsTable = document.getElementById('items-table').getElementsByTagName('tbody')[0];
            const totalAmount = document.getElementById('total-amount');
            const itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
            const productSelect = document.getElementById('product_id');
            const unitPriceInput = document.getElementById('unit_price');
            const quantityInput = document.getElementById('quantity');
            const subtotalInput = document.getElementById('subtotal');
            const unitText = document.querySelector('.unit-text');
            const submitTransactionBtn = document.getElementById('submitTransactionBtn');
            const transactionForm = document.getElementById('transactionForm');

            let items = [];
            let editingItemIndex = -1;

            // Show/hide customer section based on transaction type
            transactionType.addEventListener('change', function() {
                if (this.value === 'out') {
                    customerSection.style.display = 'block';
                    document.getElementById('customer_id').setAttribute('required', 'required');
                } else {
                    customerSection.style.display = 'none';
                    document.getElementById('customer_id').removeAttribute('required');
                }

                if (this.value) {
                    transactionItemsSection.forEach(section => {
                        section.style.display = 'block';
                    });
                } else {
                    transactionItemsSection.forEach(section => {
                        section.style.display = 'none';
                    });
                }
            });

            // Show product price when selected
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const price = selectedOption.getAttribute('data-price');
                    const unit = selectedOption.getAttribute('data-unit');
                    unitPriceInput.value = price;
                    unitText.textContent = unit;
                    calculateSubtotal();
                } else {
                    unitPriceInput.value = '';
                    unitText.textContent = 'Unit';
                    subtotalInput.value = '';
                }
            });

            // Calculate subtotal when price or quantity changes
            [unitPriceInput, quantityInput].forEach(input => {
                input.addEventListener('input', calculateSubtotal);
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

            // Add Item Button
            document.getElementById('addItemBtn').addEventListener('click', function() {
                resetItemForm();
                editingItemIndex = -1;
                itemModal.show();
            });

            // Save Item Button
            document.getElementById('saveItemBtn').addEventListener('click', function() {
                if (validateItemForm()) {
                    const productId = productSelect.value;
                    const productOption = productSelect.options[productSelect.selectedIndex];
                    const productName = productOption.text;
                    const unitPrice = parseFloat(unitPriceInput.value);
                    const quantity = parseInt(quantityInput.value);
                    const subtotal = unitPrice * quantity;
                    const unit = productOption.getAttribute('data-unit');

                    const item = {
                        product_id: productId,
                        product_name: productName,
                        unit_price: unitPrice,
                        quantity: quantity,
                        subtotal: subtotal,
                        unit: unit
                    };

                    if (editingItemIndex >= 0) {
                        items[editingItemIndex] = item;
                    } else {
                        items.push(item);
                    }

                    renderItems();
                    itemModal.hide();
                }
            });

            // Render items to table
            function renderItems() {
                itemsTable.innerHTML = '';
                let total = 0;

                items.forEach((item, index) => {
                    const row = itemsTable.insertRow();
                    row.innerHTML = `
                    <td>${item.product_name}</td>
                    <td>Rp ${formatNumber(item.unit_price)}</td>
                    <td>${item.quantity} ${item.unit}</td>
                    <td>Rp ${formatNumber(item.subtotal)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning edit-item" data-index="${index}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-item" data-index="${index}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;
                    total += item.subtotal;
                });

                totalAmount.textContent = `Rp ${formatNumber(total)}`;

                // Add event listeners for edit and delete buttons
                document.querySelectorAll('.edit-item').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        editItem(index);
                    });
                });

                document.querySelectorAll('.delete-item').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        deleteItem(index);
                    });
                });
            }

            // Edit item
            function editItem(index) {
                editingItemIndex = index;
                const item = items[index];

                productSelect.value = item.product_id;
                unitPriceInput.value = item.unit_price;
                quantityInput.value = item.quantity;
                subtotalInput.value = item.subtotal;
                unitText.textContent = item.unit;

                itemModal.show();
            }

            // Delete item
            function deleteItem(index) {
                items.splice(index, 1);
                renderItems();
            }

            // Reset item form
            function resetItemForm() {
                document.getElementById('itemForm').reset();
                productSelect.classList.remove('is-invalid');
                unitPriceInput.classList.remove('is-invalid');
                quantityInput.classList.remove('is-invalid');
                unitText.textContent = 'Unit';
            }

            // Validate item form
            function validateItemForm() {
                let isValid = true;

                if (!productSelect.value) {
                    productSelect.classList.add('is-invalid');
                    document.querySelector('.product-feedback').textContent = 'Produk harus dipilih';
                    isValid = false;
                } else {
                    productSelect.classList.remove('is-invalid');
                }

                if (!unitPriceInput.value || parseFloat(unitPriceInput.value) <= 0) {
                    unitPriceInput.classList.add('is-invalid');
                    document.querySelector('.price-feedback').textContent = 'Harga harus lebih dari 0';
                    isValid = false;
                } else {
                    unitPriceInput.classList.remove('is-invalid');
                }

                if (!quantityInput.value || parseInt(quantityInput.value) <= 0) {
                    quantityInput.classList.add('is-invalid');
                    document.querySelector('.quantity-feedback').textContent = 'Jumlah harus lebih dari 0';
                    isValid = false;
                } else {
                    // Check stock availability for outgoing transactions
                    if (transactionType.value === 'out') {
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        const stock = parseInt(selectedOption.getAttribute('data-stock'));
                        const quantity = parseInt(quantityInput.value);

                        // Only check if we're not editing the same item
                        if (editingItemIndex >= 0) {
                            const currentItem = items[editingItemIndex];
                            if (currentItem.product_id === productSelect.value && currentItem.quantity ===
                                quantity) {
                                // Same item, same quantity, no need to check stock
                            } else if (quantity > stock) {
                                quantityInput.classList.add('is-invalid');
                                document.querySelector('.quantity-feedback').textContent =
                                    `Stok tidak cukup. Tersedia: ${stock}`;
                                isValid = false;
                            }
                        } else if (quantity > stock) {
                            quantityInput.classList.add('is-invalid');
                            document.querySelector('.quantity-feedback').textContent =
                                `Stok tidak cukup. Tersedia: ${stock}`;
                            isValid = false;
                        }
                    }

                    if (isValid) {
                        quantityInput.classList.remove('is-invalid');
                    }
                }

                return isValid;
            }

            // Format number with thousand separator
            function formatNumber(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Submit transaction
            submitTransactionBtn.addEventListener('click', function() {
                if (validateTransaction()) {
                    // Add hidden inputs for items
                    const form = transactionForm;

                    // Clear any previous items
                    const previousItems = form.querySelectorAll('input[name^="items"]');
                    previousItems.forEach(item => item.remove());

                    // Add items to the form
                    items.forEach((item, index) => {
                        Object.entries(item).forEach(([key, value]) => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = `items[${index}][${key}]`;
                            input.value = value;
                            form.appendChild(input);
                        });
                    });

                    form.submit();
                }
            });

            // Validate transaction
            function validateTransaction() {
                let isValid = true;

                // Check if transaction type is selected
                if (!transactionType.value) {
                    transactionType.classList.add('is-invalid');
                    isValid = false;
                } else {
                    transactionType.classList.remove('is-invalid');
                }

                // Check if customer is selected for outgoing transactions
                if (transactionType.value === 'out') {
                    const customerId = document.getElementById('customer_id');
                    if (!customerId.value) {
                        customerId.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        customerId.classList.remove('is-invalid');
                    }
                }

                // Check if there are items
                if (items.length === 0) {
                    alert('Transaksi harus memiliki minimal 1 item');
                    isValid = false;
                }

                return isValid;
            }

            // Initialize customer section based on initial transaction type
            if (transactionType.value === 'out') {
                customerSection.style.display = 'block';
                document.getElementById('customer_id').setAttribute('required', 'required');
            }

            // Initialize items section based on initial transaction type
            if (transactionType.value) {
                transactionItemsSection.forEach(section => {
                    section.style.display = 'block';
                });
            }
        });

        // Transaction Code Regeneration
        document.getElementById('regenerate-code').addEventListener('click', function() {
            fetch('{{ route("mindo.transactions.generate-code") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('transaction_code').value = data.transaction_code;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
@endpush
