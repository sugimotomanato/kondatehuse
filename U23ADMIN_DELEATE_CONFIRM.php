<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

$id=trim($_POST['parent_account_ID'] ?? '');


}else{
 error_log("無効アクセス: " . $e->getMessage());
        $_SESSION['error'] = "無効なアクセスです";
        header('Location: ./U15ADMIN_LOGIN.php');
        exit();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー削除確認</title>
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
    <h1>ほんとうに削除しますか？</h1>
    <form action="U23ADMIN_DELEATE_COMPLETE.php" method="post" style="display:inline;">
         <input type="hidden" name="parent_account_ID" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" name="action" value="delete">はい</button>
    </form>

    <form action="U17ADMIN_DELEATE.php" method="get" style="display:inline;">
        <button type="submit">いいえ</button>
    </form>
</body>
</html>

