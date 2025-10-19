@extends('layouts.app')

@section('main-class', 'purchase-page')

@section('css')
<link rel="stylesheet" href="{{ asset('css/buy.css') }}">
@endsection

@section('content')
<form action="{{ route('purchase.checkout', $item->id) }}" method="post">
    @csrf
    <div class="purchase-container">
        <div class="purchase-content">
            <!-- 商品情報セクション -->
            <div class="product-section">
                <div class="product-image">
                    <div class="image-placeholder">
                        <img src="{{ asset('storage/'.$item->item_path) }}" alt="{{ $item->name }}">
                    </div>
                </div>
                <div class="product-info">
                    <h2 class="product-name">{{ $item->name }}</h2>
                    <div class="product-price">¥{{ number_format($item->price) }}</div>
                </div>
            </div>

            <hr class="divider">

            <!-- 支払い方法セクション -->
            <div class="payment-section">
                <h3 class="section-title">支払い方法</h3>
                <div class="payment-select">
                    <select class="payment-dropdown" name="payment_method" id="payment_method">
                        <option value="">選択してください</option>
                        <option value="konbini">コンビニ払い</option>
                        <option value="card">カード支払</option>
                    </select>
                    @error('payment_method')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <hr class="divider">

            <!-- 配送先セクション -->
            <div class="delivery-section">
                <div class="delivery-header">
                    <h3 class="section-title">配送先</h3>
                    <a href="{{ route('purchase.change', $item->id) }}" class="change-button">変更する</a>
                </div>
                <div class="delivery-address">
                    <p class="postal-code">〒 {{ $addressData['postal_code'] ?? $profile->postal_code }}</p>
                    <p class="address">{{ $addressData['address'] ?? '' }}{{ $addressData['building'] ?? '' }}</p>
                </div>
            </div>
        </div>

        <!-- サイドバー -->
        <div class="purchase-sidebar">
            <div class="price-summary">
                <div class="price-row">
                    <span class="price-label">商品代金</span>
                    <span class="price-value">¥{{ number_format($item->price) }}</span>
                </div>
            </div>

            <div class="payment-method-summary">
                <div class="payment-row">
                    <span class="payment-label">支払い方法</span>
                    <span class="payment-value" id="payment_value">-</span>
                </div>
            </div>

            <button class="purchase-button" type="submit">購入する</button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('payment_method');
        const display = document.getElementById('payment_value');

        select.addEventListener('change', function() {
            const selectedText = select.options[select.selectedIndex].text;
            display.textContent = select.value === '' ? '-' : selectedText;
        });
    });
</script>
@endsection