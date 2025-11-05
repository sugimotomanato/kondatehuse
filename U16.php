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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. データの受け取り
    $ID = $_POST['system_ID'] ?? ''; 
    $pass = $_POST['system_password'] ?? '';
    
    // NOT NULL制約のある必須項目にダミー値を設定（DB設計に基づく）
    // ★★★ 注意: 実際の運用ではユーザー入力やシステムでの正しい値生成が必要です ★★★
    $parent_account = $name;            
    $telephone_number = '000-0000-0000';
    $account_status = 1;                
    $favorites = 0;                     
    $hert = 0;                          

    // 2. 入力チェック
    if (empty($ID)  || !ctype_alnum($ID)) {
        $errors[] = "アカウントが間違っています";
         header('Location: ./U15.php');
            exit();
    }
    if (empty($pass) || strlen($pass) > 50 || !ctype_alnum($pass)) {
        $errors[] = "アカウントが間違っています";
         header('Location: ./U15.php');
            exit();
    }
    

    // 3. データベース操作
    if (empty($errors)) {
        try {
            // DB接続
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 管理者ログインのチェック
$stmt = $pdo->prepare("SELECT * FROM system WHERE system_users_id = ? AND system_users_password = ?");
$stmt->execute([$ID, $pass]);
if ($stmt->fetch()) {
    // ログイン成功 → 完了ページへ
    header('Location: complete.php');
    exit();
} else {
    // ログイン失敗 → ログイン画面へ戻る
    $errors[] = "このアカウントは間違っています";
    header('Location: ./U15.php');
    exit();
}


       } catch (PDOException $e) {
            // SQLエラーや接続エラーが発生した場合
            $errors[] = "データベースエラーが発生しました: " . $e->getMessage();
            header('Location: U15.php');
            exit();
            // データベース接続情報のエラーを特定しやすいように、デバッグ情報をログに出すことも推奨されます。
        }     
    }

    }


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
<a href="U17.php">ユーザ退会処理 ></a>

</body>
</html>