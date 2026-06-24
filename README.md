# Hotel New Itaya Signage System

ホテルニューイタヤ向けデジタルサイネージ CMS の開発リポジトリです。

## 現在の構成

- `index.html`: 管理画面と表示画面のエントリ
- `styles.css`: 管理画面・サイネージ表示のスタイル
- `app.js`: 広告素材管理、会場案内管理、表示ロジック
- `assets/`: ロゴとサンプル広告素材

## 画面

- 管理画面 広告1: `index.html`
- 管理画面 広告2: `admin-ad2.html`
- 管理画面 会場: `admin-venue.html`
- 広告利用画面1: `ad1.html`
- 広告利用画面2: `ad2.html`
- 会場案内: `venue.html`

会場案内は `venue.html?date=YYYY-MM-DD` で表示日を指定できます。時刻確認用の `time=HH:MM` と組み合わせる場合は `venue.html?date=YYYY-MM-DD&time=HH:MM` を使用します。

管理画面は簡易ログイン後に表示されます。現在の初期パスワードは `itaya2026` です。

## メモ

現状はブラウザの `localStorage` と `IndexedDB` にデータを保存する静的プロトタイプです。
今後、複数端末で共有する本番運用にする場合は、認証、サーバー保存、素材アップロード先、端末管理、公開 URL の設計が必要です。
