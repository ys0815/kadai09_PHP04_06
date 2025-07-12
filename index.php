<?php
// 環境変数を読み込む関数が定義されているファイルを読み込む
require_once 'load_env.php';
require_once 'db.php';
require_once 'funcs.php'; // h関数を使うため
// データベースに接続
$pdo = db_conn();

// サーバー名（localhost or さくらサーバーなど）によって使用する .env ファイルを切り替える
$envFile = ($_SERVER['SERVER_NAME'] === 'localhost')
    ? __DIR__ . '/.env.local' // ローカル環境用の.envファイル
    : __DIR__ . '/.env.production'; // 本番サーバー用の.envファイル
// 選択された.envファイルの中身を読み込んで、環境変数に設定する
loadEnv($envFile);

// APIキーを使う
$apiKey = getenv('TMDB_API_KEY');

// 情報メッセージとエラーメッセージを入れる変数を準備
$info_message = '';
$error_message = '';
// 検索結果を入れるための箱を用意
$results = [];

// -----------------------
// 検索キーワードの処理
// -----------------------
if (!isset($_GET['query'])) {
    // 検索フォームがまだ送信されていない時（最初の画面表示時）
    $info_message = '上記に検索したい映画タイトルを入力してください。';
} elseif ($_GET['query'] === '') {
    // 空文字で検索した時
    $error_message = '検索ワードを入力してください。';
} else {
    // 検索キーワードがちゃんと送られてきた時（TMDbで実際に検索する）
    $query = urlencode($_GET['query']);
    $url = "https://api.themoviedb.org/3/search/movie?api_key={$apiKey}&language=ja-JP&query={$query}&include_adult=false";
    $json = file_get_contents($url);
    //取得した映画情報のjsonを「$data」に
    $data = json_decode($json, true);

    // 「$data」に映画の検索結果が含まれているか確認
    if (isset($data['results'])) {
        $results = $data['results'];
        // 検索ワードに映画がヒットしなかった時
        if (empty($results)) {
            $error_message = '該当する映画が見つかりませんでした。検索ワードを変えてみてください。';
        }
    } else {
        // APIから正しくデータを取れなかった時
        $error_message = '映画情報の取得に失敗しました。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOVIE REVIEW REN02.</title>
    <link href="./css/reset.css" rel="stylesheet" />
    <link href="./css/style.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Sawarabi+Gothic&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://www.youtube.com">
    <link rel="dns-prefetch" href="https://www.youtube.com">
    <link rel="preconnect" href="https://i.ytimg.com">
    <link rel="dns-prefetch" href="https://i.ytimg.com">
    <style>

    </style>

</head>

<body>
    <header class="site-header">
        <div class="header-top">
            <div class="header-left">
                <a href="bookmark_list.php">レビュー 一覧</a>｜<a href="index.php">映画検索</a>
            </div>
            <div class="header-right">
                <?php if (isset($_SESSION['chk_ssid']) && isset($_SESSION['name'])): ?>
                    <span class="user-info">
                        <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES) ?> さん｜
                        <a href="logout.php" class="inout">ログアウト</a>
                    </span>
                <?php else: ?>
                    <span class="user-info">
                        <a href="login.php" class="inout">ログイン</a>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="header-center">
            <a href="index.php"><img src="./img/review-logo.png" alt="REVIEWロゴ" class="logo"></a>
            <p>映画の楽しみ、深まる。あなたのレビューが誰かの心に。 </p>
            <span>あなたの感想が新たな感動のきっかけに。映画の輪を広げよう。</span>
        </div>
    </header>

    <!-- 映画検索フォーム -->
    <form method="GET" action="" class="search-form">
        <input type="text" name="query" placeholder="映画タイトルを入力してください" required>
        <button type="submit">検索</button>
    </form>
    <!-- 検索結果＆メッセージ表示エリア -->
    <?php if (isset($_GET['query'])): ?>
        <div class="content-area">
            <!-- メッセージがある場合に表示 -->
            <?php if (!empty($info_message)): ?>
                <p class="info-message"><?= h($info_message) ?></p>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <p class="info-message"><?= h($error_message) ?></p>
            <?php endif; ?>

            <!-- 検索結果がある場合に表示 -->
            <?php if (!empty($results)): ?>
                <h2>検索結果</h2>
                <ul>
                    <?php foreach ($results as $movie): ?>
                        <li class="movie-item">
                            <!-- 映画タイトル -->
                            <strong><?= h($movie['title']) ?></strong>
                            <!-- 映画ポスター ※登録されていない場合もある -->
                            <?php if (!empty($movie['poster_path'])): ?>
                                <img src="https://image.tmdb.org/t/p/w300<?= h($movie['poster_path']) ?>" alt="<?= h($movie['title']) ?>のポスター">
                            <?php endif; ?>
                            <span>公開日：<?= h($movie['release_date'] ?? '不明') ?></span>
                            <span>平均評価点：<?= h($movie['vote_average'] ?? 'なし') ?></span>
                            <p class="overview">あらすじ：<?= nl2br(h($movie['overview'] ?? '情報なし')) ?></p>
                            <!-- TMDbの公式ページへのリンク -->
                            <a href="https://www.themoviedb.org/movie/<?= h($movie['id']) ?>" target="_blank" class="official-link">公式ページを見る</a>

                            <!-- 予告編を取得して埋め込む -->
                            <?php
                            $movieId = $movie['id'];
                            $videoUrl = "https://api.themoviedb.org/3/movie/{$movieId}/videos?api_key={$apiKey}&language=ja-JP";
                            $videoJson = @file_get_contents($videoUrl);
                            $youtubeKey = null;
                            if ($videoJson !== false) {
                                $videoData = json_decode($videoJson, true);
                                if (!empty($videoData['results'])) {
                                    foreach ($videoData['results'] as $video) {
                                        if ($video['site'] === 'YouTube' && $video['type'] === 'Trailer') {
                                            $youtubeKey = $video['key'];
                                            break;
                                        }
                                    }
                                }
                            }
                            ?>
                            <?php if ($youtubeKey): ?>
                                <div class="trailer">
                                    <iframe width="400" height="200"
                                        src="https://www.youtube.com/embed/<?= h($youtubeKey) ?>"
                                        frameborder="0"
                                        allowfullscreen
                                        loading="lazy">
                                    </iframe>
                                </div>
                            <?php endif; ?>

                            <!-- 感想を入力してブックマークするフォーム -->
                            <?php if (isset($_SESSION["chk_ssid"])): // ログインしているかチェック 
                            ?>
                                <form method="POST" action="save.php" class="bookmark-form">
                                    <input type="hidden" name="title" value="<?= h($movie['title']) ?>">
                                    <input type="hidden" name="poster" value="https://image.tmdb.org/t/p/w300<?= h($movie['poster_path']) ?>">
                                    <input type="hidden" name="url" value="https://www.themoviedb.org/movie/<?= h($movie['id']) ?>">
                                    <textarea name="review" placeholder="映画の感想を書く" required></textarea>
                                    <!-- 視聴した日にち -->
                                    <label>視聴日
                                        <input type="date" name="watched_date" required>
                                    </label>
                                    <!-- 視聴方法 -->
                                    <label>視聴方法
                                        <select name="watched_method" required>
                                            <option value="">選択してください</option>
                                            <option value="映画館">映画館</option>
                                            <option value="配信サービス">配信サービス</option>
                                            <option value="DVD / BD / VHS">DVD / BD / VHS</option>
                                            <option value="TV地上波">TV地上波</option>
                                            <option value="その他">その他</option>
                                        </select>
                                    </label>
                                    <button type="submit">レビューを書く</button>
                                </form>
                            <?php else: // ログインしていない場合 
                            ?>
                                <div class="login-prompt">
                                    ※レビューを書くには<a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">ログイン</a>が必要です。
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <footer>
        <p>&copy; 2025 MOVIE REVIEW REN02.</p>
    </footer>
</body>

</html>