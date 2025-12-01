<?php
ob_start();
session_start();

// ==========================================================
// データベース接続設定 (ロリポップ情報とDB設計を統合)
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';         // データベース名

if ($_SERVER["REQUEST_METHOD"] === "GET") {
$id = $_GET['parent_account_ID'] ?? '';
 // 安全に取得
 $ID=$id;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
 

  $stmt = $pdo->prepare("SELECT * FROM `parent_account` WHERE BINARY `parent_account_ID` = ?");
        $stmt->execute([$ID]);
        $delete_data = $stmt->fetch(PDO::FETCH_ASSOC);

 
} catch (PDOException $e) {
 error_log("DBエラー: " . $e->getMessage());
        $_SESSION['error'] = "接続エラーが発生しました。";
        header('Location: ./U17ADMIN_DELEATE.php');
        exit();
    }
}

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

    <p>ID： <?= htmlspecialchars($delete_data['parent_account_ID'], ENT_QUOTES, 'UTF-8') ?></p>
    <p>家族コード： <?= htmlspecialchars($delete_data['family_code'], ENT_QUOTES, 'UTF-8') ?></p>
    <p>ユーザー名： <?= htmlspecialchars($delete_data['user_name'], ENT_QUOTES, 'UTF-8') ?></p>



    <form action="U23ADMIN_DELEATE_COMPLETE.php" method="post" style="display:inline;">
        <input type="hidden" name="parent_account_ID" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" name="action" value="delete">はい</button>
    </form>

    <form action="U17ADMIN_DELEATE.php" method="get" style="display:inline;">
        <button type="submit">いいえ</button>
    </form>
</body>
</html>