<?php
// db.php を読み込んで、データベースに接続する関数を使用できるようにする
require_once 'db.php';
// funcs.php を読み込んで、ログイン状態を確認する関数などを使用可能にする
require_once 'funcs.php';
// ログインチェック（未ログインならログインページへリダイレクト）
loginCheck();
// データベース接続
$pdo = db_conn();

// データ登録SQL作成
$sql = "SELECT * FROM gs_kadai_table03";
$stmt = $pdo->prepare($sql);

// ログインユーザーの情報を変数に代入
$user_id = $_SESSION['user_id'];
$is_admin = ($_SESSION['kanri_flg'] == 1); // 管理者フラグが1なら true

// SQLのベースは条件によって分岐
if ($is_admin) {
    // 管理者の場合：すべてのレビュー（非表示も含む）を取得
    $sql = "SELECT * FROM gs_kadai_table03 ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
} else {
    // 一般ユーザーの場合：非表示にされていないレビューのみ取得
    $sql = "SELECT * FROM gs_kadai_table03 WHERE is_hidden = 0 ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
}
// SQLを実行
$status = $stmt->execute();


// データ表示
$values = "";
// 実行結果のチェック
if ($status == false) {
    // エラーがあった場合はその内容を表示して終了
    $error = $stmt->errorInfo();
    exit("SQLError:" . $error[2]);
}

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
$json = json_encode($values, JSON_UNESCAPED_UNICODE);
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
                <!-- ログイン状態に応じて表示内容を分ける -->
                <?php if (isset($_SESSION['chk_ssid']) && isset($_SESSION['name'])): ?>
                    <!-- ログイン中の表示 -->
                    <span class="user-info">
                        <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES) ?> さん｜
                        <a href="logout.php" class="inout">ログアウト</a>
                    </span>
                <?php else: ?>
                    <!-- ログインしていない場合 -->
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

    <!-- 映画ブックマーク一覧の表示エリア -->
    <div class="content-area">
        <!-- レビューが1件もない場合のメッセージ -->
        <?php if (empty($values)): ?>
            <p class="info-message">まだレビューがありません。映画検索ページからレビューしてみましょう！</p>
        <?php else: ?>
            <h2>レビュー 一覧</h2>
            <ul>
                <!-- データベースから取得したレビューを1件ずつ表示 -->
                <?php foreach ($values as $row): ?>
                    <li class="movie-item <?= $row['is_hidden'] ? 'is-hidden' : '' ?>">
                        <!-- 映画タイトル -->
                        <strong><?= h($row['title']) ?></strong>

                        <!-- 映画ポスター ※登録されていない場合もある -->
                        <?php if (!empty($row['poster'])): ?>
                            <img src="<?= h($row['poster']) ?>" alt="ポスター">
                        <?php endif; ?>
                        <!-- TMDbの公式ページへのリンク -->
                        <a href="<?= h($row['url']) ?>" target="_blank" class="official-link">公式ページ</a>
                        <span class="review-label">【 感想 】</span>
                        <!-- 自分が入力したレビュー -->
                        <p class="review-text"><?= nl2br(h($row['review'])) ?></p><br>
                        <p class="review-text">視聴日：<?= h($row['watched_date']) ?></p>
                        <p class="review-text">視聴方法：<?= h($row['watched_method']) ?></p>
                        <!-- ブックマーク登録日 -->
                        <small class="created-at">登録日：<?= h($row['created_at']) ?></small>

                        <!-- 管理者用の操作ボタン（非表示切り替え、削除） -->
                        <?php if ($is_admin): ?>
                            <div class="admin-controls">
                                <?php if ($row['is_hidden']): ?>
                                    <span class="hidden-label">※非表示中</span>
                                <?php endif; ?>
                                <!-- 表示切り替え -->
                                <a href="toggle_hide.php?id=<?= h($row['id']) ?>" class="admin-btn">
                                    <?= $row['is_hidden'] ? '再表示' : '非表示' ?>
                                </a>
                                <!-- 削除ボタン -->
                                <a href="delete.php?id=<?= h($row['id']) ?>" class="delete-btn" onclick="return confirm('本当に削除しますか？');">削除</a>
                            </div>
                        <?php endif; ?>

                        <!-- 自分の投稿であれば編集ボタン表示 -->
                        <?php if ($row['user_id'] == $_SESSION['user_id']): ?>
                            <div class="action-buttons">
                                <a href="detail.php?id=<?= h($row['id']) ?>" class="detail-btn">編集する</a>
                            </div>
                        <?php endif; ?>

                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 MOVIE REVIEW REN02.</p>
    </footer>
</body>

</html>