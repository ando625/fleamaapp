@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}" />
@endsection


@section('content')
<div class="content-wrapper">
    @if (session('success'))
    <div class="alert-success-wrapper">
        <div class="alert alert-success">✅ {{ session("success") }}</div>
    </div>
    @endif @if(session('error'))
    <div class="alert-error-wrapper">
        <div class="alert-error">❌{{ session("error") }}</div>
    </div>
    @endif

    <div class="full-width-line">
        <nav class="tab-navigation">
            <li>
                <a
                    href="{{ route('items.index', ['tab' => 'recommend']) }}"
                    class="tab-link {{ $tab === 'recommend' ? 'active' : '' }}"
                    >おすすめ</a
                >
            </li>
            <li>
                <a
                    href="{{ route('items.index', ['tab' => 'mylist']) }}"
                    class="tab-link {{ $tab === 'mylist' ? 'active' : '' }}"
                    >マイリスト</a
                >
            </li>
        </nav>
    </div>
    <ul class="products-grid">
        @foreach($items as $item)
        <li class="product-item">
            <a href="{{ route('items.show', $item->id) }}">
                <div class="product-image">
                    <img
                        src="{{ asset('storage/' . $item->item_path) }}"
                        alt="{{ $item->name }}"
                    />
                    @if ($item->status === 'sold')
                    <div class="sold-overlay">Sold</div>
                    @endif
                </div>
                <div class="product-name">{{ $item->name }}</div>
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endsection
