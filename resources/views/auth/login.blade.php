<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Nhân viên</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #ffe0b2, #fff3e0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            background-color: #fff;
        }

        .login-header {
            background: #ff6f00;
            color: #fff;
            padding: 1.5rem;
            text-align: center;
        }

        .login-body {
            padding: 1.75rem;
        }

        .form-control:focus {
            border-color: #ff6f00;
            box-shadow: 0 0 0 0.15rem rgba(255, 111, 0, 0.25);
        }

        .btn-orange {
            background-color: #ff6f00;
            border-color: #ff6f00;
        }

        .btn-orange:hover {
            background-color: #e65100;
            border-color: #e65100;
        }

        .alert-danger {
            font-size: 0.9rem;
            padding: 0.5rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <h4 class="mb-0">Đăng nhập Nhân viên FANI</h4>
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email đăng nhập</label>
                    <input type="email" class="form-control" name="email" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger text-center">{{ $errors->first() }}</div>
                @endif

                <div class="d-grid">
                    <button type="submit" class="btn btn-orange text-white">Đăng nhập</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
