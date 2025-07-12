<?php
// db.php を読み込んで、データベースに接続する関数を使えるようにする
require_once 'db.php';
// funcs.php を読み込んで、ログインチェックの関数などを使えるようにする
require_once 'funcs.php';
// ログインしていなければログインページへ飛ばす
loginCheck();
// DB接続
$pdo = db_conn();

// GETでidが送られてきているかチェック
$id = $_GET['id'] ?? '';
if ($id === '') {
    exit('IDが指定されていません');
}

// 指定されたIDのレビューを取得するSQLを準備
$sql = "SELECT * FROM gs_kadai_table03 WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_GET["id"], PDO::PARAM_INT); // idは数値なのでINTでバインド
$status = $stmt->execute();

// データ表示
if ($status == false) {
    // SQL実行に失敗した場合
    $error = $stmt->errorInfo();
    exit("SQLError:" . $error[2]);
}

// SQL実行成功：1件のレビューを取得
$row =  $stmt->fetch(PDO::FETCH_ASSOC); // 編集対象の1件のデータを取得

// 自分の投稿ではない場合、または管理者でない場合は編集できない
if ($row['user_id'] != $_SESSION['user_id'] && $_SESSION['kanri_flg'] != 1) {
    exit('このブックマークを編集する権限がありません。');
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
</head>

<body>
    <header class="site-header">
        <div class="header-top">
            <div class="header-left">
                <a href="bookmark_list.php">レビュー 一覧</a>｜<a href="index.php">映画検索</a>
            </div>
            <div class="header-right">
                <?php if (isset($_SESSION['chk_ssid']) && isset($_SESSION['name'])): ?>
                    <!-- ログイン中のユーザー名とログアウトリンクを表示 -->
                    <span class="user-info">
                        <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES) ?> さん｜
                        <a href="logout.php" class="inout">ログアウト</a>
                    </span>
                <?php else: ?>
                    <!-- ログインしていない場合はログインリンクを表示 -->
                    <span class="user-info">
                        <a href="login.php" class="inout">ログイン</a>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="header-center">
            <a href="index.php"><img src="./img/review-logo.png" alt="REVIEWロゴ" class="logo"></a>
        </div>
    </header>

    <!-- メインコンテンツエリア：編集フォーム -->
    <div class="content-area">

        <h2>レビューを編集する</h2>
        <form method="POST" action="update.php" class="bookmark-edit-form">
            <div class="movie-item">
                <!-- id（非表示項目） -->
                <input type="hidden" name="id" value="<?= h($row['id']) ?>">
                <!-- 映画タイトル表示（編集不可） -->
                <strong>「 <?= h($row['title']) ?> 」</strong>

                <!-- ポスターが登録されていれば表示 -->
                <?php if (!empty($row['poster'])): ?>
                    <img src="<?= h($row['poster']) ?>" alt="ポスター">
                <?php endif; ?>
                <a href="<?= h($row['url']) ?>" target="_blank" class="official-link">公式ページ</a>
                <span class="review-label">【 感想 】</span>
                <textarea name="review" required><?= h($row['review']) ?></textarea>
                <!-- 視聴した日にち -->
                <label>視聴日
                    <input type="date" name="watched_date" value="<?= h($row['watched_date']) ?>" required>
                </label>
                <!-- 視聴方法 -->
                <label>視聴方法
                    <select name="watched_method" required>
                        <option value="">選択してください</option>
                        <option value="映画館" <?= $row['watched_method'] == '映画館' ? 'selected' : ''; ?>>映画館</option>
                        <option value="配信サービス" <?= $row['watched_method'] == '配信サービス' ? 'selected' : ''; ?>>配信サービス</option>
                        <option value="DVD / BD / VHS" <?= $row['watched_method'] == 'DVD / BD / VHS' ? 'selected' : ''; ?>>DVD/BD</option>
                        <option value="TV地上波" <?= $row['watched_method'] == 'TV地上波' ? 'selected' : ''; ?>>TV地上波</option>
                        <option value="その他" <?= $row['watched_method'] == 'その他' ? 'selected' : ''; ?>>その他</option>
                    </select>
                </label>
                <small class="created-at">登録日：<?= h($row['created_at']) ?></small>

                <div class="action-buttons">
                    <button type="submit">更新する</button>
                </div>

            </div>
        </form>

    </div>

    <footer>
        <p>&copy; 2025 MOVIE REVIEW REN02.</p>
    </footer>
</body>

</html>