<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" class="logo-image" width="120">
                </a>
            </div>
            <!-- 検索ホーム -->
            <div class="search-container">
                <input type="text" class="search-input" placeholder="なにをお探しですか？">
            </div>
            <!-- ナビゲーション　-->
            <nav class="header-nav">
                <ul class="header-row">
                    @if (Auth::check())
                    <!-- ログインしている時　 -->
                     <li class="header-nav__item">
                        <form action="/logout" method="post">
                            @csrf
                            <button type="submit" class="header-nav__button">ログアウト</button>
                        </form>
                    </li>
                    <li class="heder-nav__item">
                        <a href="/mypage" class="header-nav__link">マイページ</a>
                    </li>
                    <li class="header-nav__item">
                        <a href="/sell" class="header-nav__link nav-button">出品</a>
                    </li>
                    
                    @else
                    <!-- ログインしてない時　-->
                    <li class="header-nav__item">
                        <a href="/login" class="header-nav__link">ログイン</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="/mypage">マイページ</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link nav-button" href="/sell">出品</a>
                    </li>
                @endif
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        @yield('content')
    </main>
</body>
</html>