@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/listing.css') }}">
@endsection


@section('content')
<div class="container">
    <h1 class="page-title">商品の出品</h1>

    <form action="/sell" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-section">
            <label class="section-title no-border">商品画像</label>
            <div class="image-upload-area">
                <input type="file" name="item_path" id="product-image" accept="image/*" style="display: none;" onchange="previewImage(event)">
                <button type="button" class="btn-upload" onclick="document.getElementById('product-image').click()">
                    画像を選択する
                </button>
                <div id="image-preview">
                    <img id="preview" src="" alt="">
                </div>

                @error('item_path')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="form-section">
            <h2 class="section-title big-title">商品の詳細</h2>
            <div class="form-group">
                <label class="form-label">カテゴリー</label>
                <input type="hidden" name="category_id" id="category_ids">

                <div class="category-buttons">
                    @foreach($categories as $category)
                    <button type="button" class="btn-category" onclick="toggleCategory({{ $category->id }}, this)">
                        {{ $category->name }}
                    </button>
                    @endforeach
                </div>
                @error('category_id')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">商品の状態</label>
                <select name="condition_id" class="form-select">
                    <option value="">選択してください</option>
                    <option value="1">良好</option>
                    <option value="2">目立った傷や汚れなし</option>
                    <option value="3">やや傷や汚れあり</option>
                    <option value="4">状態が悪い</option>
                </select>
                @error('condition_id')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="form-section">
            <h2 class="section-title big-title">商品名と説明</h2>

            <div class="form-group">
                <label class="form-label">商品名</label>
                <input type="text" name="name" class="form-input" value="">
                @error('name')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">ブランド名</label>
                <input type="text" name="brand" class="form-input" value="">
            </div>

            <div class="form-group">
                <label class="form-label">商品の説明</label>
                <textarea name="description" class="form-textarea" rows="6"></textarea>
                @error('description')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">販売価格</label>
                <input type="text" name="price" class="form-input" placeholder="¥">
                @error('price')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <button type="submit" class="btn-submit">出品する</button>
    </form>
</div>


<script>
    let selectedCategories = [];

    function toggleCategory(id, btn) {
        const index = selectedCategories.indexOf(id);

        if (index > -1) {
            selectedCategories.splice(index, 1);
            btn.classList.remove('active');
        } else {
            selectedCategories.push(id);
            btn.classList.add('active');
        }
        document.getElementById('category_ids').value = selectedCategories.join(',');
    }
</script>

<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        const previewArea = document.getElementById('image-preview');
        const uploadButton = document.querySelector('.btn-upload');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                previewArea.classList.add('show');
                uploadButton.classList.add('below-image');
                uploadButton.textContent = '画像を変更する';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection