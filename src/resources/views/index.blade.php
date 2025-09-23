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
        @foreach($items as $item)
        <div class="product-item">
            <div class="product-image">
                <img src="{{ asset($item->item_path) }}" alt="{{ $item->name }}">
            </div>
            <div class="product-name">{{ $item->name }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection