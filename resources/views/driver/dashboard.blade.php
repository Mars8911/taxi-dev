<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>司機儀表板 - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: #f8f9fa; }
        .order-card { transition: box-shadow 0.2s; }
        .order-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('driver.dashboard') }}">
                <i class="bi bi-person-badge me-2"></i>司機儀表板
            </a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small"><i class="bi bi-telephone me-1"></i>{{ auth()->user()->phone ?? '-' }}</span>
                <form method="POST" action="{{ route('driver.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">登出</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <!-- 歡迎區塊 -->
        <div class="card shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">歡迎，{{ auth()->user()->name ?? '司機' }}</h5>
                        <p class="text-muted small mb-0">待接訂單會顯示在下方，點擊「接單」即可開始行程</p>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnRefresh" title="重新整理">
                        <i class="bi bi-arrow-clockwise"></i> 重新整理
                    </button>
                </div>
            </div>
        </div>

        <!-- 待接訂單列表 -->
        <h6 class="mb-3"><i class="bi bi-list-ul me-1"></i>待接訂單</h6>

        <div id="orderListContainer">
            <!-- 載入中 -->
            <div id="loadingState" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">載入中...</span>
                </div>
                <p class="text-muted mt-2 mb-0">載入訂單中...</p>
            </div>

            <!-- 無訂單 -->
            <div id="emptyState" class="card shadow-sm text-center py-5 d-none">
                <div class="card-body">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">目前沒有待接訂單</p>
                    <p class="text-muted small">乘客叫車後，訂單會出現在這裡</p>
                </div>
            </div>

            <!-- 訂單列表 -->
            <div id="orderList" class="d-none"></div>
        </div>
    </main>

    <p class="text-center py-3 mb-0">
        <a href="{{ url('/') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>返回首頁</a>
    </p>

    <!-- 接單成功 Modal -->
    <div class="modal fade" id="acceptSuccessModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 mb-2">接單成功</h5>
                    <p class="text-muted small mb-0">請前往接客，與乘客確認行程</p>
                    <div id="acceptSuccessInfo" class="text-start mt-4 p-3 bg-light rounded small"></div>
                    <button type="button" class="btn btn-primary mt-4" data-bs-dismiss="modal">確定</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 注入登入司機 ID 供 API 使用
        window.__DRIVER_ID__ = {{ auth()->id() }};
    </script>
    <script>
        (function() {
            const driverId = window.__DRIVER_ID__;
            const orderListEl = document.getElementById('orderList');
            const loadingEl = document.getElementById('loadingState');
            const emptyEl = document.getElementById('emptyState');
            const btnRefresh = document.getElementById('btnRefresh');
            const acceptSuccessModal = new bootstrap.Modal(document.getElementById('acceptSuccessModal'));
            const acceptSuccessInfo = document.getElementById('acceptSuccessInfo');

            function fetchOrders() {
                loadingEl.classList.remove('d-none');
                emptyEl.classList.add('d-none');
                orderListEl.classList.add('d-none');
                orderListEl.innerHTML = '';

                fetch('/api/orders/driver?driver_id=' + driverId)
                    .then(r => r.json())
                    .then(res => {
                        loadingEl.classList.add('d-none');
                        if (!res.success) {
                            alert(res.message || '載入訂單失敗');
                            emptyEl.classList.remove('d-none');
                            return;
                        }
                        const orders = res.data || [];
                        if (orders.length === 0) {
                            emptyEl.classList.remove('d-none');
                            return;
                        }
                        renderOrders(orders);
                        orderListEl.classList.remove('d-none');
                    })
                    .catch(() => {
                        loadingEl.classList.add('d-none');
                        emptyEl.classList.remove('d-none');
                        alert('載入訂單失敗，請稍後再試');
                    });
            }

            function renderOrders(orders) {
                orderListEl.innerHTML = orders.map(o => `
                    <div class="card order-card shadow-sm mb-3" data-order-id="${o.id}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-primary">#${o.id}</span>
                                        ${o.passenger ? `<span class="text-muted small"><i class="bi bi-person me-1"></i>${o.passenger.name || '-'}</span>` : ''}
                                    </div>
                                    <p class="mb-1"><i class="bi bi-geo-alt text-primary me-1"></i>${o.start_location || '-'}</p>
                                    <p class="mb-2 text-muted small"><i class="bi bi-arrow-down me-1"></i>${o.end_location || '-'}</p>
                                    <div class="d-flex gap-3 small text-muted">
                                        <span><i class="bi bi-activity me-1"></i>${o.distance_km || 0} 公里</span>
                                        <span><i class="bi bi-currency-dollar me-1"></i>${o.total_price || 0} 元</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-accept" data-order-id="${o.id}">
                                    <i class="bi bi-check-lg me-1"></i>接單
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');

                orderListEl.querySelectorAll('.btn-accept').forEach(btn => {
                    btn.addEventListener('click', () => acceptOrder(parseInt(btn.dataset.orderId, 10)));
                });
            }

            function acceptOrder(orderId) {
                const btn = orderListEl.querySelector(`.btn-accept[data-order-id="${orderId}"]`);
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>處理中...';
                }

                fetch('/api/orders/' + orderId + '/accept', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ driver_id: driverId }),
                })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            const d = res.data || {};
                            acceptSuccessInfo.innerHTML = `
                                <strong>乘客：</strong>${d.passenger?.name || '-'} ${d.passenger?.phone ? '(' + d.passenger.phone + ')' : ''}<br>
                                <strong>起點：</strong>${d.start_location || '-'}<br>
                                <strong>終點：</strong>${d.end_location || '-'}
                            `;
                            acceptSuccessModal.show();
                            fetchOrders();
                        } else {
                            alert(res.message || '接單失敗');
                            fetchOrders();
                        }
                    })
                    .catch(() => {
                        alert('接單失敗，請稍後再試');
                        fetchOrders();
                    })
                    .finally(() => {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>接單';
                        }
                    });
            }

            btnRefresh.addEventListener('click', fetchOrders);
            document.getElementById('acceptSuccessModal').addEventListener('hidden.bs.modal', () => fetchOrders());

            fetchOrders();
        })();
    </script>
</body>
</html>
