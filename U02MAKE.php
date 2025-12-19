<?php
// U02MAKE.php

// エラーメッセージを初期化
$errors = [];
$code = '';
$name = '';
$complete_page = 'U03MAKE_LAST.php'; 

// ==========================================================
// データベース接続設定
// ★★★ 修正箇所: ユーザー名とデータベース名が管理画面の情報と一致していることを確認 ★★★
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019'; // ★正：LAA1685019
$db_pass = '6group'; // ★正：6group
$db_name = 'LAA1685019-kondatehausu'; // ★正：LAA1685019-kondatehausu
// ==========================================================

// フォームがPOST送信された場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. データの受け取り
    $code = $_POST['parent_account'] ?? ''; // 家族コード（フォームフィールド名）
    $confirm_code = $_POST['confirm_parent_account'] ?? ''; // 家族コード（再入力）
    $name = $_POST['user_name'] ?? ''; // 名前
    
    // NOT NULL制約のある必須項目にダミー値を設定 (DB構造 image_8cd527.png に基づく)
    $account_status = 1;
    $favorites = 0; 
    $hert = 0;
    $icon_path = 'default_icon.png';
    
    // 2. 入力チェック (既存のロジックは維持)
    if (empty($code) || strlen($code) < 6 || !ctype_alnum($code)) {
        $errors[] = "家族コードは6文字以上の英数字で入力してください。";
    }
    if (empty($name) || strlen($name) > 50) {
        $errors[] = "名前を入力してくださ(50文字以内)。";
    }
    if ($code !== $confirm_code) {
        $errors[] = "家族コードが一致しません。";
    }

    // 3. データベース操作
    if (empty($errors)) {
        try {
            // DB接続
            // PDO接続文字列が $db_user と $db_pass を使用していることを確認
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 3-1. 家族コードの一意性チェック
            $stmt = $pdo->prepare("SELECT `family_code` FROM `parent_account` WHERE `family_code` = ?");
            $stmt->execute([$code]);
            if ($stmt->fetch()) {
                $errors[] = "この家族コードは既に使用されています。別のコードを入力してください。";
            }

            // 3-2. 登録処理 (テーブル名: parent_account)
            if (empty($errors)) {
                $sql = "INSERT INTO `parent_account` (`family_code`, `account_status`, `favorites`, `hert`, `user_name`, `icon`) 
                         VALUES (?, ?, ?, ?, ?, ?)"; 
                
                $stmt = $pdo->prepare($sql);
                
                $stmt->execute([
                    $code,          // 1. family_code に家族コード
                    $account_status,// 2. account_status
                    $favorites,     // 3. favorites
                    $hert,          // 4. hert
                    $name,          // 5. user_name に名前
                    $icon_path      // 6. icon
                ]);
                
                // 4. 登録成功！完了画面へリダイレクト
                header("Location: {$complete_page}?code=" . urlencode($code));
                exit; 
            }

        } catch (PDOException $e) {
            // 接続エラーまたはSQLエラー
            $errors[] = "データベースエラーが発生しました: " . htmlspecialchars($e->getMessage());
            error_log("DBエラー in U02MAKE: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>家族コード発行</title>
    <style>
        /* スタイルシート省略 */
        body { 
            font-family: sans-serif; 
            text-align: center; 
            background-color: #f0f0f0; 
            background-image: url('haikei2.jpg'); 
            background-repeat: repeat;
            background-size: cover; 
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .center-box { 
            width: 90%; 
            max-width: 400px; 
            padding: 30px; 
            background-color: white; 
            border-radius: 10px; 
            box-shadow: 0 0 20px rgba(0,0,0,0.2); 
            text-align: left;
        }
        .form-title { margin-top: 0; font-size: 1.8em; font-weight: bold; text-align: center; }
        label { display: block; margin-top: 15px; font-size: 0.9em; color: #555; }
        input[type="text"] { 
            width: 95%; 
            padding: 10px; 
            margin-top: 5px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            font-size: 1.1em;
        }
        .submit-container { text-align: right; margin-top: 30px; }
        button[type="submit"] { 
            background: none; 
            border: none; 
            font-size: 2.5em; 
            cursor: pointer;
            color: #5cb85c;
            padding: 0;
            line-height: 1;
        }
        .error-list { color: red; text-align: left; list-style-type: disc; padding-left: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="center-box">
            <h1 class="form-title">家族コードを発行する</h1>

            <?php if (!empty($errors)): ?>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post" action="U02MAKE.php">
                <label for="parent_account">家族コードを作成  6文字英数以上</label>
                <input type="text" id="parent_account" name="parent_account" 
                        value="<?php echo htmlspecialchars($code); ?>" 
                        placeholder="コード作成入力" required minlength="6" pattern="[a-zA-Z0-9]+">

                <label for="confirm_parent_account">家族コードの再入力</label>
                <input type="text" id="confirm_parent_account" name="confirm_parent_account" 
                        placeholder="コード再入力" required minlength="6" pattern="[a-zA-Z0-9]+">
                
                <label for="user_name">名前</label>
                <input type="text" id="user_name" name="user_name" 
                        value="<?php echo htmlspecialchars($name); ?>" 
                        placeholder="名前入力" required maxlength="50">
                
                <div class="submit-container">
                    <button type="submit">→ </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>