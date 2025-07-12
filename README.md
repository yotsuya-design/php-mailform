# PHP Mailform

シンプルで軽量なPHPベースのメールフォームスクリプトです。フォームからのメール送信とスパムチェック機能を提供します。JSON APIとして設計されており、フロントエンドから簡単に呼び出すことができます。

## メール送信／スパムチェックスクリプト

- `api/sendmail.php`: フォームからメールを送信するスクリプトです。
- `api/spamcheck.php`: テキストにスパムワードか含まれるかをチェックするスクリプトです。

## `sendmail.php` の使い方

このスクリプトは、フォームから受け取ったデータをメールとして送信します。
JSON形式のPOSTリクエストで呼び出されるように設計されています。

### 設定

`api/sendmail.php` の先頭にある以下の変数を編集してください。

- `$TO_EMAIL`：受信先メールアドレス  
- `$FROM_NAME`：送信者として表示される名前  
- `$ORG_NAME`：組織名（任意）

### リクエスト形式

`api/sendmail.php` に対して、以下のフィールドを含む JSON ボディの POST リクエストを送信します。

| フィールド名 | 型     | 必須 | 説明         |
|--------------|--------|------|--------------|
| `title`      | string | ✔︎   | メールの件名 |
| `email`      | string | ✔︎   | 送信者のメールアドレス |
| `body`       | string | ✔︎   | メール本文   |

### リクエストの例

```bash
curl -X POST -H "Content-Type: application/json" -d '{
  "title": "サービスに関するお問い合わせ",
  "email": "customer@example.com",
  "body": "サービスの詳しい内容について知りたいです。"
}' https://your-domain.com/api/sendmail.php
```

### レスポンスの例

- **200 OK**: 送信成功
  ```json
  {
    "status": "success",
    "message": "Mail sent successfully"
  }
  ```
- **400 Bad Request**: リクエストの必須項目が不足
  ```json
  {
    "status": "error",
    "message": "Missing required information."
  }
  ```
- **405 Method Not Allowed**: POST以外のメソッドでアクセス
- **500 Internal Server Error**: メール送信処理に失敗

## `spamcheck.php` の使い方

このスクリプトは、与えられたテキスト内にスパムワード、URL、メールアドレスが含まれていないかを判定します。

### 設定

スパムと判定する単語一覧は、api/spam_words.json にてカスタマイズ可能です（JSON 配列形式）。

### リクエスト形式
| フィールド名 | 型     | 必須 | 説明                        |
|--------------|--------|------|-----------------------------|
| `text`       | string | ✔︎   | チェック対象のテキスト      |

### リクエストの例

```bash
curl -X POST -H "Content-Type: application/json" -d '{
  "text": "This is a test message with a URL https://example.com"
}' https://your-domain.com/api/spamcheck.php
```

### レスポンスの例

- **200 OK**: チェック成功
  ```json
  {
    "status": "success",
    "isSpam": true,
    "reason": "URL"
  }
  ```
  - isSpam: スパムと判定されたか（true/false）
  - reason: 判定理由（例: “URL”, “Email”, “Word”）

- **405 Method Not Allowed**: POST以外のメソッドでアクセス

### 動作確認用のサンプル

sendmail.php と spamcheck.php を組み合わせた確認用のサンプルファイルとして index.html を同梱しています。
PHP が動作するサーバーにアップロードし、ブラウザで index.html を開いてご確認ください。

## ライセンス

このプロジェクトは MITライセンス の下で公開されています。
商用利用、改変、再配布を含め、自由にご利用いただけます。