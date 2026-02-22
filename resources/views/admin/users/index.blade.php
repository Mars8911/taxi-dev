@extends('admin.layout')

@section('title', '會員管理')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 fw-semibold">會員管理</h1>

    {{-- 篩選 --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 g-md-3">
                <div class="col-12 col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="搜尋姓名、Email、手機"
                        class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">全部角色</option>
                        <option value="passenger" {{ request('role') === 'passenger' ? 'selected' : '' }}>乘客</option>
                        <option value="driver" {{ request('role') === 'driver' ? 'selected' : '' }}>司機</option>
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
                            <th>姓名</th>
                            <th>Email</th>
                            <th class="d-none d-md-table-cell">手機</th>
                            <th>角色</th>
                            <th>黑名單</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td><small>{{ $user->email }}</small></td>
                            <td class="d-none d-md-table-cell">{{ $user->phone ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $user->role === 'driver' ? 'bg-success' : 'bg-primary' }}">
                                    {{ $user->role === 'driver' ? '司機' : '乘客' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $user->is_blacklisted ? 'bg-danger' : 'bg-secondary' }}">
                                    {{ $user->is_blacklisted ? '是' : '否' }}
                                </span>
                            </td>
                            <td>
                                @if (!$user->is_admin)
                                <form method="POST" action="{{ route('admin.users.toggle-blacklist', $user) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $user->is_blacklisted ? 'btn-success' : 'btn-outline-danger' }}">
                                        {{ $user->is_blacklisted ? '解除' : '黑名單' }}
                                    </button>
                                </form>
                                @else
                                <span class="text-muted small">管理員</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">尚無會員</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
