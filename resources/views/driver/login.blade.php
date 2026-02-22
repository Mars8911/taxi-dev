<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>司機登入 - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #3d7ab5 100%); }
        .login-card { box-shadow: 0 1rem 3rem rgba(0,0,0,.175); border-radius: 1rem; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
    <div class="w-100" style="max-width: 400px;">
        <div class="card login-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-badge text-primary" style="font-size: 3rem;"></i>
                    <h1 class="h4 mt-2 mb-1">司機登入</h1>
                    <p class="text-muted small">請使用手機與密碼登入</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('driver.login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="phone" class="form-label">手機號碼</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required autofocus
                                class="form-control" placeholder="0912345678" maxlength="10">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">密碼</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input id="password" name="password" type="password" required class="form-control">
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">記住我</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>登入
                    </button>
                </form>

                <hr class="my-4">
                <p class="text-center text-muted small mb-0">
                    還沒有帳號？<a href="{{ route('driver.register') }}">立即註冊</a>
                </p>
            </div>
        </div>
        <p class="text-center mt-3 mb-0">
            <a href="{{ url('/') }}" class="text-white-50 text-decoration-none"><i class="bi bi-arrow-left me-1"></i>返回首頁</a>
        </p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
