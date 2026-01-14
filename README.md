#アプリケーション名

勤怠管理アプリ

---

##概要

このアプリケーションはlaravelをしようした勤怠管理アプリです。
出勤・退勤・休憩時間を正確に登録できるシンプルなアプリで、さらに勤怠確定後、勤怠未入力の場合にも修正が可能です。
管理者からは日付別、スタッフ別で勤怠の確認・修正、修正申請の承認が出来るようになっており、勤怠情報のCSVダウンロードも可能となっています。

---

#環境構築

##Dockerビルド

    -git clone git@github.com:yoichi-hashimoto/COACHTECH_attendance.git
    -docker compose up -d --build

##laravel環境構築
    -docker-compose exec php bash

    -composer install

    -cp .env.example .env、環境変数を変更

    -php artisan key generate
    -php artisan migrate
    -php artisan db:seed

---

#開発環境

    -トップ画面 http://localhost/

    -ユーザー登録画面 http://localhost/register/

    -phpMyAdmin http://localhost:8080/

    -Mailhog http://localhost:8025/

---

#使用技術

    -PHP 8.4.12
    -laravel 8.83.29
    -Composer 2.8.12
    -MySQL 8.0.26
    -Docker 4.43.2
    -nginx 1.21.1
    -HTML/CSS
    -その他:Fortify

---

#主な機能

    【利用者向け】
        ユーザー登録（Fortify、メール認証対応）　/　ログイン
        勤怠登録機能（出勤、退勤、休憩：休憩は何度でも取れる）
        勤怠一覧表示
        勤怠修正機能（打刻済みの修正、未打刻時の修正申告）
        修正申請閲覧機能（承認待ち、承認済みの確認）

    【管理者向け】
        ユーザー登録（Fortify、メール認証対応））　/　ログイン
        日付別勤怠一覧表示
        スタッフ一覧表示およびスタッフ別勤怠一覧表示
        勤怠修正機能（打刻済みの修正、未打刻時の修正申告）
        勤怠修正閲覧機能（承認待ち、承認済みの確認）
        CSVダウンロード機能
        修正申請の承認機能

#ER図

![ER図](docs/ER.svg)