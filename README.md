# 飲食店予約アプリ
<img width="1437" alt="shop_all" src="https://github.com/fukumamire/todo/assets/136237535/54a24a32-84a0-4c31-a2f0-69c4943134ae">



## 作成した目的
※２０２５年１月３日時点　fukumamire/Booking-restaurantをクローンして作成したもの。1. 口コミ機能　2. 店舗一覧ソート機能が動くか確認するため作成したものです



## アプリケーションURL

開発環境：http://localhost/

phpMyAdmin:：http://localhost:8080/


## 機能一覧

会員登録、ログイン、ログアウト、ユーザー情報取得、ユーザー飲食店お気に入り一覧取得、ユーザー飲食店予約情報取得、飲食店一覧取得、飲食店詳細取得、飲食店お気に入り追加、飲食店お気に入り削除、飲食店予約情報追加、飲食店予約情報削除、予約変更、評価機能、エリアで検索する、ジャンルで検索する、店名で検索する

## 使用技術（実行環境）
Laravel 8.x、PHP 7.4.9、docker、laravel-fortify、javascript、Laravel-permission

## ER図

![ER図予約変更テーブル追加](https://github.com/user-attachments/assets/6db7800f-468e-41a6-82c6-4fd8e9decd97)

## テーブル設計

### users テーブル

| カラム名   | 型             | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
|------------|----------------|-------------|------------|----------|-------------|
| id         | bigint         | 〇          | 〇         | 〇       |             |
| name       | VARCHAR        |             |            | 〇       |             |
| email      | VARCHAR        |             | 〇         | 〇       |             |
| password   | VARCHAR        |             |            | 〇       |             |
| created_at | timestamp      |             |            | 〇       |             |
| updated_at | timestamp      |             |            | 〇       |             |

### shops テーブル

<span style="color:red">`avg_rating` カラムを追加</span>

| カラム名     | 型               | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
|--------------|------------------|-------------|------------|----------|-------------|
| id           | unsigned bigint   | 〇          | 〇         | 〇       |             |
| user_id      | unsigned bigint   |             |            |          |             |
| name         | VARCHAR           |             |            | 〇       |             |
| outline      | TEXT              |             |            |          |             |
| created_at   | timestamp         |             |            |          |             |
| updated_at   | timestamp         |             |            |          |             |
|<span style="color:red"> avg_rating </span>  | float             |             |            |          |             |


### shop_areas テーブル (中間テーブル)

| カラム名   | 型               | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
|------------|------------------|-------------|------------|----------|-------------|
| id         | unsigned bigint  | 〇          | 〇         | 〇       |             |
| shop_id    | unsigned bigint  |             | 〇         | 〇       | 〇           |
| area_id    | unsigned bigint  |             | 〇         | 〇       | 〇           |
| created_at | timestamp        |             |            |          |             |
| updated_at | timestamp        |             |            |          |             |

### areas テーブル

| カラム名   | 型               | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
|------------|------------------|-------------|------------|----------|-------------|
| id         | unsigned bigint  | 〇          | 〇         | 〇       |             |
| name       | VARCHAR          |             |            | 〇       |             |
| created_at | timestamp        |             |            |          |             |
| updated_at | timestamp        |             |            |          |             |

### genres テーブル

| カラム名   | 型               | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
|------------|------------------|-------------|------------|----------|-------------|
| id         | unsigned bigint  | 〇          |            | 〇       |             |
| shop_id    | INT              |             | 〇         | 〇       | 〇           |
| name       | VARCHAR          |             |            | 〇       |             |
| created_at | timestamp        |             |            |          |             |
| updated_at | timestamp        |             |            |          |             |

### shop_images テーブル

| カラム名       | 型               | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
|----------------|------------------|-------------|------------|----------|-------------|
| id             | unsigned bigint  | 〇          | 〇         | 〇       |             |
| shop_id        | INT (bigint)     |             | 〇         | 〇       | 〇           |
| shop_image_url | VARCHAR(255)     |             |            | 〇       |             |
| created_at     | timestamp        |             |            | 〇       |             |
| updated_at     | timestamp        |             |            | 〇       |             |


### bookings テーブル

| カラム名         | 型         | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
|------------------|------------|-------------|------------|----------|-------------|
| id               | INT(bigint)| 〇          |            | 〇       |             |
| user_id          | INT(bigint)|             |            | 〇       |             |
| shop_id          | INT(bigint)|             |            | 〇       |             |
| date             | DATE       |             |            | 〇       |             |
| time             | TIME       |             |            | 〇       |             |
| number_of_people | INT        |             |            | 〇       |             |
| status           | VARCHAR    |             |            | 〇       |             |
| qr_code_token    | VARCHAR    |             |            | 〇       |             |
| created_at       | timestamp  |             |   〇       |          |             |
| updated_at       | timestamp  |             |            |          |             |

### favorites テーブル

| カラム名   | 型         | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
|------------|------------|-------------|------------|----------|-------------|
| id         | INT(bigint)| 〇          | 〇         |          |             |
| user_id    | INT(bigint)|             |            | 〇       |             |
| shop_id    | INT(bigint)|             |            | 〇       |             |
| created_at | timestamp  |             |            |          |             |
| updated_at | timestamp  |             |            |          |             |

## bookings_changes テーブル

このテーブルは、予約の変更履歴を追跡するために使用されます。

| カラム名                | 型          | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY                    |
|-------------------------|-------------|-------------|------------|----------|--------------------------------|
| id                    | BIGINT      | 〇           |            | 〇        |                                |
| booking_id            | BIGINT      |             |            | 〇        | 外部キー booking_id           |
| user_id               | BIGINT      |             |            | 〇        | 外部キー user_id              |
| old_booking_date      | DATE        |             |            | 〇        |                                |
| old_booking_time      | TIME        |             |            | 〇        |                                |
| old_number_of_people  | INTEGER     |             |            | 〇        |                                |
| new_booking_date      | DATE        |             |            | 〇        |                                |
| new_booking_time      | TIME        |             |            | 〇        |                                |
| new_number_of_people  | INTEGER     |             |            | 〇        |                                |
| changed_at            | TIMESTAMP   |             |            |          |                                |
| created_at            | TIMESTAMP   |             |            | 〇        |                                |
| updated_at            | TIMESTAMP   |             |            | 〇        |                                |
### 説明
- **id**: プライマリキーとして使用される自動増分のID。
- **booking_id**: 予約を参照する外部キー。
- **user_id**: ユーザーを参照する外部キー。
- **old_booking_date**: 予約変更前の日付。
- **old_booking_time**: 予約変更前の時間。
- **old_number_of_people**: 予約変更前の人数。
- **new_booking_date**: 予約変更後の日付。
- **new_booking_time**: 予約変更後の時間。
- **new_number_of_people**: 予約変更後の人数。
- **changed_at**: 変更が行われた日時（`nullable`）。
- **created_at**: レコードの作成日時。Laravelによって自動的に管理
- **updated_at**: レコードの最終更新日時。Laravelによって自動的に管理されます

## reviewsテーブル
| カラム名    | 型           | PRIMARY KEY | UNIQUE KEY | NOT NULL（NUllを許可しない） | FOREIGN KEY |
|-------------|--------------|-------------|------------|----------|-------------|
| id          | bigint       | 〇           |            | 〇        　　　　　　　|             |
| shop_id     | bigint       |             |            | 〇        　　　　　　　　| 〇           |
| user_id     | bigint       |             |            | 〇        　　　　　　　　| 〇           |
| rating      | int      　　|             |            | 〇        　　　　　　　　|             |
| image_url   | VARCHAR(255) |　            |           |          　　　　　　　　　|             |
| comment     | text         |             |            | 〇        |             |
| created_at  | timestamp    |             |            |          |             |
| updated_at  | timestamp    |             |            |          |             |
### 説明

このテーブルは、ユーザーが飲食店に対して投稿するレビューを保存するためのものです。各カラムの詳細は以下の通りです。

- `id`: レビューの一意の識別子 (PRIMARY KEY)。
- `shop_id`: レビュー対象の店舗のID (FOREIGN KEY)。
- `user_id`: レビューを書いたユーザーのID (FOREIGN KEY)。
- `rating`: 店舗の評価（1から5の星の数）。
- `image_url`: 画像　あってもなくてもOK
- `comment`: レビューの本文。
- `created_at`/`updated_at`: レビューが作成・更新された日時。

# 環境構築
## Dockerビルド

1. git@github.com:fukumamire/test-booking.git
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

6. シーディングの実行
``` php artisan db:seed ```


## アカウントの種類（テストユーザー）
・テスト　太郎
test@example.com

・テスト　次郎
jiro@example.com

・テスト　三郎
saburo@example.com

・テスト　花子
hanako@example.com

・test 管理者　（管理者ユーザー）
kanrisya@example.com

・光　（店舗ユーザー（店舗代表者）） ユーザー　id:22
shop-hikari@example.com

・店長　（店舗ユーザー（店舗代表者））ユーザー　id:55
shop-daihyousya@example.com


パスワードは「１」を８つ入力してください


## CSV インポート形式

以下の形式で CSV ファイルを作成してください。

| 店舗名 |ユーザーID| 地域 | ジャンル | 店舗概要 | 画像URL |
|--------|------|------|----------|----------|---------|
| 魚屋 |22| 東京都 | 寿司 | 新鮮な魚を提供します。 | http://example.com/image.jpg |
- 項目名「ユーザーID」のIDと「画像URL」のURLは必ず半角でお願いします。
- 項目は全て入力必須
- 店舗名: 50文字以内
- ユーザーID:半角数字
- 地域: 「東京都」「大阪府」「福岡県」のいずれか
- ジャンル: 「寿司」「焼肉」「イタリアン」「居酒屋」「ラーメン」のいずれか
- 店舗概要: 400文字以内
- 画像URL: jpeg、png形式の画像URL

