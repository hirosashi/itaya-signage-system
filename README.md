# Hotel New Itaya Signage System

ホテルニューイタヤ向けデジタルサイネージ CMS の開発リポジトリです。

## 現在の構成

- `index.html`: 管理画面と表示画面のエントリ
- `styles.css`: 管理画面・サイネージ表示のスタイル
- `app.js`: 広告素材管理、会場案内管理、表示ロジック
- `assets/`: ロゴとサンプル広告素材

## 画面

- 管理画面: `index.html`
- 広告利用画面1: `index.html?screen=ad1`
- 広告利用画面2: `index.html?screen=ad2`
- 会場案内: `index.html?screen=venue`

## メモ

現状はブラウザの `localStorage` と `IndexedDB` にデータを保存する静的プロトタイプです。
今後、複数端末で共有する本番運用にする場合は、認証、サーバー保存、素材アップロード先、端末管理、公開 URL の設計が必要です。
