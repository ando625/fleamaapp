@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content-wrapper">
    <nav class="tab-navigation">
        <a href="#" class="tab-link">おすすめ</a>
        <a href="#" class="tab-link active">マイリスト</a>
    </nav>

    <div class="products-grid">
        <div class="product-item">
            <div class="product-image">
                <span class="placeholder-text">商品画像</span>
            </div>
            <div class="product-name">商品名</div>
        </div>
        
        <div class="product-item">
            <div class="product-image">
                <span class="placeholder-text">商品画像</span>
            </div>
            <div class="product-name">商品名</div>
        </div>
        
        <div class="product-item">
            <div class="product-image">
                <span class="placeholder-text">商品画像</span>
            </div>
            <div class="product-name">商品名</div>
        </div>
    </div>
</div>
@endsection