<?php
// データベース接続関数が定義されたファイルを読み込む
require_once 'db.php';

// -------------------------
// POSTデータの受け取り
// -------------------------
$name = $_POST['name'] ?? ''; // ユーザー名を取得。もし未定義なら空文字列。
$lid = $_POST['lid'] ?? ''; // ログインIDを取得。もし未定義なら空文字列。
$lpw = $_POST['lpw'] ?? ''; // パスワードを取得。もし未定義なら空文字列。
$kanri_flg = $_POST['kanri_flg'] ?? 0; // 管理者フラグを取得。未定義ならデフォルトで「0」

// -------------------------
// 入力チェック（バリデーション）
// -------------------------
if (empty($lid) || empty($lpw)) {
    exit('IDとパスワードは必須です。');
}

// -------------------------
// パスワードのハッシュ化
// -------------------------
// ユーザーが入力したパスワードを安全な形式に変換（ハッシュ化）
// データベースにはこのハッシュ値を保存する
$hashed_pw = password_hash($lpw, PASSWORD_DEFAULT);

// データベース接続
$pdo = db_conn();

// -------------------------
// ユーザー登録のSQL文を作成
// -------------------------
$sql = "INSERT INTO gs_kadai_table03_user(name, lid, lpw, kanri_flg, life_flg) VALUES(:name, :lid, :lpw, :kanri_flg, 0)";
$stmt = $pdo->prepare($sql);

// プレースホルダーに値をバインド（安全にSQLを実行するため）
$stmt->bindValue(':name', $name, PDO::PARAM_STR); // :name に $name の値を文字列型として紐づけ
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR); // :lid に $lid の値を文字列型として紐づけ
$stmt->bindValue(':lpw', $hashed_pw, PDO::PARAM_STR); // :lpw にハッシュ化されたパスワードを文字列型として紐づけ
$stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT); // :kanri_flg に $kanri_flg の値を整数型として紐づけ
$status = $stmt->execute();

// -------------------------
// 実行結果のチェック
// -------------------------
if ($status == false) {
    // SQL実行時にエラーが発生した場合
    $error = $stmt->errorInfo(); // エラー情報を取得
    exit("SQLError:" . $error[2]); // エラーメッセージを表示して処理を終了
} else {
    // 登録成功後はログインページへリダイレクト
    header("Location: login.php");
    exit();
}
