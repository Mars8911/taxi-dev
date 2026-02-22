@extends('admin.layout')

@section('title', '訂單管理')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 fw-semibold">訂單管理</h1>

    {{-- 篩選 --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 g-md-3">
                <div class="col-12 col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="搜尋地址、乘客"
                        class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">全部狀態</option>
                        <option value="matching" {{ request('status') === 'matching' ? 'selected' : '' }}>配對中</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>進行中</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>已完成</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>已取消</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search me-1"></i>搜尋
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>乘客</th>
                            <th class="d-none d-lg-table-cell">司機</th>
                            <th>起點</th>
                            <th>終點</th>
                            <th class="d-none d-md-table-cell">距離</th>
                            <th>車資</th>
                            <th>狀態</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>
                                {{ $order->passenger?->name ?? '-' }}
                                @if($order->passenger?->phone)
                                <br><small class="text-muted">{{ $order->passenger->phone }}</small>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $order->driver?->name ?? '-' }}</td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 100px;" title="{{ $order->start_location }}">{{ Str::limit($order->start_location, 12) }}</span></td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 100px;" title="{{ $order->end_location }}">{{ Str::limit($order->end_location, 12) }}</span></td>
                            <td class="d-none d-md-table-cell">{{ $order->distance }} km</td>
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
                            <td colspan="8" class="text-center text-muted py-5">尚無訂單</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
