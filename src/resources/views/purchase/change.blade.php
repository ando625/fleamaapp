@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/change.css') }}">
@endsection

@section('content')
<div class="address-change-container">
    <div class="address-change-form">
        <h1 class="page-title">住所の変更</h1>
        <form class="address-form" action="{{route('purchase.updateAddress', $item->id) }}" method="post">
            @csrf

            <div class="form-group">
                <label for="postal_code" class="form-label">郵便番号</label>
                <input type="text"
                    id="postal_code"
                    name="postal_code"
                    class="form-input"
                    value="{{ old('postal_code', $addressData['postal_code']) }}"
                    placeholder="">
                @error('postal_code')
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address" class="form-label">住所</label>
                <input type="text"
                    id="address"
                    name="address"
                    class="form-input"
                    value="{{ old('address', $addressData['address']) }}"
                    placeholder="">
                @error('address')
                <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="building" class="form-label">建物名</label>
                <input type="text"
                    id="building"
                    name="building"
                    class="form-input"
                    value="{{ old('building', $addressData['building'] ?? '') }}"
                    placeholder="">
            </div>

            <button type="submit" class="update-button">更新する</button>
        </form>
    </div>
</div>
@endsection