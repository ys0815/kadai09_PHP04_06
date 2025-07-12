<?php
//ブックマークの表示/非表示切り替え処理
// データベース接続関数が定義されたファイルを読み込む
require_once 'db.php';
require_once 'funcs.php';
// ユーザーがログインしているかチェック
loginCheck();
// 管理者権限があるかチェック
adminCheck();

// GETパラメータからブックマークのIDを取得
$id = $_GET['id'] ?? '';
// idが未指定だった場合のエラー処理
if (empty($id)) {
    exit('IDが指定されていません。');
}

$pdo = db_conn(); // データベース接続

// SQL文：is_hiddenの値を反転（0→1、1→0）
// 0: 表示中 → 非表示に変更、1: 非表示中 → 表示に変更
$sql = "UPDATE gs_kadai_table03 SET is_hidden = 1 - is_hidden WHERE id = :id";
$stmt = $pdo->prepare($sql);

// :id のプレースホルダに値をバインド（セキュリティ対策）
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
// SQLクエリを実行
$status = $stmt->execute();

// SQL実行結果の確認
if ($status == false) {
    // SQL実行時にエラーが発生した場合
    $error = $stmt->errorInfo(); // エラー情報を取得
    exit("SQLError:" . $error[2]); // エラーメッセージを表示して処理を終了
} else {
    // SQLが正常に実行された場合
    // 処理後、ブックマーク一覧ページにリダイレクトします。
    redirect("bookmark_list.php");
}
