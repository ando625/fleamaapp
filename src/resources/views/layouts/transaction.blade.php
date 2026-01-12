<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="{{ route('mypage') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" class="logo-image" width="120">
                </a>
            </div>
        </div>
    </header>

    <main class="main-content @yield('main-class')">
        @yield('content')
    </main>
</body>

</html>