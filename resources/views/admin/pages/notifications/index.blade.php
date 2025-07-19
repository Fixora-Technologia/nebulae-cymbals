@extends('admin.layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Notifications</h3>

                        <div class="card-tools">
                            @if ($notifications->where('read_at', null)->count() > 0)
                                <form action="{{ route('mindo.notifications.read-all') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-check-double"></i> Mark All as Read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($notifications->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                                <h5>No notifications found</h5>
                                <p class="text-muted">You don't have any notifications at the moment.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="15%">Date</th>
                                            <th width="15%">Type</th>
                                            <th>Message</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($notifications as $notification)
                                            <tr
                                                class="{{ is_null($notification->read_at) ? 'table-light font-weight-bold' : '' }}">
                                                <td>{{ $notification->created_at->format('d M Y H:i') }}</td>
                                                <td>
                                                    @if (str_contains($notification->type, 'LowStockNotification'))
                                                        <span class="badge badge-warning text-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Low Stock
                                                        </span>
                                                    @else
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-info-circle"></i> System
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (str_contains($notification->type, 'LowStockNotification'))
                                                        <strong>{{ $notification->data['product_name'] }}</strong>
                                                        ({{ $notification->data['product_sku'] }})
                                                        is running low on stock.
                                                        <br>
                                                        <small class="text-muted">
                                                            Current stock: {{ $notification->data['current_stock'] }} |
                                                            Minimum threshold: {{ $notification->data['min_stock'] }}
                                                        </small>
                                                        <div class="mt-1">
                                                            <a href="{{ route('mindo.products.edit', $notification->data['product_id']) }}"
                                                                class="btn btn-xs btn-outline-primary">
                                                                <i class="fas fa-eye"></i> View Product
                                                            </a>
                                                        </div>
                                                    @else
                                                        {{ $notification->data['message'] ?? 'System notification' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (is_null($notification->read_at))
                                                        <span class="badge badge-secondary text-secondary">Unread</span>
                                                    @else
                                                        <span class="badge badge-success text-success">Read</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if (is_null($notification->read_at))
                                                            <form
                                                                action="{{ route('mindo.notifications.read', $notification->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-info"
                                                                    title="Mark as Read">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form
                                                            action="{{ route('mindo.notifications.destroy', $notification->id) }}"
                                                            method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $notifications->links() }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this notification?')) {
                    this.submit();
                }
            });
        });
    </script>
@endpush
