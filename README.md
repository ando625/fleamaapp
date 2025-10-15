# Flema アプリケーション環境構築・操作手順

このリポジトリは **Docker + Laravel 8 + MySQL + Stripe + Fortify** 環境で動作する Web アプリです。  
ここでは **環境構築・テスト・メール認証・Stripe 決済(カード支払い）仕組み** を含めて順を追って説明します。
このアプリでは **新規登録後のメール認証** を MailHog を使って確認できます。  
認証が完了するとプロフィール登録画面に遷移します。

---

## 1. リポジトリのクローン

```bash
git clone git@github.com:ando625/fleamaapp.git
cd fleamaapp
```

---

## 2. Docker 環境起動

Docker Desktop を起動後、以下を実行：

```bash
docker compose up -d --build
```

コンテナの確認：

```bash
docker compose ps
```

`docker-compose.yml` にすでに MailHog が定義されています。  
そのため、以下のコマンドで MailHog も自動で立ち上がります。

php         Up
mysql       Up
phpmyadmin  Up
mailhog     Up



---

## 3. Laravel 環境構築

### 3-1. PHP コンテナに入る

```bash
docker compose exec php bash
```

### 3-2. Composer で依存関係をインストール

```bash
composer install
```

- Fortify もこのときに自動でインストールされます。ユーザーが手動で入れる必要はありません。



### 3-3. `.env` ファイル作成

```bash
cp .env.example .env
```

.env に以下を設定：

```

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```



## メール認証機能（MailHog使用）

- 本アプリでは 新規会員登録時および初回ログイン時にメール認証 を行います。
- メール送信のテストは MailHog を使用します。




### 確認 ブラウザで MailHog の管理画面にアクセス

- http://localhost:8025

1. SMTPサーバー: localhost:1025
2. Web UI: http://localhost:8025→ 送信された認証メールを確認可能


### Laravel 側の設定（.env）

```MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="example@furiapp.test"
MAIL_FROM_NAME="${APP_NAME}"
```

- 新規登録後、誘導画面の「認証はこちらから」ボタンを押すと押すと MailHog が開き、届いたメール内のリンクをクリックして認証を完了させる、そしてプロフィール画面へ遷移します。
- 新規登録後メール認証をせずにログインした場合、メール認証画面に飛びます、そしてプロフィール登録していただきます。

### メール認証の流れ

1.	新規ユーザーを登録（メールアドレス・パスワードを入力）
2.	登録完了後、メール認証誘導画面に遷移
3.	誘導画面で「認証はこちらから」ボタンをクリックすると、MailHog が開きます。
4.	MailHog で届いた認証メールの中の「メールアドレスはこちら」or 「Verify Email Address」リンク（ボタン）をクリックして初めてメール認証が完了し、プロフィール設定画面に遷移します。
5. 認証完了 → プロフィール設定画面に遷移



### 3-4. アプリケーションキー生成

```bash
php artisan key:generate
```

### 3-5. データベース準備（データベースの初期化・開発用）

開発環境で事前にダミーデータを入れるので以下を実行してください：

```bash
php artisan migrate:fresh --seed
```

### 3-6. ストレージリンク作成

```bash
php artisan storage:link
```

---

## 4. PHPUnit テスト

### 4-1. GD 拡張をインストール

```bash
apt-get update
apt-get install -y libpng-dev
docker-php-ext-install gd
```

### 4-2. PHP-FPM の再起動

```bash
exit
docker compose restart php
```

### 4-3. テスト実行

```bash
docker compose exec php bash
php artisan test
```

---

## 5. Stripe のセットアップ

PHPコマンドの中に入り実行:
```bash
composer require stripe/stripe-php
```

.env に Stripe の公開キー・秘密キーを設定

```
STRIPE_KEY=pk_test_********
STRIPE_SECRET=sk_test_********
```
>  Stripe キーは Stripe にログインしダッシュボードで取得します。





##  Stripe 設定ファイルについて

・StripeのAPIキーは .env ファイルに保存したあと、config/services.php から呼び出すように設定しています。
・以下の設定が存在することを確認してください。


// config/services.php
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
],

この設定により、コントローラ内で以下のようにStripeを利用できます。

```php
\Stripe\Stripe::setApiKey(config('services.stripe.secret'));
```

- カード払いは Stripe Checkout を利用
- コンビニ払いは Stripe を通さず DB に即保存し、トップページにリダイレクト

---

## 6. 購入処理の流れ

### カード払いの場合

1. 商品購入画面で「カード支払い」を選択
2. `PurchaseController@checkout` が Stripe Checkout セッションを作成
3. Stripe 画面にリダイレクト
4. 支払い成功後、`PurchaseController@complete` で DB 保存・商品ステータス更新

### コンビニ払いの場合

1. 商品購入画面で「コンビニ払い」を選択
2. `PurchaseController@checkout` で即 DB 保存・商品ステータス更新
3. トップページにリダイレクト
4. Stripe 画面はスキップされる (Stripe実行はなし)

---


---

## 8. phpMyAdmin

- URL: `http://localhost:8080/`
- ユーザー名・パスワードは `.env` と同じ
- DB: `laravel_db` を確認可能

---

## 9. 注意事項

- MySQL データは `.gitignore` により Git には含めない
- Mac M1/M2 ARM 環境では MySQL と phpMyAdmin に `platform: linux/amd64` が指定済み
- PHP 8.1 以降、`mbstring.internal_encoding` は廃止されているため、警告が出たらコメントアウト

---

## 10. 開発環境 URL

- 開発環境: [http://localhost/]
- phpMyAdmin: [http://localhost:8080/]

---
