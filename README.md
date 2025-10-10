# flema


# 環境構築手順（Docker + Laravel）

このリポジトリは、Docker と Laravel 環境で動作するアプリケーションです。  
以下の手順でセットアップしてください。

---

## 1. リポジトリのクローン

```bash
git clone git@github.com:ando625/fleamaapp.git
cd fleamaapp
```


---

## 2. Docker コンテナの起動

Docker Desktop を起動して、以下を実行します：

```docker compose up -d --build```



## 3. Laravel 環境構築

### 3-1. PHP コンテナに入る

```docker compose exec php bash```

### 3-2. Composer で依存関係をインストール

```composer install```

### 3-3. .env ファイル作成

```cp .env.example .env```

.env に以下の環境変数を設定します：

```DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

### 3-4. アプリケーションキー生成

```php artisan key:generate```

### 3-5. データベース準備

- マイグレーション実行
```php artisan migrate```

- シーディング実行（初期データ投入）
```php artisan db:seed```

### 3-6. ストレージのリンク作成

画像をブラウザで表示するために、storage ディレクトリをリンクします：

```php artisan storage:link```


## 4.PHPUnitテスト

	GD 拡張のインストール

    apt-get update
apt-get install -y libpng-dev
docker-php-ext-install gd

.	PHP-FPM の再起動
exit  # コンテナから出る
docker compose restart php

再度 GD が有効になっているか確認
docker compose exec php bash
php -m | grep gd


### 4-1. PHPコンテナに入る

```docker compose exec php bash```

### 4-2. テストを実行
```php artisan test```

---

## 5. 注意事項

- Docker の MySQL データは Git には含めないよう .gitignore に設定済みです。  
- Mac M1/M2 ARM 環境では、MySQL と phpMyAdmin に `platform: linux/amd64` が指定済みです。Windows x86 環境でもそのまま動作します。
- PHP 8.1 以降の環境では、`php.ini` 内の  
```mbstring.internal_encoding``` 設定は廃止されているため警告が出る場合は  
コメントアウトしてください。


## 利用環境(実行環境)

- PHP: 8.3.0  
- Laravel: 8.83.27  
- MySQL: 8.0.26  

---





URL
開発環境：http://localhost/products

phpMyAdmin:：http://localhost:8080/
