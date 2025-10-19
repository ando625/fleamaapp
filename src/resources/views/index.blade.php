@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content-wrapper">
    @if (session('success'))
    <div class="alert-success-wrapper">
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="alert-error-wrapper">
        <div class="alert-error">
            ❌{{ session('error') }}
        </div>
    </div>
    @endif


    <div class="full-width-line">
        <nav class="tab-navigation">
            <a href="{{ route('items.index', ['tab' => 'recommend']) }}" class="tab-link {{ $tab === 'recommend' ? 'active' : '' }}">おすすめ</a>
            <a href="{{ route('items.index', ['tab' => 'mylist']) }}" class="tab-link {{ $tab === 'mylist' ? 'active' : '' }}">マイリスト</a>
        </nav>
    </div>

    <div class="products-grid">
        @foreach($items as $item)
        <div class="product-item">
            <a href="{{ route('items.show', $item->id) }}">
                <div class="product-image">
                    <img src="{{ asset('storage/' . $item->item_path) }}" alt="{{ $item->name }}">
                    @if ($item->status === 'sold')
                    <div class="sold-overlay">Sold</div>
                    @endif
                </div>
                <div class="product-name">{{ $item->name }}</div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection