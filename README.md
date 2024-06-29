# 飲食店予約アプリ
<img width="1437" alt="shop_all" src="https://github.com/fukumamire/todo/assets/136237535/54a24a32-84a0-4c31-a2f0-69c4943134ae">

## 作成した目的
自社で予約サービスを持ちたい。

## アプリケーションURL

開発環境：http://localhost/

phpMyAdmin:：http://localhost:8080/


## 機能一覧

ログイン機能、お気に入り追加/削除、検索

###　店舗一覧について
・店舗画像URLを複数DBに保存できるようになっています。


## 使用技術（実行環境）
Laravel 8.x、PHP 7.4.9、docker、laravel-fortify、javascript

## テーブル設計とER図

# 環境構築
## Dockerビルド

1. git@github.com:fukumamire/Booking-restaurant.gitconfirmation-test-contact-form.git
2. DockerDesktopアプリを立ち上げる
3. docker-compose up -d --build

## Laravel環境構築
1. ```docker-compose exec php bash```
2. ```composer install```
3. 必要に応じて.envファイルを作成し作成した際は以下の環境変数を追加
``` DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass 
```
4. アプリケーションキーの作成
``` php artisan key:generate```

5. マイグレーションの実行
``` php artisan migrate ```

