<?php
// エラーメッセージを格納する配列
$errors = [];
$success_message = '';

// フォームがPOST送信された場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. データの受け取り
    $code = $_POST['family_code'] ?? ''; // 招待コードとして使用
    $confirm_code = $_POST['confirm_code'] ?? '';
    $name = $_POST['user_name'] ?? ''; // ユーザー名として使用
    
    // NOT NULL制約のある必須項目（ここではダミー値を設定）
    // 実際にはユーザー入力やシステムでの生成が必要です。
    $parent_account = $name;            // 親アカウント名 (VARCHAR(50) NN)
    $telephone_number = '000-0000-0000'; // 電話番号 (VARCHAR(255) NN)
    $account_status = 1;                // アカウント状態 (TINYINT NN)
    $favorites = 0;                     // おにいり (INT NN)
    $like = 0;                          // いいね (INT NN)

    // 2. 入力チェック
    if (empty($code) || strlen($code) < 6) {
        $errors[] = "家族コード（招待コード）は6文字以上の英数字で入力してください。";
    }
    if (empty($name) || strlen($name) > 50) {
        $errors[] = "名前を入力してください（50文字以内）。";
    }
    if ($code !== $confirm_code) {
        $errors[] = "家族コードが一致しません。";
    }

    // 3. データベース接続 (前回と同じ設定を使用)
    $db_host = 'locamysql320.phy.lolipop.lan';
    $db_user = 'rLAA1685019oot';
    $db_pass = 'passwo6grouprd'; 
    $db_name = 'LAA1685019';

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 4. データベースでの招待コード（password_hash）の一意性チェック
        if (empty($errors)) {
            // カラム名: password_hash
            $stmt = $pdo->prepare("SELECT password_hash FROM parent_account WHERE password_hash = ?");
            $stmt->execute([$code]);
            
            if ($stmt->fetch()) {
                $errors[] = "この家族コードは既に使用されています。別のコードを入力してください。";
            }
        }

        // 5. 登録処理
        if (empty($errors)) {
            // テーブル名: parent_account
            // カラム名: parent_account, telephone_number, password_hash, account_status, favorites, like, name
            $sql = "INSERT INTO parent_account (parent_account, telephone_number, password_hash, account_status, favorites, `like`, name) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $parent_account,      // 親アカウント名 (NN)
                $telephone_number,    // 電話番号 (NN)
                $code,                // 招待コードハッシュ (NN)
                $account_status,      // アカウント状態 (NN)
                $favorites,           // おにいり (NN)
                $like,                // いいね (NN)
                $name                 // 名前
            ]);
            
            $success_message = "家族コード「{$code}」の発行に成功し、親アカウントとして登録されました！";
        }

    } catch (PDOException $e) {
        // SQLエラー（例: データ型不一致、その他の制約違反）が発生した場合
        $errors[] = "データベースエラーが発生しました: " . $e->getMessage();
    }
}

// 登録後の処理（メッセージ表示など）
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>家族コード発行結果</title>
</head>
<body>
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <h2>登録エラー</h2>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="index.html">戻って修正する</a>
        </div>
    <?php elseif (!empty($success_message)): ?>
        <div style="color: green;">
            <h2>登録完了</h2>
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>
</body>
</html>