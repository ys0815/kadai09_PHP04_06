<?php
// db.php を読み込んで、データベースに接続する
require_once 'db.php';

// funcs.php を読み込んで、Loginチェック
require_once 'funcs.php';

loginCheck();
adminCheck();

$pdo = db_conn();

// GETでidが送られてきているかチェック
$id = $_GET['id'] ?? '';
if ($id === '') {
    exit('IDが指定されていません');
}

// データ削除SQL作成
$sql = "DELETE FROM gs_kadai_table03 WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

// データ削除処理後
if ($status == false) {
    // SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("Delete Error: " . $error[2]);
} else {
    // 正常に削除できた場合は、bookmark_list.phpへリダイレクト
    header("Location: bookmark_list.php");
    exit;
}
