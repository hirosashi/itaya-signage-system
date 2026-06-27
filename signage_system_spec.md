# ホテルニューイタヤ デジタルサイネージシステム仕様書

作成日: 2026-06-27

## 1. 目的

ホテル館内で利用するデジタルサイネージ表示を管理するためのシステムです。

主な用途は以下です。

- 会場案内の表示
- 広告①の表示
- 広告②の表示
- 管理画面からの表示内容更新
- PDF、画像、CSVデータの登録

## 2. 対象URL

### 管理画面

| 用途 | URL |
| --- | --- |
| 会場管理トップ | `https://jyunbi.sakura.ne.jp/newitaya.com/` |
| 広告①管理 | `https://jyunbi.sakura.ne.jp/newitaya.com/admin-ad1.html` |
| 広告②管理 | `https://jyunbi.sakura.ne.jp/newitaya.com/admin-ad2.html` |

### サイネージ表示画面

| 用途 | URL |
| --- | --- |
| 会場表示 | `https://jyunbi.sakura.ne.jp/newitaya.com/venue.html` |
| 広告①表示 | `https://jyunbi.sakura.ne.jp/newitaya.com/ad1.html` |
| 広告②表示 | `https://jyunbi.sakura.ne.jp/newitaya.com/ad2.html` |

ラズベリーパイなどのサイネージ端末では、管理画面ではなく表示画面URLを使用します。

## 3. 画面構成

### 管理画面

管理画面は以下の3ページ構成です。

- 会場
- 広告①
- 広告②

グローバルナビゲーションは以下の順序です。

1. 会場
2. 広告①
3. 広告②
4. 操作マニュアル

操作マニュアルは右寄せのボタンとして表示し、PDFを別タブで開きます。

### 表示画面

表示画面はサイネージ端末専用です。

- `venue.html`: 会場案内
- `ad1.html`: 広告①
- `ad2.html`: 広告②

表示画面にはBasic認証を設定していません。

## 4. 認証仕様

### Basic認証

管理系ページと管理系APIにBasic認証を設定します。

対象:

- `index.html`
- `admin-ad1.html`
- `admin-ad2.html`
- `admin-venue.html`
- `login.php`
- `upload.php`

Basic認証ユーザー名:

```text
itaya-admin
```

パスワードは仕様書には記載しません。

### 管理ログイン

Basic認証通過後、管理画面内で `Signage CMS Login` を表示します。

管理ログインは `login.php` でサーバー側判定します。

仕様:

- パスワードは `auth.php` 内のハッシュで照合
- 平文パスワードはJavaScript内に保持しない
- ログイン成功時に署名付きトークンを発行
- 保存・アップロード時はトークンを確認

## 5. セキュリティ仕様

### noindex

全ページに以下を設定します。

HTML:

```html
<meta name="robots" content="noindex,nofollow,noarchive">
```

HTTPヘッダー:

```apache
X-Robots-Tag: noindex, nofollow, noarchive
```

### セキュリティヘッダー

`.htaccess` で以下を設定します。

