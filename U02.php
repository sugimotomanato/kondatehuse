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

// フォームがPOST送信された場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. データの受け取り
    $code = $_POST['family_code'] ?? ''; 
    $confirm_code = $_POST['confirm_code'] ?? '';
    $name = $_POST['user_name'] ?? ''; 
    
    // NOT NULL制約のある必須項目にダミー値を設定（DB設計に基づく）
    // ★★★ 注意: 実際の運用ではユーザー入力やシステムでの正しい値生成が必要です ★★★
    $parent_account = $name;            
    $telephone_number = '000-0000-0000';
    $account_status = 1;                
    $favorites = 0;                     
    $hert = 0;                          

    // 2. 入力チェック
    if (empty($code) || strlen($code) < 6 || !ctype_alnum($code)) {
        $errors[] = "家族コードは6文字以上の英数字で入力してください。";
    }
    if (empty($name) || strlen($name) > 50) {
        $errors[] = "名前を入力してください（50文字以内）。";
    }
    if ($code !== $confirm_code) {
        $errors[] = "家族コードが一致しません。";
    }

    // 3. データベース操作
    if (empty($errors)) {
        try {
            // DB接続
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 3-1. 招待コード（password_hash）の一意性チェック
            $stmt = $pdo->prepare("SELECT password_hash FROM parent_account WHERE password_hash = ?");
            $stmt->execute([$code]);
            if ($stmt->fetch()) {
                $errors[] = "この家族コードは既に使用されています。別のコードを入力してください。";
            }

            // 3-2. 登録処理 (テーブル名: parent_account)
            if (empty($errors)) {
                $sql = "INSERT INTO parent_account (parent_account, telephone_number, password_hash, account_status, favorites, hert, name) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $parent_account, $telephone_number, $code, 
                    $account_status, $favorites, $hert, $name
                ]);
                
                // 4. 登録成功！完了画面へリダイレクト
                header("Location: {$complete_page}?code=" . urlencode($code));
                exit; 
            }

        } catch (PDOException $e) {
            // SQLエラーや接続エラーが発生した場合
            $errors[] = "データベースエラーが発生しました: " . $e->getMessage();
            // データベース接続情報のエラーを特定しやすいように、デバッグ情報をログに出すことも推奨されます。
        }
    }
}
// ==========================================================
// HTML出力開始
// ==========================================================
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>家族コード発行</title>
    <style>
        /* CSSは前回のコードと同じため省略します */
        body { 
            font-family: sans-serif; 
            text-align: center; 
            background-color: #f0f0f0; 
            background-image: url('background.jpg'); 
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

            <form method="post" action="index.php">
                <label for="family_code">家族コードを作成（6文字英数以上）</label>
                <input type="text" id="family_code" name="family_code" 
                       value="<?php echo htmlspecialchars($code); ?>" 
                       placeholder="0123456789" required minlength="6" pattern="[a-zA-Z0-9]+">

                <label for="confirm_code">家族コードの再入力</label>
                <input type="text" id="confirm_code" name="confirm_code" 
                       placeholder="0123456789" required minlength="6" pattern="[a-zA-Z0-9]+">
                
                <label for="user_name">名前</label>
                <input type="text" id="user_name" name="user_name" 
                       value="<?php echo htmlspecialchars($name); ?>" 
                       placeholder="名前を入力" required maxlength="50">
                
                <div class="submit-container">
                    <button type="submit">→ </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>