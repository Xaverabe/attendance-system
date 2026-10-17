<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: min(100%, 440px);
            background: rgba(255,255,255,0.96);
            border-radius: 18px;
            box-shadow: 0 20px 45px rgba(15,32,62,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card mx-auto p-4 p-md-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-1">Attendance Monitor</h2>
                <p class="text-muted mb-0">Teacher and Admin portal</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" class="form-control form-control-lg" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" class="form-control form-control-lg" required>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
            </form>

            <div class="mt-4 small text-muted text-center">
                Demo accounts:<br>
                Admin: admin@attendance.test / password<br>
                Teacher: teacher@attendance.test / password
            </div>
        </div>
    </div>
</body>
</html>
