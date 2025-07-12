<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
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
                <!-- ログイン状態によって表示を切り替える -->
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
        <h2>ログイン</h2>
        <!-- ログイン後、どこのページにリダイレクトするかを保存 -->
        <?php
        session_start();
        // -----------------------------
        // 元のページのURLが指定されていた場合（ログイン後に戻すために保存）
        // 例: login.php?redirect=save.php のようにアクセスされたとき
        // -----------------------------
        if (isset($_GET['redirect'])) {
            $_SESSION['redirect_after_login'] = $_GET['redirect'];
        }
        ?>

        <form method="POST" action="login_act.php" class="auth-form">
            <label><input type="text" name="lid" placeholder="ログインID" required></label>
            <label><input type="password" name="lpw" placeholder="パスワード" required></label>
            <button type="submit">ログイン</button>
            <p class="register-link">初めてのご利用ですか？今すぐ<a href="register.php">こちら</a>から新規登録</p>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 MOVIE REVIEW REN02.</p>
    </footer>
</body>

</html>