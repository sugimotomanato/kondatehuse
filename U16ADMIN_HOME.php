<?php
// ================================================
// ロリポップ MySQL 接続テスト（本番環境用）
// ================================================
 
$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';                 // データベース名
 
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    echo "✅ ロリポップDB接続成功<br>";
 
    // データベース動作確認（現在時刻を取得）
    $stmt = $pdo->query("SELECT NOW() AS now_time");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "現在時刻: " . htmlspecialchars($row['now_time'], ENT_QUOTES, 'UTF-8');
 
} catch (PDOException $e) {
    echo "❌ DB接続エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
 






<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
          background-image: url('images/haikei2.jpg');
          background-size: cover;      /* 画面全体にフィット */
          background-position: center; /* 中央に配置 */
          background-repeat: no-repeat;/* 繰り返さない */
        }
      </style>
</head>
<body>
    <img src="haikei2.jpg" alt="料理の写真" width="400" style="margin-top: 120px; margin-bottom: 120px;"><br>
        <h1>献立家</h1>
<a href="U17ADMIN_DELEATE.php">ユーザ退会処理 ></a>

</body>
</html>