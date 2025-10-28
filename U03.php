<?php
// index.phpからURLパラメータ 'code' を取得
$family_code = $_GET['code'] ?? 'コードを取得できませんでした';
$display_code = strtoupper(htmlspecialchars($family_code));

// コードが取得できなかった場合は、エラーページなどにリダイレクトしても良い
if ($family_code === 'コードを取得できませんでした') {
    // 例: header("Location: index.php"); exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U03コード発行完了画面</title>
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
        .complete-title { font-size: 1.5em; margin-bottom: 20px; text-align: center; }
        .code-display { font-size: 3.5em; font-weight: bold; color: #000; margin-bottom: 5px; text-align: center; line-height: 1.2;}
        .note { font-size: 0.8em; color: #777; margin-bottom: 50px; text-align: center; }
        .login-link { 
            display: inline-block; 
            padding: 10px 20px; 
            background-color: #5cb85c; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="center-box">
            <p class="complete-title">あなたの家族コード</p>
            <div class="code-display"><?php echo $display_code; ?></div>
            <p class="note"># どこかに記録をしておいてください</p>
            <a href="U01.php" class="login-link">ログインに戻る</a>
        </div>
    </div>
</body>
</html>