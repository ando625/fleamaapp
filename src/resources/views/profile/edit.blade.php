@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
<div class="profile-settings-container">
    <div class="profile-settings-wrapper">
        <h1 class="profile-title">プロフィール設定</h1>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')
            <div class="profile-image-section">
                <div class="profile-image-wrapper">
                    <div class="profile-image-circle">
                        <img src="{{ $profile->profile_image
                                    ? asset('storage/'.$profile->profile_image)
                                    : asset('images/default-avatar.png') }}"
                            alt="プロフィール画像" id="profile-preview">
                        <div class="profile-image-text">
                            プロフィール<br>画像
                        </div>
                    </div>
                </div>
                <div class="image-upload-btn">
                    <label for="profile_image" class="upload-label">画像を選択する</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;">
                </div>
            </div>

            <div class="form-group">
                <label for="name" class="form-label">ユーザー名</label>
                <input type="text" id="name" name="name" class="form-input"
                        value="{{ old('name', $user->name) }}">
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="postal_code" class="form-label">郵便番号</label>
                <input type="text" id="postal_code" name="postal_code" class="form-input"
                        value="{{ old('postal_code', $profile->postal_code) }}">
                @error('postal_code')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address" class="form-label">住所</label>
                <input type="text" id="address" name="address" class="form-input"
                        value="{{ old('address', $profile->address) }}">
                @error('address')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="building" class="form-label">建物名</label>
                <input type="text" id="building" name="building" class="form-input"
                        value="{{ old('building', $profile->building) }}">
                @error('building')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="update-btn">更新する</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const profileText = document.querySelector('.profile-image-text');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-preview').src = e.target.result;
            document.getElementById('profile-preview').style.display = 'block';
            profileText.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const profileImg = document.getElementById('profile-preview');
    const profileText = document.querySelector('.profile-image-text');

    if (profileImg.src && !profileImg.src.includes('default-avatar.png')) {
        profileImg.style.display = 'block';
        profileText.style.display = 'none';
    } else {
        profileImg.style.display = 'none';
        profileText.style.display = 'flex';
    }
});
</script>
@endsection