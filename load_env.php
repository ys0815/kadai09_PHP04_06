<?php
// .envファイルを読み込み、環境変数として設定する関数
function loadEnv($filePath)
{
    // 指定された.envファイルが存在するか確認（なければエラーを出す）
    if (!file_exists($filePath)) {
        throw new Exception(".env file not found: {$filePath}");
    }

    // .envファイルを1行ずつ読み込む（改行を除外、空行はスキップ）
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name); // 余計な空白を削除
        $value = trim($value); // 余計な空白を削除

        // 取得した変数を環境変数として登録する
        putenv("{$name}={$value}");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}
