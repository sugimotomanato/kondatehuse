<?php
ob_start();
session_start();// ハッシュを読み込む
// エラーメッセージを初期化
$errors = [];
$code = '';
$name = '';
$complete_page = 'complete.php'; 

// ==========================================================
// データベース接続設定 (ロリポップ情報とDB設計を統合)
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan';// データベース名
$db_user = 'LAA1685019-kondatehausu'; // 画像から推測される正しいユーザー名
$db_pass = '6group'; // 画像のパスワードから推測
$db_name = 'LAA1685019'; // 画像のDB名から推測
 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
// 受け取った値
$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
$password = htmlspecialchars($_POST['password'], ENT_QUOTES);
$email = htmlspecialchars($_POST['email'], ENT_QUOTES);

// ★ 半角英数字でなければエラー
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "メールアドレスの形式が間違っています。";
    header('Location: ./U20.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
 

  $stmt = $pdo->prepare("SELECT * FROM `system` WHERE BINARY `email` = ?");
        $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "このアカウントは既に登録されています。別のアカウントを入力してください。";
                    header('Location: ./U20.php');
                    exit();
            }
         


 
} catch (PDOException $e) {
 error_log("DBエラー: " . $e->getMessage());
        $_SESSION['error'] = "接続エラーが発生しました。";
        header('Location: ./U15ADMIN_LOGIN.php');
        exit();
    }
}
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


<p>管理者名：<?= htmlspecialchars($name, ENT_QUOTES); ?></p>
<p>パスワード：<?= str_repeat("●", strlen($password)) ?></p>
<p>メールアドレス：<?= htmlspecialchars($email, ENT_QUOTES); ?></p>

<!-- 戻るボタン -->
<form action="U20.php" method="post">
    <input type="hidden" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES); ?>">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES); ?>">
    <input type="hidden" name="password" value="<?= htmlspecialchars($password, ENT_QUOTES); ?>">
    <button type="submit">戻る</button>
</form>

<!-- 登録ボタン -->
<form action="U22Done.php" method="post">
    <input type="hidden" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES); ?>">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES); ?>">
    <input type="hidden" name="password" value="<?= htmlspecialchars($password, ENT_QUOTES); ?>">
    <button type="submit">登録する</button>
</form>

</body>
</html>
