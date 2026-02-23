# audio2video 開発者ガイド

## プロジェクト概要

音声ファイル（mp3等）を静止画付きの動画ファイル（mp4等）に変換するWebアプリケーション。
Twitter/FacebookなどSNSへの音声投稿ができない問題を解決するためのツール。
本番URL: http://audio2video.me
CLI版: [ttskch/audio2video-cli](https://github.com/ttskch/audio2video-cli)

## 技術スタック

### バックエンド
- **PHP** 7.1.3+
- **Symfony** 4.x（MicroKernelTrait使用のマイクロカーネル構成）
- **SensioFrameworkExtraBundle** 5.x（`@Route`, `@Template` アノテーション）
- **Symfony Form** / **Validator** / **Translation** / **Process**
- **テスト**: PHPUnit（Symfony PHPUnit Bridge経由、`bin/phpunit`）

### フロントエンド
- **Webpack Encore** 0.17（Symfony公式Webpackラッパー）
- **jQuery** 3.x + **Bootstrap** 4 + **Popper.js**
- **Select2**（Bootstrap4テーマ付き: `@ttskch/select2-bootstrap4-theme`）
- **Font Awesome** 4.x
- **is-loading**（フォーム送信時のオーバーレイ表示）
- **SCSS**（node-sass + sass-loader）
- **PostCSS**（autoprefixer）

### 外部ツール（サーバー側）
- **FFmpeg** / **FFprobe**: 音声→動画変換の中核処理
- **ImageMagick** + **Imagick PHP拡張**: ブランク画像の生成

### インフラ
- **Docker**: Alpine Linux ベース（`ttskch/nginx-php-fpm-heroku` イメージ）
- **Nginx** + **PHP-FPM** 構成
- **Heroku**: Container Registry経由のデプロイ
- **GitHub Actions**: mainブランチへのプッシュでDockerイメージをビルド→Herokuへデプロイ

## ディレクトリ構成

```
├── assets/                  # フロントエンドソース
│   ├── images/logo.png
│   ├── js/app.js            # メインJavaScript（jQuery）
│   └── scss/
│       ├── _variables.scss  # Bootstrap変数のインポートのみ
│       ├── app.scss         # アプリ固有スタイル
│       ├── form-theme.scss  # フォームテーマ用追加CSS
│       └── vendors.scss     # Bootstrap/FA/Select2読み込み+カスタマイズ
├── bin/
│   ├── console              # Symfonyコンソール
│   └── phpunit              # PHPUnitラッパー
├── config/
│   ├── bundles.php          # バンドル登録
│   ├── packages/            # 環境別設定（framework, twig, translation, monolog等）
│   ├── routes/              # ルーティング設定
│   ├── services.yaml        # サービス定義（DI設定）
│   └── services_test.yaml   # テスト環境用
├── docker/
│   ├── nginx.conf           # Nginx設定（audio2video.me用）
│   └── php.ini              # PHP設定（タイムゾーン、アップロード制限等）
├── public/
│   └── index.php            # フロントコントローラー
├── src/
│   ├── Controller/HomeController.php  # 唯一のコントローラー
│   ├── Entity/ConvertCriteria.php     # フォームのデータクラス（非DB）
│   ├── Form/ConvertType.php           # Symfonyフォーム定義
│   ├── Kernel.php                     # マイクロカーネル
│   └── Service/Converter.php          # FFmpeg/Imagickを使った変換ロジック
├── templates/
│   ├── base.html.twig                 # ベーステンプレート
│   ├── home/index.html.twig           # メインページ
│   ├── form_theme/                    # Bootstrap4用カスタムフォームテーマ
│   └── widgets/                       # GA, AdSense, AddThis
├── tests/                   # テストディレクトリ（現状ほぼ空）
├── translations/
│   ├── messages.ja.yaml     # 日本語翻訳
│   └── messages.en.yaml     # 英語翻訳
├── Dockerfile               # Dockerイメージ定義
├── composer.json            # PHP依存関係
├── package.json             # npm依存関係
└── webpack.config.js        # Webpack Encore設定
```

## アーキテクチャ・設計方針

### 全体構成
- **シングルページ構成**: ルートは2つのみ（`/` と `/download/`）
- **MicroKernelTrait**: Symfony Flex + マイクロカーネルによるコンパクトな構成
- **DBなし**: データベースを使わず、ファイルアップロード→変換→ダウンロードの完結型
- **セッション**: 変換結果のファイルパスをセッションに保存し、ダウンロード時に取り出す

### 変換フロー
1. ユーザーが音声ファイルをアップロード（フォーム送信）
2. `Converter::convert()` が実行:
   - `ffprobe` で音声の再生時間を取得
   - 再生時間が制限（140秒）を超えていないかチェック
   - アップロード画像またはImagickで生成したブランク画像を連番コピー
   - `ffmpeg` で連番画像+音声から動画を生成
3. 出力ファイルパスをセッションに保存
4. レスポンスHTML内のJavaScriptで自動的に `/download/` へリダイレクト
5. `BinaryFileResponse` でファイルをダウンロード提供

### DI / サービス設定
- `services.yaml` でautowire/autoconfigure有効
- `Converter` は `app.duration_limit_sec`（140秒）をコンストラクタ引数で受け取る
- `TranslatorInterface`, `SessionInterface` は明示的エイリアスで注入

### ルーティング
- **開発環境**: アノテーションベース（`@Route` アノテーション）
- **本番環境**: `annotations.yaml.prod` に切り替え。サブドメインでロケール切り替え（`ja.audio2video.me` / `en.audio2video.me`）。`www.audio2video.me` と `ja.audio2video.me` は `audio2video.me` に301リダイレクト

### 国際化（i18n）
- デフォルトロケール: `ja`
- フォールバックロケール: `en`
- 翻訳ファイル: `translations/messages.{ja,en}.yaml`
- 翻訳キーは英語の自然文をそのまま使用（例: `Audio file`, `Convert`）。英語翻訳ファイルには日本語固有の長文コンテンツのみ記載

### フロントエンド構成
- **エントリーポイント**: `app`（`assets/js/app.js` + `assets/scss/app.scss`）
- **共有エントリー**: `vendors`（jQuery, Bootstrap, Popper.js, Select2, vendors.scss）
- アセットのバージョニング有効、SourceMapは開発時のみ
- `copy-webpack-plugin` で `assets/images/` → `public/build/images/` にコピー

### フォームテーマ
- Symfony標準の `bootstrap_4_layout.html.twig` をベースにカスタマイズした独自テーマ
- `my_bootstrap_4_layout.html.twig`: インラインラジオ/チェックボックス対応（`parent_attr.inline`）等の拡張
- `my_bootstrap_4_horizontal_layout.html.twig`: 水平レイアウト版（col-sm-3/9比率に変更）。現在は未使用（twig.yamlでコメントアウト）
- `form_theme/docs/` にパッチファイルあり（元のSymfonyテンプレートからの差分記録用）

## 実装スタイル・コーディング規約

### PHP
- PSR-4オートロード: `App\` → `src/`
- アノテーションベースのルーティング・テンプレート指定（`@Route`, `@Template`）
- アノテーションベースのバリデーション（`@Assert\*`）
- Entityクラスは `public` プロパティ（DTO的な利用、Doctrine未使用）
- `Controller` は `Symfony\Bundle\FrameworkBundle\Controller\Controller` を継承
- サービスクラスは流暢インターフェース（`setCriteria()` が `$this` を返す）
- 外部コマンド実行は `Symfony\Component\Process\Process` を使用（文字列引数で直接コマンド構築）
- タイムアウト: ffprobe/ffmpegともに60秒

### JavaScript
- ES6 import文使用（`import isLoading from 'is-loading'`）
- jQueryベースのDOM操作
- グローバルな即時実行（モジュール/クラス構成ではない）

### CSS/SCSS
- Bootstrap 4の変数とミックスインを活用
- SCSSの `@import` でモジュール分割
- 日本語環境向けフォント設定あり（ヒラギノ、メイリオ等）
- ベースフォントサイズ: 14px

### テンプレート
- Twig使用
- `base.html.twig` → 各ページテンプレートの継承構造
- ブロック: `title`, `subtitle`, `eyecatch`, `before_container`, `content`, `after_container`, `footer`, `stylesheets`, `javascripts`
- フラッシュメッセージは `alert-{type}` で表示（HTMLタグ許可: `|raw` フィルタ使用）
- 外部ウィジェット（Google Analytics, Google AdSense, AddThis）は個別テンプレートに分離

## 環境構築

### 必要要件
- PHP 7.1.3+
- npm
- Docker（FFmpeg, ImageMagickを含む）

### セットアップ
```bash
docker build . -t {tag}
composer install    # .envのコピー、npmインストール、アセットビルドも自動実行
```

### 開発サーバー起動
```bash
docker run -p 8888:8888 -v $(pwd):/docroot {tag}
# ブラウザで http://localhost:8888 を開く
```

### アセット関連コマンド
```bash
npm run dev      # 開発ビルド
npm run watch    # ウォッチモード
npm run build    # 本番ビルド
```

### テスト
```bash
bin/phpunit
```

## PHP設定（本番）
- タイムゾーン: Asia/Tokyo
- upload_max_filesize / post_max_size: 10MB
- max_execution_time: 60秒
- memory_limit: 128MB
- Nginxの `client_max_body_size`: 10MB

## CI/CD

### GitHub Actions (`.github/workflows/deploy.yaml`)
- mainブランチへのプッシュで自動実行
- `ubuntu-latest` でDockerイメージをビルド
- Heroku Container Registryにプッシュ後、Formation APIでリリース

### GitHub Secrets（リポジトリ設定で登録が必要）
- `HEROKU_EMAIL`: Herokuアカウントのメールアドレス
- `HEROKU_API_KEY`: Heroku APIキー

## 制約・注意事項

- 音声ファイルの再生時間上限: **140秒**（`app.duration_limit_sec` パラメーター）
- アップロードファイルサイズ上限: **10MB**
- `Converter` 内でシェルコマンドを文字列で直接構築しているため、ファイル名にシングルクォートを含むファイルの処理に注意
- Dockerコンテナ内のデフォルトユーザーは `nonroot`（sudoは可能）
- デフォルトブランチは `main`
