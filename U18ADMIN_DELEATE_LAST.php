<?php
// エラーメッセージを初期化
$errors = [];
$code = '';
$name = '';
$complete_page = 'complete.php'; 

// ==========================================================
// データベース接続設定 (ロリポップ情報とDB設計を統合)
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019-kondatehausu';
$db_pass = '6group';
$db_name = 'LAA1685019';

$id = $_GET['parent_account_ID'] ?? ''; // 安全に取得
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>削除確認</title>
        <style>
body {
    margin: 0;
    padding: 0;
    background-image: url('haikei2.jpg'); /* ← これが背景画像！ */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: "ヒラギノ角ゴ ProN", sans-serif;
    text-align: center;
}
      </style>
</head>
<body>
    <h1>削除しますか？</h1>
    <p>ID: <?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?></p>

    <form action="U19ADMIN_MAKE.php" method="post" style="display:inline;">
        <input type="hidden" name="parent_account_ID" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" name="action" value="delete">はい</button>
    </form>

    <form action="U17ADMIN_DELEATE.php" method="get" style="display:inline;">
        <button type="submit">いいえ</button>
    </form>
</body>
</html>