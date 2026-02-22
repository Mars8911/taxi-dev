<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>歡迎來到搭車系統 - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 40%, #3d7ab5 100%);
            display: flex;
            align-items: center;
        }
        .hero-title { font-size: clamp(1.75rem, 5vw, 2.75rem); }
        .hero-subtitle { font-size: clamp(0.95rem, 2.5vw, 1.15rem); opacity: 0.95; }
        .action-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 1rem;
        }
        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,.15);
        }
        .action-card .card-body { padding: 1.5rem 1.75rem; }
    </style>
</head>
<body>
    <section class="hero-section py-5">
        <div class="container">
            <div class="text-center text-white mb-5">
                <i class="bi bi-car-front-fill display-4 mb-3 opacity-90"></i>
                <h1 class="hero-title fw-bold mb-3">歡迎來到搭車系統</h1>
                <p class="hero-subtitle mb-0">安全、便捷的智慧叫車服務</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-12 col-sm-8 col-md-5 col-lg-4">
                    <a href="{{ route('passenger.login') }}" class="text-decoration-none">
                        <div class="card action-card bg-white shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-geo-alt-fill text-primary mb-2" style="font-size: 2rem;"></i>
                                <h5 class="fw-semibold text-dark mb-2">乘客登入</h5>
                                <p class="text-muted small mb-0">乘客註冊與登入叫車</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-sm-8 col-md-5 col-lg-4">
                    <a href="{{ route('driver.login') }}" class="text-decoration-none">
                        <div class="card action-card bg-white shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-person-badge text-primary mb-2" style="font-size: 2rem;"></i>
                                <h5 class="fw-semibold text-dark mb-2">司機登入</h5>
                                <p class="text-muted small mb-0">司機註冊與登入接單</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="text-center mt-5 pt-4">
                <p class="text-white-50 small mb-0">每公里 50 元 · 即時配對 · 安全可靠</p>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