- `X-Robots-Tag`
- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: no-referrer`
- `Permissions-Policy`
- `Content-Security-Policy`

### ファイル保護

以下の直接アクセスを拒否します。

- ドットファイル
- `.env`
- `.ini`
- `.log`
- `.csv`
- `.bak`
- `.old`
- `.sql`
- `.git`
- `data/` 配下

### ディレクトリ一覧

ディレクトリ一覧は無効です。

```apache
Options -Indexes
```

### 海外IP制限

海外IP制限は現在使用していません。

理由:

- サーバー環境により国判定変数が安定しないため
- 表示用サイネージページを止めない運用を優先するため

現在は、管理画面をBasic認証と管理ログインで保護します。

## 6. データ保存仕様

### 状態データ

会場予定、広告設定、表示設定はサーバー側に保存します。

保存先:

```text
data/signage-state.json
```

直接閲覧は `.htaccess` により拒否します。

### 更新ログ

保存操作時に更新ログを記録します。

保存先:

```text
data/signage-updates.log
```

記録内容:

- 保存日時
- アクセス元IP
- User-Agent
- イベント件数

### メディアファイル

広告用PDF・画像はサーバー側に保存します。

保存先:

```text
uploads/
```

状態データには `./uploads/xxxxx.pdf` のような相対URLを保存します。

## 7. 広告表示仕様

### 対象

- 広告①
- 広告②

### 表示形式

各広告は以下の表示形式を選択できます。

- 縦
- 横

### 縦表示

1枠にPDFまたは画像を表示します。

### 横表示

上下2枠に分けて表示します。

- 上段用素材
- 下段用素材

### 対応ファイル

画像:

- jpg
- jpeg
- png
- webp
- gif

PDF:

- pdf

複数ページPDFはページごとにスライド対象になります。

### PDF表示

PDFはPDF.jsを利用してページ画像として表示します。

PDF.jsは以下のCDNを利用します。

```text
https://cdn.jsdelivr.net/npm/pdfjs-dist@4.10.38
```

そのため、PDF表示にはサイネージ端末からインターネット接続が必要です。

## 8. 会場案内仕様

### 入力項目

- 日付
- 時間
- 会場
- 名前

### 会場

会場は1行入力です。

複数会場を表示したい場合は、カンマ区切りで入力します。

例:

```text
会場A,会場B
```

表示時は改行されます。

### 名前

名前は複数行入力に対応します。

入力した改行は保存され、サイネージ表示にも反映されます。

### 表示フラグ

会場予定は、初期状態ではサイネージに表示されません。

管理画面で「表示」を押した予定だけがサイネージに表示されます。

「表示中」を押すと非表示に戻ります。

### 日付タブ

会場管理画面では、本日から7日分のタブを表示します。

各タブには、その日の登録件数を表示します。

### 時刻判定

時刻判定は日本・東京時間を使用します。

```text
Asia/Tokyo
```

### 開催中表示

予定時刻と現在時刻をもとに、開催中表示を行います。

表示対象は「表示」フラグが有効な予定です。

## 9. CSVインポート仕様

会場管理画面でCSVをインポートできます。

CSVから取得する主な項目:

- 開始日
- 開始時刻
- 会場
- 名前
- グループ名
- 会社名
- ステータス

キャンセル扱いの行は取り込み対象外です。

同じ日付のデータを再インポートした場合、その日付の予定をCSV内容で更新します。

同一予定と判定できる場合、表示フラグは保持します。

## 10. 管理マニュアル仕様

操作マニュアルPDFを配置します。

ファイル:

```text
itaya_signage_manual.pdf
```

管理画面のグローバルナビゲーション右側に「操作マニュアル」ボタンを表示します。

クリック時は別タブでPDFを開きます。

## 11. 公開範囲

表示用ページはBasic認証なしで閲覧できます。

そのため、正確には以下の状態です。

- URLを知っている人は表示ページを閲覧可能
- 検索エンジンには登録されにくい
- ディレクトリ一覧から探すことはできない
- `data/` 配下の保存データは直接閲覧不可

完全な非公開ページではありません。

## 12. 主なファイル

| ファイル | 役割 |
| --- | --- |
| `index.html` | 会場管理トップ |
| `admin-ad1.html` | 広告①管理 |
| `admin-ad2.html` | 広告②管理 |
| `admin-venue.html` | 会場管理 |
| `venue.html` | 会場表示 |
| `ad1.html` | 広告①表示 |
| `ad2.html` | 広告②表示 |
| `app.js` | 管理・表示ロジック |
| `styles.css` | 画面スタイル |
| `auth.php` | 認証・入力正規化共通処理 |
| `login.php` | 管理ログイン |
| `state.php` | 状態データ取得・保存 |
| `upload.php` | PDF・画像アップロード |
| `.htaccess` | Basic認証・ヘッダー・アクセス制御 |
| `.user.ini` | PHPエラー表示抑止 |
| `itaya_signage_manual.pdf` | 操作マニュアル |
