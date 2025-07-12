<?php
// エラー表示
ini_set("display_errors", 1); // エラー表示設定


// .envファイルの読み込み
require_once __DIR__ . '/load_env.php';
$envFile = ($_SERVER['SERVER_NAME'] === 'localhost')
    ? __DIR__ . '/.env.local' // ローカル用の設定ファイル
    : __DIR__ . '/.env.production'; // 本番用の設定ファイル
// 選ばれた.envファイルを読み込み、環境変数に登録
loadEnv($envFile);

// データベース接続
function db_conn()
{
    // .envから読み込んだ環境変数を使って接続情報を設定
    $db_name = getenv('DB_NAME');
    $db_id   = getenv('DB_ID');
    $db_pw   = getenv('DB_PW');
    $db_host = getenv('DB_HOST');

    try {
        // PDOを使ってMySQLに接続（データベース名・文字コード・ホスト名、ユーザーID・パスワード）
        $pdo = new PDO("mysql:dbname={$db_name};charset=utf8;host={$db_host}", $db_id, $db_pw);
        // 接続オブジェクトを返す（これを使ってSQLを実行）
        return $pdo;
    } catch (PDOException $e) {
        // 接続に失敗した場合はエラーを表示して処理を終了する
        exit('DB Connection Error:' . $e->getMessage());
    }
}
