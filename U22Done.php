<?php 
$db_host = 'mysql320.phy.lolipop.lan';   // ロリポップのMySQLホスト
$db_user = 'LAA1685019';    // データベースユーザー名
$db_pass = '6group';                     // データベースパスワード
$db_name = 'LAA1685019-kondatehausu';         // データベース名
 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
$password = htmlspecialchars($_POST['password'], ENT_QUOTES);
$email = htmlspecialchars($_POST['email'], ENT_QUOTES);

// ★ 半角英数字でなければエラー
if (!preg_match('/^[a-zA-Z0-9]+$/', $email)) {
    $_SESSION['error'] = "IDは半角英数字で入力してください。";
    header('Location: ./U20.php');
    exit();
}

    if ($ID === '' || $pass === '') {
     $_SESSION['error'] = "IDとパスワードを入力してください。";
        header('Location: ./U15ADMIN_LOGIN.php');
        exit();
    }

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
                   $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
 

  $stmt = $pdo->prepare("SELECT * FROM `system` WHERE BINARY `system_users_id` = ?");
        $stmt->execute([$ID]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $isValid = false;
        if ($user && !empty($user['system_users_password'])) {
            $stored_pass = trim($user['system_users_password']);
            if (password_verify($pass, $stored_pass) || $pass === $stored_pass) {
                $isValid = true;
            }
        }

        if ($isValid) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $ID;
        } else {
        $_SESSION['error'] = "ID またはパスワードが違います。";
        header('Location: ./U15ADMIN_LOGIN.php');
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

