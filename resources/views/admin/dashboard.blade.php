@extends('admin.layout')

@section('title', '儀表板')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 fw-semibold">儀表板</h1>

    {{-- 統計卡片 --}}
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">總會員數</p>
                    <p class="h4 mb-0 fw-bold">{{ $stats['users_total'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
                <div class="card-body">
                    <p class="text-muted small mb-1">乘客</p>
                    <p class="h4 mb-0 fw-bold text-primary">{{ $stats['users_passenger'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
                <div class="card-body">
                    <p class="text-muted small mb-1">司機</p>
                    <p class="h4 mb-0 fw-bold text-success">{{ $stats['users_driver'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">總訂單數</p>
                    <p class="h4 mb-0 fw-bold">{{ $stats['orders_total'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body">
                    <p class="text-muted small mb-1">待接單</p>
                    <p class="h4 mb-0 fw-bold text-warning">{{ $stats['orders_matching'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 border-start border-info border-4">
                <div class="card-body">
                    <p class="text-muted small mb-1">已完成</p>
                    <p class="h4 mb-0 fw-bold text-info">{{ $stats['orders_completed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 最近訂單 --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <h2 class="h6 mb-0 fw-semibold">最近訂單</h2>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-right-circle me-1"></i>檢視全部
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">ID</th>
                            <th>乘客</th>
                            <th>起點</th>
                            <th>終點</th>
                            <th>車資</th>
                            <th>狀態</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->passenger?->name ?? '-' }}</td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 120px;" title="{{ $order->start_location }}">{{ Str::limit($order->start_location, 15) }}</span></td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 120px;" title="{{ $order->end_location }}">{{ Str::limit($order->end_location, 15) }}</span></td>
                            <td>{{ $order->total_price }} 元</td>
                            <td>
                                @php
                                    $badge = match($order->status) {
                                        'matching' => 'warning',
                                        'ongoing' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $order->status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">尚無訂單</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
