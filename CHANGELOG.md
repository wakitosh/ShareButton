# Changelog / 変更履歴

All notable changes to this project will be documented in this file.
このファイルでは、プロジェクトの主な変更点を記録します。

This project follows semantic versioning. Dates use ISO-8601 (YYYY-MM-DD).
本プロジェクトはセマンティック バージョニングに従い、日付は ISO-8601（YYYY-MM-DD）形式です。

## [1.0.0] - 2025-10-18

### English
- Initial public release of ShareButton.
- Derived from the Omeka S "Sharing" module (no upstream changelog available)
- View helper: provide `shareButton` with backward-compatible alias `sharing`
- Block layouts (site and resource page): register only `sharebutton` in invokables and add alias `sharing` to avoid duplicate entries in the UI
- Controllers and routes: embed endpoints for items, media, and pages; oEmbed endpoint
- Assets: CSS and JS for share buttons
- i18n: translator configuration for gettext `.mo` files
- Module metadata: GPLv3 license, author (Toshihito Waki), repository links, tags
- Code style: cleaned and linted `config/module.config.php` (docblock, indentation, uppercase NULL)
- Compatibility: Omeka S ^4.0.0

### 日本語
- ShareButton の初回公開リリース。
- Omeka S「Sharing」モジュールからの派生（上流の CHANGELOG は未提供）
- ビューヘルパー: `shareButton` を提供し、後方互換のエイリアス `sharing` を用意
- ブロックレイアウト（サイト/リソースページ）: invokables には `sharebutton` のみ登録し、UI の重複表示回避のため `sharing` をエイリアス化
- コントローラとルート: アイテム/メディア/ページ用の埋め込みエンドポイント、および oEmbed エンドポイントを提供
- アセット: 共有ボタン用の CSS と JS を同梱
- 国際化: gettext `.mo` の翻訳設定を追加
- メタデータ: GPLv3 ライセンス、著者（Toshihito Waki）、リポジトリリンク、タグを整備
- コードスタイル: `config/module.config.php` を整形/静的検査（DocBlock、インデント、NULL 大文字）
- 互換性: Omeka S ^4.0.0

