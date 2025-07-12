<?php
// db.php を読み込んで、データベースに接続する
require_once 'db.php';

// funcs.php を読み込んで、Loginチェック
require_once 'funcs.php';

loginCheck();

$pdo = db_conn();

$id = $_POST['id'] ?? '';
$title = $_POST['title'] ?? '';
$poster = $_POST['poster'] ?? '';
$url = $_POST['url'] ?? '';
$review = $_POST['review'] ?? '';
$watched_date = $_POST['watched_date'] ?? '';
$watched_method = $_POST['watched_method'] ?? '';

//バリデーション

if ($id === '' || $review === '' || $watched_date === '' || $watched_method === '') {
    exit('すべての項目を入力してください');
}

// if (mb_strlen($review) > 1000) {
//     exit('レビューは1000文字以内で入力してください');
// }

// 更新対象のブックマークのuser_idをDBから取得する
$sql_check = "SELECT user_id FROM gs_kadai_table03 WHERE id = :id";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->bindValue(':id', $id, PDO::PARAM_INT);
$stmt_check->execute();
$bookmark = $stmt_check->fetch(PDO::FETCH_ASSOC);

// ブックマークの所有者でなければ、処理を中断
if ($bookmark['user_id'] != $_SESSION['user_id'] && $_SESSION['kanri_flg'] != 1) {
    exit('このブックマークを更新する権限がありません。');
}

// SQL（更新）
$sql = "UPDATE gs_kadai_table03 SET review=:review, watched_date=:watched_date, watched_method=:watched_method WHERE id=:id";
$stmt = $pdo->prepare($sql);
// $stmt->bindValue(':title', $title, PDO::PARAM_STR);
// $stmt->bindValue(':poster', $poster, PDO::PARAM_STR);
// $stmt->bindValue(':url', $url, PDO::PARAM_STR);
$stmt->bindValue(':review', $review, PDO::PARAM_STR);
$stmt->bindValue(':watched_date', $watched_date, PDO::PARAM_STR);
$stmt->bindValue(':watched_method', $watched_method, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status === false) {
    $error = $stmt->errorInfo();
    exit("Update Error: " . $error[2]);
} else {
    header("Location: bookmark_list.php");
    exit;
}
