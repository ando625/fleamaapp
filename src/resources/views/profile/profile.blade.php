@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="content-wrapper">
    <div class="profile-section">
        <div class="profile-avatar">
            <img src="{{ $user->profile?->profile_image ? asset('storage/' . $user->profile->profile_image) : asset('images/default-avatar.png') }}">
        </div>
        <div class="profile-info">
            <h2 class="username">{{ $user->name ?? 'ユーザー名' }}</h2>
            @if(!is_null($user->average_rating))
            <div class="rating-display">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $user->average_rating)
                        <span class="star-filled">★</span>
                    @else
                        <span class="star-empty">★</span>
                    @endif
                @endfor
            </div>
            @endif
        </div>
        <div class="profile-actions">
            <a href="{{ route('profile.edit') }}">
                <button class="edit-profile-btn">プロフィールを編集</button>
            </a>
        </div>
    </div>
    <nav class="tab-navigation">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="tab-link {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="tab-link {{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
        <a href="{{ route('mypage', ['page' => 'transaction']) }}" class="tab-link {{ $tab === 'transaction' ? 'active' : '' }}">取引中の商品
            @if($transactionCount > 0)
            <span>{{$transactionCount}}</span>
            @endif
        </a>
    </nav>
    <div class="products-grid">

        @foreach ($itemsToShow as $row)

        @if ($tab === 'transaction')
            @php
                $item = $row->item;
            @endphp
        <div class="product-item">
            <a href="{{ route('profile.transactions.show', $row->id) }}">
                <div class="product-image">

                    @if ($row->unread_count > 0)
                        <span class="message-badge">
                            {{ $row->unread_count }}
                        </span>
                    @endif


                    @if($item->item_path)
                    <img src="{{ asset('storage/'. $item->item_path) }}" alt="{{ $item->name }}">
                    @else
                    <span class="placeholder-text">商品画像</span>
                    @endif
                </div>

                <div class="product-name">
                    {{ $item->name ?? '商品名' }}
                </div>
            </a>
        </div>

        <!-- 出品・購入タブ -->
        @else
            <div class="product-item">
                <a href="{{ route('items.show', $row->id) }}">
                    <div class="product-image">

                        {{-- 商品画像 --}}
                        @if($row->item_path)
                            <img src="{{ asset('storage/' . $row->item_path) }}">
                        @else
                            <span class="placeholder-text">商品画像</span>
                        @endif

                        {{-- Sold 表示（取引中では出さない） --}}
                        @if ($row->status === 'sold')
                            <div class="sold-overlay">Sold</div>
                        @endif
                    </div>

                    <div class="product-name">
                        {{ $row->name }}
                    </div>
                </a>
            </div>
        @endif
        @endforeach
    </div>
</div>
@endsection