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

// 受け取った値
$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
$password = htmlspecialchars($_POST['password'], ENT_QUOTES);
$email = htmlspecialchars($_POST['email'], ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>入力内容確認</title>
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

<h2>入力内容の確認</h2>


<p>管理者名：<?= $name ?></p>
<p>パスワード：<?= $password ?></p>
<p>メールアドレス：<?= $email ?></p>


<!-- 戻るボタン（入力値を保持して form.php に戻す） -->
<form action="U20.php" method="post">
    <input type="hidden" name="name" value="<?= $name ?>">
    <input type="hidden" name="email" value="<?= $email ?>">
    <input type="hidden" name="password" value="<?= $password ?>">
    <button type="submit">戻る</button>
</form>

<!-- DB登録へ -->
<form action="U22Done.php" method="post">
    <input type="hidden" name="name" value="<?= $name ?>">
    <input type="hidden" name="email" value="<?= $email ?>">
    <input type="hidden" name="password" value="<?= $password ?>">
    <button type="submit">登録する</button>
</form>

</body>
</html>
