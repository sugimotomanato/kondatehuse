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
</head>
<body>
        <h1>献立家</h1>
    <form action="U20" method="post">
        <label for="">
        管理者名
        <p><input type="text" name="ID" id=""></p>
        </label>
        <label for="">
        パスワード（半角英数8文字以上）
       <p><input type="password" name="passward" id=""></p> 
        </label>
        <label for="">
        メールアドレス
       <p><input type="email" name="email" id=""></p> 
        </label>
        <button type="submit">登録</button>
</form>
</body>
</html>