# Changelog / 変更履歴

All notable changes to this project will be documented in this file.
このファイルでは、プロジェクトの主な変更点を記録します。

This project follows semantic versioning. Dates use ISO-8601 (YYYY-MM-DD).
本プロジェクトはセマンティック バージョニングに従い、日付は ISO-8601（YYYY-MM-DD）形式です。

## [1.1.1] - 2026-11-13

### English
- Security: the embed pages (`/embed-item`, `/embed-media`, `/embed-page`) printed the title and description of a resource without escaping, so markup stored in metadata was interpreted by the browser. Titles, descriptions and URLs are now escaped. A description that relied on HTML is shown as plain text in the embed page.
- Security: the Facebook App ID is written into the page script with HTML-safe JSON encoding, and the embed URL attribute is escaped.
- Fixed: the "Email" share link now URL-encodes the subject and body, so characters in the page address can no longer add other mail headers.
- Fixed: the oEmbed endpoint answered with a server error when the site or page in the given URL did not exist, or when a page attachment had no item. It now answers 404, or skips the attachment.
- Fixed: the embed page for a site page linked to a wrong address; it now links to the site.
- Fixed: the embed button is left out where there is nothing to embed, instead of raising a PHP warning.

### 日本語
- セキュリティ: 埋め込みページ（`/embed-item`、`/embed-media`、`/embed-page`）がリソースのタイトルと説明をエスケープせずに出力していたため、メタデータ中のマークアップがブラウザで解釈されていました。タイトル、説明、URL をエスケープするようにしました。HTML を前提にした説明は、埋め込みページではプレーンテキストで表示されます。
- セキュリティ: Facebook App ID を HTML 安全な JSON エンコードでスクリプトに出力し、埋め込み URL の属性をエスケープするようにしました。
- 修正: 「Email」共有リンクの件名と本文を URL エンコードするようにしました。ページのアドレスに含まれる文字で、ほかのメールヘッダーを追加できなくなります。
- 修正: oEmbed エンドポイントが、指定 URL のサイトやページが存在しない場合、またはページの添付にアイテムがない場合にサーバーエラーを返していました。404 を返す、または該当の添付を飛ばすようにしました。
- 修正: サイトページの埋め込みページのリンク先が誤っていたため、サイトへのリンクに直しました。
- 修正: 埋め込む対象がない場所では、PHP の警告を出す代わりに埋め込みボタンを表示しないようにしました。

## [1.1.0] - 2025-10-20

### English
- Theme-independence: Automatically add Open Graph meta tags (og:*) on Item/Media/Page show views when missing, so shares look good even if the theme doesn’t output them.
- Duplicate-safe: Skip emitting og:* tags already present (from theme or other modules).
- No breaking changes: Placement and block behavior unchanged; site settings continue to control methods and layout.

### 日本語
- テーマ非依存化: テーマが `og:*` を出力しない場合でも、アイテム/メディア/ページの詳細表示で Open Graph メタを自動補完し、共有時の見栄えを確保します。
- 重複回避: すでに出力済みの `og:*` がある場合は追加しません（テーマや他モジュールとの併用に配慮）。
- 破壊的変更なし: 表示位置やブロックの挙動は従来どおり。サイト設定でボタン種別や表示方法を制御できます。

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

