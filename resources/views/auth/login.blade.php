<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #f0f4ff, #dce3f7);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }
        .login-header {
            background-color: #3f51b5;
            color: white;
            text-align: center;
            padding: 25px 20px;
        }
        .login-header img {
            max-width: 150px;
            margin-bottom: 4px;
        }
        .btn-custom {
            background-color: #3f51b5;
            color: white;
            border: 1px solid;
        }
        .btn-custom:hover {
            background-color: #ffffff;
            border: 1px solid #3f51b5;
            color: #3f51b5;
        }
        .form-control:focus {
            border-color: #3f51b5;
            box-shadow: 0 0 0 0.2rem rgba(63, 81, 181, 0.25);
        }
        .forgot-link {
            color: #3f51b5;
            text-decoration: none;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 576px) {
            .login-header {
                padding: 20px 10px;
            }
            .login-header h3 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <!-- Ganti URL logo sesuai kebutuhan -->
        <img src="storage/assets/simantap.png" alt="Logo">
        <h3 class="mt-2">Welcome Back</h3>
        <p class="mb-0">Please login to your account</p>
    </div>
    <div class="p-4">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       required autofocus autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- checkbox show pass --}}
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" id="showPassword">
                <label class="form-check-label" for="showPassword">
                    Tampilkan Password
                </label>
            </div>


            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-3">

                    <a class="forgot-link">
                        Lupa Password? Silakan Hubungi Admin!
                    </a>
            </div>

            <!-- Button -->
            <button type="submit" class="btn btn-custom w-100 py-2">Log In</button>
        </form>
    </div>
</div>

<script>
    // Pastikan skrip berjalan setelah halaman dimuat sepenuhnya
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen password dan checkbox berdasarkan ID-nya
        const passwordInput = document.getElementById('password');
        const showPasswordCheckbox = document.getElementById('showPassword');

        // Tambahkan 'event listener' saat checkbox diubah (dicentang/tidak)
        showPasswordCheckbox.addEventListener('change', function() {
            // Ubah tipe input password berdasarkan status checkbox
            // Jika dicentang (this.checked), tipe jadi 'text', jika tidak, jadi 'password'
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    });
</script>


</body>
</html>
