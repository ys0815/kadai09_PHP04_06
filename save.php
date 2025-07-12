<?php
// db.php を読み込んで、データベースに接続する
require_once 'db.php';

// funcs.php を読み込んで、Loginチェック
require_once 'funcs.php';
// セッションを開始
loginCheck();

$pdo = db_conn();

// POSTデータ取得
$id = $_POST['id'] ?? '';
$title = $_POST['title'] ?? '';
$poster = $_POST['poster'] ?? '';
$url = $_POST['url'] ?? '';
$review = $_POST['review'] ?? '';
$watched_date = $_POST['watched_date'] ?? '';
$watched_method = $_POST['watched_method'] ?? '';

// セッションからユーザーIDを取得
$user_id = $_SESSION['user_id'] ?? '';

// バリデーション
// 必須項目が1つでも空だったらエラーメッセージを表示して終了
if ($title === '' || $url === '' || $review === '' || $poster === '' || $watched_date === '' || $watched_method === '') {
    exit('すべての項目を入力してください');
}
// URLの形式が正しくない場合はエラーメッセージを表示して終了
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    exit('URLの形式が正しくありません');
}
// レビューが1000文字を超えていたらエラーメッセージを表示して終了
if (mb_strlen($review) > 1000) {
    exit('レビューは1000文字以内で入力してください');
}

// データ登録SQL作成
$sql = "INSERT INTO gs_kadai_table03 (user_id, title, poster, url, review, watched_date, watched_method) VALUES (:user_id, :title, :poster, :url, :review, :watched_date, :watched_method)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':title', $title, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':poster', $poster, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':url', $url, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':review', $review, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':watched_date', $watched_date, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':watched_method', $watched_method, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute(); //ここで実装する！

// データ登録処理後
if ($status == false) {
    // SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("SQLError" . $error[2]);
} else {
    // 正常に登録できた場合は、bookmark_list.phpへリダイレクト
    header("Location: bookmark_list.php");
    exit;
}
