@extends('layouts.transaction')

@section('css')
<link rel="stylesheet" href="{{asset('css/interact.css')}}">
@endsection

@section('content')

<div class="interact-container">

    <!-- 左サイドバー -->
    <aside class="sidebar">
        <h2 class="sidebar-title">その他の取引</h2>
        <nav class="sidebar-nav">
            @foreach ($otherTransactions as $otherTransaction)
            <a href="{{ route('profile.transactions.show', $otherTransaction) }}" class="sidebar-item">
                <span class="item-label">{{$otherTransaction->item->name}}</span>
            </a>
            @endforeach
        </nav>
    </aside>

    <!-- メイン -->
    <div class="main-container">
        @if (session('success'))
            <div class="flash-message success">
                {{ session('success')}}
            </div>
        @endif
        <!-- ヘッダー -->
        <div class="interact-header">
            <div class="user-info">
                <!-- <img src="{{ $partner->profile?->profile_image ? asset('storage/' . $partner->profile->profile_image) : asset('images/default-avatar.png') }}" class="user-avatar"> -->
                @if($partner->profile?->profile_image)
                <img src="{{ asset('storage/' . $partner->profile->profile_image) }}" class="user-avatar">
                @else
                <div class="user-avatar"></div>
                @endif

                <h1>「{{$partner->name}}」さんとの取引画面</h1>
            </div>
            @if(auth()->id() === $transaction->buyer_id && ! $transaction->completed_at)
            <button class="complete-btn">取引を完了する</button>
            @endif
        </div>
        <hr>

        <!-- 商品情報 -->
        <div class="product-info">
            <div class="product-image">
                <img src="{{ asset('storage/' . $item->item_path) }}" alt="">
            </div>
            <div class="product-details">
                <h2 class="product-name">{{$item->name}}</h2>
                <p class="product-price">¥{{$item->price}}</p>
            </div>
        </div>
        <hr>

        <!-- メッセージエリア -->
        <div class="message-area">
            @foreach ($messages as $message)
                @php
                    $isOwn = $message->sender_id === $user->id;
                @endphp

                <div class="message-group {{ $isOwn ? 'own-message' : 'other-message' }}">
                    <div class="message-header">
                        <!-- <img src="{{ $isOwn ? ($user->profile?->profile_image ? asset('storage/' . $user->profile->profile_image) : asset('images/default-avatar.png')) : ($partner->profile?->profile_image ? asset('storage/' . $partner->profile->profile_image) : asset('images/default-avatar.png')) }}" class="message-avatar"> -->
                        @if($isOwn && $user->profile?->profile_image)
                        <img src="{{ asset('storage/' . $user->profile->profile_image) }}" class="message-avatar">
                        @elseif(!$isOwn && $partner->profile?->profile_image)
                        <img src="{{ asset('storage/' . $partner->profile->profile_image) }}" class="message-avatar">
                        @else
                        <div class="message-avatar"></div>
                        @endif
                        <div class="message-name">{{$isOwn ? $user->name : $partner->name}}</div>
                    </div>

                    <div class="message-content">
                        <div class="message-bubble">
                            <div class="message-text" id="message-text-{{ $message->id }}">
                                {{ $message->body }}
                            </div>
                            @if($message->image_path)
                            <div class="message-image">
                                <img src="{{ asset('storage/' . $message->image_path) }}" alt="送信画像">
                            </div>
                            @endif

                            @if($isOwn)
                            <form action="{{ route('profile.messages.update', $message) }}" method="post" class="edit-form" id="edit-form-{{ $message->id }}" style="display: none;">
                                @csrf
                                @method('PUT')
                                <textarea name="body" class="edit-textarea">{{ $message->body }}</textarea>
                                <button type="submit" class="action-btn">保存</button>
                                <button type="button" class="action-btn cancel-btn" data-id="{{ $message->id }}">キャンセル</button>
                            </form>
                            @endif
                        </div>
                        @if($isOwn)
                        <div class="message-actions">
                            <button class="action-btn edit-btn" data-id="{{ $message->id }}">編集</button>
                            <form action="{{ route('profile.messages.destroy', $message) }}" method="post" onsubmit="return confirm('このメッセージを削除しますか？')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button class="action-btn" type="submit">削除</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 入力エリア -->
        <div class="input-area">
            @if ($errors->any())
                <div>
                    @foreach ($errors->all() as $error)
                        <p class="error-text">{{$error}}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('profile.messages.store', $transaction) }}" method="post" enctype="multipart/form-data" class="message-form">
            @csrf
                <textarea name="body" class="message-input" placeholder="取引メッセージを記入してください" rows="1">{{ old('body') }}</textarea>

                <label class="image-upload-btn">
                    画像を追加
                    <input type="file" class="image-input" name="image" accept=".png,.jpeg,.jpg,image/*" onchange="console.log(this.files)">
                </label>

                <button class="send-btn" type="submit">
                    <span class="send-icon">
                        <img src="{{ asset('images/airplane.jpg') }}" alt="送信">
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- 購入者モーダル -->
@if(auth()->id() === $transaction->buyer_id && ! $transaction->completed_at)
<div class="modal-overlay" id="ratingModalBuyer" style="display: none;">
    <div class="modal-content">
        <form action="{{ route('profile.transactions.rate', $transaction) }}" method="post">
            @csrf
            <div class="modal-header"><h2 class="modal-title">取引が完了しました。</h2></div>
            <hr>
            <div class="modal-body">
                <p class="rating-label">今回の取引相手はどうでしたか？</p>
                <input type="hidden" name="rating" id="rating-input-buyer">
                <div class="star-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="star" data-rating="{{ $i }}">★</button>
                    @endfor
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                <button class="submit-btn" type="submit">送信する</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- 出品者モーダル -->
@if(
    auth()->id() === $transaction->seller_id &&
    $transaction->buyer_reviewed &&
    ! $transaction->seller_reviewed &&
    $transaction->completed_at === null
)
<div class="modal-overlay" id="ratingModalSeller" style="display: flex;">
    <div class="modal-content">
        <form action="{{ route('profile.transactions.rate', $transaction) }}" method="post">
            @csrf
            <div class="modal-header"><h2 class="modal-title">取引が完了しました。</h2></div>
            <hr>
            <div class="modal-body">
                <p class="rating-label">今回の取引相手はどうでしたか？</p>
                <input type="hidden" name="rating" id="rating-input-seller">
                <div class="star-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="star" data-rating="{{ $i }}">★</button>
                    @endfor
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                <button class="submit-btn" type="submit">送信する</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    // --- メッセージ編集 ---
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            document.getElementById(`message-text-${id}`).style.display = 'none';
            document.getElementById(`edit-form-${id}`).style.display = 'block';

        });
    });

    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            document.getElementById(`edit-form-${id}`).style.display = 'none';
            document.getElementById(`message-text-${id}`).style.display = 'block';

        });
    });

    // --- チャット入力保持 ---
    const textarea = document.querySelector('.message-input');

    if (textarea) {
        const transactionId = '{{ $transaction->id }}';
        const storageKey = `chat_body_${transactionId}`;

        textarea.value = localStorage.getItem(storageKey) ?? '';
        textarea.addEventListener('input', () => {
            localStorage.setItem(storageKey, textarea.value);
        });

        textarea.form.addEventListener('submit', () => {
            localStorage.removeItem(storageKey);
        })
    }

    // --- モーダル処理 ---
    const modalBuyer = document.getElementById('ratingModalBuyer');
    const modalSeller = document.getElementById('ratingModalSeller');

    if (modalBuyer) {
        document.querySelectorAll('.complete-btn').forEach(btn => {
            btn.addEventListener('click', () => modalBuyer.style.display = 'flex');
        });
        modalBuyer.addEventListener('click', e => { if(e.target === modalBuyer) modalBuyer.style.display = 'none'; });
        modalBuyer.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', () => {
                const val = star.dataset.rating;
                modalBuyer.querySelectorAll('.star').forEach(s => s.classList.toggle('active', s.dataset.rating <= val));
                modalBuyer.querySelector('#rating-input-buyer').value = val;
            });
        });
    }

    if (modalSeller) {
        modalSeller.addEventListener('click', e => { if(e.target === modalSeller) modalSeller.style.display = 'none'; });
        modalSeller.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', () => {
                const val = star.dataset.rating;
                modalSeller.querySelectorAll('.star').forEach(s => s.classList.toggle('active', s.dataset.rating <= val));
                modalSeller.querySelector('#rating-input-seller').value = val;
            });
        });
    }

    // --- 自動リサイズ textarea ---
    document.addEventListener('input', e => {
        if (e.target.classList.contains('auto-resize')) {
            e.target.style.height = 'auto';
            e.target.style.height = e.target.scrollHeight + 'px';
        }
    });
</script>

@endsection