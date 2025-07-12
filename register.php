<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規ユーザー登録</title>
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
        </div>
    </header>

    <div class="content-area">
        <h2>新規ユーザー登録</h2>
        <form method="POST" action="register_act.php" class="auth-form">
            <label><input type="text" name="name" placeholder="名前" required></label>
            <label><input type="text" name="lid" placeholder="ログインID" required></label>
            <label><input type="password" name="lpw" placeholder="パスワード" required></label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="kanri_flg" value="0" required>
                    <span>一般</span>
                </label>
                <label>
                    <input type="radio" name="kanri_flg" value="1">
                    <span>管理者</span>
                </label>
            </div>

            <button type="submit">登録する</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 MOVIE REVIEW REN02.</p>
    </footer>
</body>

</html>