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
$db_user = 'LAA1685019-kondatehausu'; // 画像から推測される正しいユーザー名
$db_pass = '6group'; // 画像のパスワードから推測
$db_name = 'LAA1685019'; // 画像のDB名から推測




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
<div style="text-align: center;">
    <img src="images/haikei2.jpg" alt="料理の写真" width="400" style="margin-top: 120px; margin-bottom: 120px;"><br>
    <form action="U16.php" method="post">
       <label for="system_ID">管理者ID</label>
<p><input type="text" name="system_ID" id="system_ID" required></p>

<label for="system_password">パスワード（半角英数8文字以上）</label>
<p><input type="password" name="system_password" id="system_password" required></p>

        <button type="submit">ログイン</button>
    </form>

    <a href="U19.php">管理者登録はこちら</a>
</div>

</body>
</html>
