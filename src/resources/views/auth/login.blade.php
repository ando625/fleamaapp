<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <header class="header">
        <div class="header-container">
            <a href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" class="logo-image">
            </a>
        </div>
    </header>

    <main class="main-content">
        <div class="login-container">
            <h1 class="login-title">ログイン</h1>

            <form class="login-form" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">メールアドレス</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-input">
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">パスワード</label>
                    <input type="password" id="password" name="password" value="{{ old('password') }}" class="form-input">
                    @error('password')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="login-button">ログインする</button>
            </form>

            <div class="register-link">
                <a href="/register" class="register-link-text">会員登録はこちら</a>
            </div>
        </div>
    </main>
</body>

</html>