
<?php
// エラーメッセージを初期化
$errors = [];
$code = '';
$confirm_code = '';

// ==========================================================
// データベース接続設定 (ロリポップ情報を使用)
// ==========================================================
// ※ XAMPP環境で試す場合は、必ずロリポップ側で外部接続を許可してください。
//    許可できない場合は、手順2の代替案を参照し、ローカルDB情報に一時的に変更してください。
$db_host = 'mysql320.phy.lolipop.lan'; //
$db_user = 'LAA1685019-kondatehausu'; //
$db_pass = '6group'; //
$db_name = 'LAA1685019'; //

$delete_complete_page = 'U05x.php'; 

// フォームがPOST送信された場合の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. データの受け取り
    $code = $_POST['family_code'] ?? ''; 
    $confirm_code = $_POST['confirm_code'] ?? '';
    
    // 2. 入力チェック
    
    // 家族コードは6文字以上の英数字
    if (empty($code) || strlen($code) < 6 || !ctype_alnum($code)) {
        $errors[] = "家族コードは6文字以上の英数字で入力してください。";
    }
    
    // 家族コードの一致チェック
    if ($code !== $confirm_code) {
        $errors[] = "家族コードが一致しません。";
    }

    // 3. 全てのチェックを通過した場合、DB操作へ
    if (empty($errors)) {
        try {
            // DB接続
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 3-1. 家族コード（password_hash）の存在チェックと削除
            
            // 削除対象のレコード数を取得
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM parent_account WHERE password_hash = ?");
            $stmt->execute([$code]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                // レコードが存在する場合、削除を実行
                $sql = "DELETE FROM parent_account WHERE password_hash = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$code]);
                
                // 4. 削除成功！完了画面へリダイレクト
                header("Location: U05.php");
                exit; 
            } else {
                // コードが存在しない場合
                $errors[] = "入力された家族コードが見つかりませんでした。";
            }

        } catch (PDOException $e) {
            // SQLエラーや接続エラーが発生した場合
            $errors[] = "データベースエラーが発生しました: " . $e->getMessage();
            // 接続情報の問題が考えられる場合は、ローカルDBに変更してみてください。
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
    <title>アカウント削除</title>
    <style>
        /* CSSは、画像のデザインを再現するため、以前のコードに基づき作成します */
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
        label { display: block; margin-top: 15px; font-size: 0.9em; color: #555; text-align: center;}
        input[type="text"] { 
            width: 90%; 
            padding: 10px; 
            margin-top: 5px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            font-size: 1.1em;
            text-align: center;
            margin-left: 5%;
        }
        .submit-container { text-align: center; margin-top: 30px; }
        button[type="submit"] { 
            /* 画像の「確定」ボタンのスタイル */
            background-color: #70d870; /* 明るい緑 */
            color: white;
            border: none;
            padding: 10px 40px;
            font-size: 1.1em;
            border-radius: 20px;
            cursor: pointer;
            box-shadow: 0 4px #4caf50;
            transition: all 0.1s;
        }
        button[type="submit"]:active {
            box-shadow: 0 0 #4caf50;
            transform: translateY(4px);
        }
        .error-list { color: red; text-align: center; list-style-type: none; padding-left: 0; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="center-box">
            <h1 class="form-title">削除に必要な情報を入力してください</h1>
            
            <?php if (!empty($errors)): ?>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post" action="U05.php">
                
                <label for="family_code">家族コードの入力（6文字英数以上）</label>
                <input type="text" id="family_code" name="family_code" 
                       value="<?php echo htmlspecialchars($code); ?>" 
                       placeholder="0123456789" required minlength="6" pattern="[a-zA-Z0-9]+">

                <label for="confirm_code">家族コードの再入力</label>
                <input type="text" id="confirm_code" name="confirm_code" 
                       value="<?php echo htmlspecialchars($confirm_code); ?>"
                       placeholder="0123456789" required minlength="6" pattern="[a-zA-Z0-9]+">
                
                <div class="submit-container">
                    <button type="submit">確定</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

