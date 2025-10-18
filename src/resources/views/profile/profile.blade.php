@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="content-wrapper">
    <!-- ユーザープロフィール部分 -->
    <div class="profile-section">
        <div class="profile-avatar">
            <img src="{{ $user->profile?->profile_image ? asset('storage/' . $user->profile->profile_image) : asset('images/default-avatar.png') }}">
        </div>
        <div class="profile-info">
            <h2 class="username">{{ $user->name ?? 'ユーザー名' }}</h2>
        </div>
        <div class="profile-actions">
            <a href="{{ route('profile.edit') }}">
                <button class="edit-profile-btn">プロフィールを編集</button>
            </a>
        </div>
    </div>

    <!-- タブナビゲーション -->
    <nav class="tab-navigation">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="tab-link {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="tab-link {{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
    </nav>

    <!-- 商品グリッド -->
    <div class="products-grid">
        @php
        $itemsToShow = match($tab) {
        'sell' => $listings,
        'buy' => $purchases,
        };
        @endphp

        @foreach ($itemsToShow as $item)
        <div class="product-item">
            <a href="{{ route('items.show', $item->id) }}">
                <div class="product-image">
                    @if($item->item_path)
                    <img src="{{ asset('storage/'. $item->item_path) }}" alt="{{ $item->name }}">
                    @else
                    <span class="placeholder-text">商品画像</span>
                    @endif

                    @if ($item->status === 'sold')
                    <div class="sold-overlay">Sold</div>
                    @endif
                </div>
                <div class="product-name">{{ $item->name ?? '商品名' }}</div>
            </a>
        </div>
        @endforeach

    </div>
</div>
@endsection