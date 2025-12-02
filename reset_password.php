<?php
session_start();

$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';
$db_pass = '6group';
$db_name = 'LAA1685019-kondatehausu';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("DB接続エラー");
}

$token = $_GET['token'] ?? '';
if (!$token) exit("無効なリクエストです");

// トークンの有効性確認（期限内かつ存在するか）
$stmt = $pdo->prepare("SELECT * FROM `password_resets` WHERE `expires_at` >= NOW()");
$stmt->execute();
$tokens = $stmt->fetchAll();

$validToken = false;
foreach ($tokens as $row) {
    if (password_verify($token, $row['token'])) {
        $validToken = true;
        break;
    }
}

if (!$validToken) {
    exit("無効または期限切れのトークンです");
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>パスワード再設定</title>
<style>
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: sans-serif;
    background: #f0f0f0;
}
form {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    width: 300px;
}
input, button {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    font-size: 16px;
}
button {
    background: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}
button:hover {
    background: #45a049;
}
.error {
    color: red;
    font-size: 14px;
    margin-top: 5px;
}
</style>
</head>
<body>
<form method="post" action="reset_password_process.php" onsubmit="return validatePasswords();">
    <h2>新しいパスワードを入力してください</h2>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <input type="password" name="password" id="password" placeholder="新しいパスワード" required>
    <input type="password" name="password_confirm" id="password_confirm" placeholder="確認用パスワード" required>
    <div class="error" id="error"></div>
    <button type="submit">更新</button>
</form>

<script>
function validatePasswords() {
    const pw1 = document.getElementById('password').value;
    const pw2 = document.getElementById('password_confirm').value;
    const errorDiv = document.getElementById('error');

    if (pw1 !== pw2) {
        errorDiv.textContent = "パスワードが一致しません。";
        return false; // フォーム送信を止める
    }
    if (pw1.length < 8) {
        errorDiv.textContent = "パスワードは8文字以上必要です。";
        return false;
    }
    return true; // 送信OK
}
</script>
</body>
</html>


