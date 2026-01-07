<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録 - CoachTech</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>
    <header class="header">
        <div class="header-container">
            <a href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" class="logo-image">
            </a>
        </div>
    </header>

    <main class="main">
        <div class="register-container">
            <h1 class="register-title">会員登録</h1>
            <form action="/register" method="POST" class="register-form">
                @csrf
                <div class="form-group">
                    <label for="username" class="form-label">ユーザー名</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        value="{{ old('name') }}">
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">メールアドレス</label>
                    <input
                        type="text"
                        id="email"
                        name="email"
                        class="form-input"
                        value="{{ old('email') }}"
                        novalidate
                    >
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">パスワード</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input">
                    @error('password')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">確認用パスワード</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input">
                    @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="register-button">登録する</button>
            </form>
            <div class="login-link-container">
                <a href="/login" class="login-link">ログインはこちら</a>
            </div>
        </div>
    </main>
</body>

</html>