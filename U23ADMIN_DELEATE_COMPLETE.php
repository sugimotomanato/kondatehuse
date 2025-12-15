<?php
ob_start();
session_start();

// ==========================================================
// データベース接続設定
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';
$db_pass = '6group';
$db_name = 'LAA1685019-kondatehausu';

// POSTで削除リクエストが来たときだけ処理する
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // GET から ID 取得（安全処理）
    $id = $_POST['parent_account_ID'] ?? '';

    if ($id === '') {
        $_SESSION['error'] = "削除対象IDが指定されていません。";
        header('Location: ./U17ADMIN_DELEATE.php');
        exit();
    }

    try {
        // DB接続
        $pdo = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
            $db_user,
            $db_pass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ==========================================================
        // DELETE 実行
        // ==========================================================
        $stmt = $pdo->prepare("DELETE FROM `parent_account` WHERE BINARY `parent_account_ID` = ?");
        $stmt->execute([$id]);

        // 削除件数チェック
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "ID: {$id} のデータを削除しました。";
        } else {
            $_SESSION['error'] = "指定されたIDのデータが見つかりません。";
            header('Location: ./U17ADMIN_DELEATE.php');
        exit();
        }



    } catch (PDOException $e) {
        error_log("DBエラー: " . $e->getMessage());
        $_SESSION['error'] = "データベースエラーが発生しました。";
        header('Location: ./U17ADMIN_DELEATE.php');
        exit();
    }
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
    <title>ユーザー削除完了</title>
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
    <h1>削除完了</h1>
    <a href="U16ADMIN_HOME.php">戻る</a>
</body>
</html>
