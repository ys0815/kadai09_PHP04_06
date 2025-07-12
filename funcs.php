<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

//リダイレクト
function redirect($file_name)
{
    header("Location: " . $file_name);
    exit();
}

// 認証チェック
function loginCheck()
{
    // セッションIDと保存されているセッションIDが一致しない、またはセッションIDが保存されていない場合
    if (!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid'] != session_id()) {
        // 現在アクセスしようとしているページのURL（クエリ文字列含む）をセッションに保存
        // 例: /index.php?query=映画
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect("login.php");
    }
}

// 管理者権限チェック
function adminCheck()
{
    // 管理者フラグがセットされていない、または管理者（1）ではない場合
    if (!isset($_SESSION['kanri_flg']) || $_SESSION['kanri_flg'] != 1) {
        echo '管理者権限がありません。';
        exit('<a href="bookmark_list.php">ブックマーク一覧へ戻る</a>');
    }
}
