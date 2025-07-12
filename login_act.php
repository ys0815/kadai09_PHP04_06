<?php
// ----------------------------------------
// ログイン処理（login_act.php）
// ----------------------------------------
//DB接続 // ★ここへ移動！ (もし移動していなければ)
require_once 'db.php';
require_once 'funcs.php';
// データベース接続
$pdo = db_conn();

// -----------------------------
// フォームから送られてきたデータを受け取る
// -----------------------------
$lid = $_POST['lid'] ?? ''; // 'lid'（ログインID）を取得。未定義なら空文字列。
$lpw = $_POST['lpw'] ?? ''; // 'lpw'（パスワード）を取得。未定義なら空文字列。

// -----------------------------
// 入力チェック（どちらかが未入力ならエラー）
// -----------------------------
if ($lid === '' || $lpw === '') {
    // JavaScriptのアラートを出すHTMLを生成して終了
    echo '<script type="text/javascript">';
    echo 'alert("ログインIDとパスワードを入力してください");';
    echo 'history.back();'; // 一つ前のページに戻る
    echo '</script>';
    exit(); // PHPの実行を終了
}

// -----------------------------
// SQLを使ってログイン情報を確認（life_flg=0のアクティブユーザーのみ対象）
// -----------------------------
$sql = "SELECT * FROM gs_kadai_table03_user WHERE lid=:lid AND life_flg=0"; // life_flg=0 のユーザーのみ対象
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR); // :lid に $lid の値を文字列型として紐づけ
$status = $stmt->execute(); //ここで実行する

// -----------------------------
// SQL実行エラーチェック
// -----------------------------
if ($lid === '' || $lpw === '') {
    echo '<script type="text/javascript">';
    echo 'alert("ログインIDとパスワードを入力してください");'; // ユーザーにアラート表示
    echo 'history.back();'; // ブラウザの履歴で一つ前のページに戻る
    echo '</script>';
    exit(); // PHPスクリプトの実行を終了
}

// ★★ 1. funcs.phpを読み込むことでセッションが開始されるため、ここでの session_start() は不要です ★★
// 以前はこのファイルで session_start() を行っていましたが、funcs.phpで一元管理する方針になりました。
// funcs.php は必ず db.php の後に読み込むようにしてください。
// session_start(); // ←この行は削除してください

// データベース接続関数（db_conn()）が定義されたファイルを読み込む
require_once 'db.php';
// 汎用関数（認証チェック、リダイレクトなど）が定義されたファイルを読み込む
// funcs.php を読み込むことで、そのファイルの冒頭でセッションが自動的に開始されます。
require_once 'funcs.php';

// データベース接続を確立
$pdo = db_conn();

// -----------------------------
// フォームから送られてきたデータを受け取る
// -----------------------------
$lid = $_POST['lid'] ?? ''; // 'lid'（ログインID）を取得。未定義なら空文字列。
$lpw = $_POST['lpw'] ?? ''; // 'lpw'（パスワード）を取得。未定義なら空文字列。

// -----------------------------
// 入力チェック（どちらかが未入力ならエラー）
// -----------------------------
if ($lid === '' || $lpw === '') {
    echo '<script type="text/javascript">';
    echo 'alert("ログインIDとパスワードを入力してください");'; // ユーザーにアラート表示
    echo 'history.back();'; // ブラウザの履歴で一つ前のページに戻る
    echo '</script>';
    exit(); // PHPスクリプトの実行を終了
}

// -----------------------------
// SQLを使ってログイン情報を確認（life_flg=0のアクティブユーザーのみ対象）
// -----------------------------
$sql = "SELECT * FROM gs_kadai_table03_user WHERE lid=:lid AND life_flg=0";
$stmt = $pdo->prepare($sql); // SQLインジェクションを防ぐためにプリペアドステートメントを使用
// プレースホルダにログインIDの値をバインド
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR); // :lid に $lid の値を文字列型として紐づける
$status = $stmt->execute(); // SQLクエリを実行

// -----------------------------
// SQL実行エラーチェック
// -----------------------------
if ($status == false) {
    // SQL実行時にエラーが発生した場合
    $error = $stmt->errorInfo(); // エラー情報を取得
    exit("SQLError:" . $error[2]); // エラーメッセージを表示して処理を終了
}

// -----------------------------
// 取得したデータを確認（ユーザー1件のみ）
// ----------------------------
$val = $stmt->fetch();         //1レコードだけ取得する方法

// -----------------------------
// パスワードの照合（DBにあるハッシュと入力値を比較）
// -----------------------------
if ($val === false || !password_verify($lpw, $val["lpw"])) {
    //Login失敗時（login.phpへ）
    redirect("login.php");
} else {
    // -----------------------------
    // ログイン成功時：セッションに情報を保存
    // -----------------------------
    $_SESSION["chk_ssid"] = session_id(); // 現在のセッションIDを保存
    $_SESSION["kanri_flg"] = $val['kanri_flg']; // 管理者フラグを保存（1:管理者, 0:一般）
    $_SESSION["name"] = $val['name']; // ユーザー名を保存
    $_SESSION["user_id"] = $val['id']; // ユーザーIDを保存 （ブックマーク登録などで利用）

    // セッションに `redirect_after_login` （元のアクセス先URL）が保存されているかチェック
    if (isset($_SESSION['redirect_after_login']) && $_SESSION['redirect_after_login'] !== '') {
        $redirect_url = $_SESSION['redirect_after_login']; // 保存しておいたURLを取得
        unset($_SESSION['redirect_after_login']); // 使用済みなのでセッション変数から削除（次回のリダイレクトに影響しないように）
        redirect($redirect_url); // 保存されたURLへリダイレクト
    } else {
        // `redirect_after_login` が保存されていない（例: 直接ログインページに来た）場合、
        // デフォルトで `index.php` へリダイレクトします。
        redirect("index.php"); // funcs.phpで定義されたredirect関数を使用
    }
}
