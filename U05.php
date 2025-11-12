
<?php
// delete_account.php から POST で家族コードを受け取る
$code = $_POST['family_code'] ?? '';
$confirm_code = $_POST['confirm_code'] ?? '';

// 不正なアクセスやコード未入力の場合は、入力画面に戻す
if (empty($code) || $code !== $confirm_code) {
    header("Location: U04.php");
    exit;
}

// フォームで渡すための家族コードをHTMLエンティティに変換
$safe_code = htmlspecialchars($code);
$safe_confirm_code = htmlspecialchars($confirm_code);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>退会最終確認</title>
    <style>
        /* CSSは、画像のデザインを再現するため、以前のコードに基づき作成します */
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
            text-align: center; /* テキスト中央揃え */
        }
        .confirm-message { 
            font-size: 1.8em; /* 画像に合わせたサイズ */
            font-weight: bold; 
            margin: 30px 0;
            line-height: 1.5;
        }
        .button-group { 
            margin-top: 40px; 
        }
        .button-style {
            display: block;
            width: 80%;
            margin: 15px auto;
            padding: 15px 10px;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none; /* aタグの場合 */
            border: none;
        }
        .btn-ok { 
            /* 「大丈夫です」ボタン（灰色） */
            background-color: #a0a0a0; 
            color: white;
            box-shadow: 0 4px #8a8a8a;
        }
        .btn-cancel { 
            /* 「やめておく」ボタン（赤色） */
            background-color: #e00000; 
            color: white;
            box-shadow: 0 4px #c00000;
        }
        .btn-style:active {
            box-shadow: none;
            transform: translateY(4px);
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="center-box">
            <p class="confirm-message">
                あなたが退会を進めると<br>
                メンバー全員が<br>
                使用することが<br>
                できなくなります<br>
                がよろしいですか？
            </p>
            
            <div class="button-group">
                <form method="post" action="U01.php">
                    <input type="hidden" name="family_code" value="<?php echo $safe_code; ?>">
                    <input type="hidden" name="confirm_code" value="<?php echo $safe_confirm_code; ?>">
                    <button type="submit" class="button-style btn-ok">削除する</button>
                </form>

                <a href="U01.php" class="button-style btn-cancel">やめておく</a>
            </div>
        </div>
    </div>
</body>
</html>

