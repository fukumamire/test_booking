# 飲食店予約アプリ
<img width="1437" alt="shop_all" src="https://github.com/fukumamire/todo/assets/136237535/54a24a32-84a0-4c31-a2f0-69c4943134ae">



## 作成した目的
自社で予約サービスを持ちたいという依頼があったため

## アプリケーションURL

開発環境：http://localhost/

phpMyAdmin:：http://localhost:8080/


## 機能一覧

会員登録、ログイン、ログアウト、ユーザー情報取得、ユーザー飲食店お気に入り一覧取得、ユーザー飲食店予約情報取得、飲食店一覧取得、飲食店詳細取得、飲食店お気に入り追加、飲食店お気に入り削除、飲食店予約情報追加、飲食店予約情報削除、エリアで検索する、ジャンルで検索する、店名で検索する

## 使用技術（実行環境）
Laravel 8.x、PHP 7.4.9、docker、laravel-fortify、javascript

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

| カラム名   | 型               | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
|------------|------------------|-------------|------------|----------|-------------|
| id         | unsigned bigint  | 〇          | 〇         | 〇       |             |
| name       | VARCHAR          |             |            | 〇       |             |
| outline    | TEXT             |             |            |          |             |
| created_at | timestamp        |             |            |          |             |
| updated_at | timestamp        |             |            |          |             |

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
| created_at       | timestamp  |             |            |          |             |
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


# 環境構築
## Dockerビルド

1. git@github.com:fukumamire/Booking-restaurant.git
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

・テスト　次郎

・テスト　三郎

・テスト　花子

パスワードは「１」を８つ入力してください
