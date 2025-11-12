<?php
session_start();
$error = "";
if($_Server["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? "";
    $familly_code = $_POST["family_code"] ?? "";
    if ($name && $family_code) {
        $_SESSION["name"] =$name;
        $_SESSION["family_code"] = $family_code;
        header("Location: home.php");
        exit;
    }else {
        $error = "すべての項目を入力してください。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
    <meta charset="UTF-8">
    <title>献立家　ログイン</title>
    <style>
        body {
            font-family: "Hiragino Sans", sans-serif;
            background-color: #f0f0e8;            
            align-items: center;
            display: flex;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #e6e0d0;
            border: 2px solid #333;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 4px 4px 10px rgba(0,0,0,0.2);
            width: 300px;
            text-align: center; 
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .switch {
            background-color: #3d3d3d;
            border-radius: 8px;
            padding: 20p;
            margin-bottom: 25px;
            position: relative;
        }
        
        .button {
            background-color: #444;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }
        .button:hover{
            background-color: #666;
        }
        label {
            display: block;
            text-align: left;
            font-size: 14px;
            margin-top: 10px;
        }
        input[type="text"]{
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #aaa;
            border-radius: 4px;
        }
        .error{
            color: red;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .link{
            margin-top: 14px;
            font-size: 15px;
        }
        .ling a:hover{
            text-decoration: underline;
        }
        .admin-link {
            font-size: 13px;
            margin-top: 20px;
            color: gray;
        }
        </style>
        </head>
        <body>
            <div clas="container">
                <div class="title">献立家</div>

                <div class="switch">
                    <from method="POST">
                        <?php if ($error): ?>
                        <p class="error"><?= htmlspecialchars($error) ?></p>
                 <?php endif; ?>
                 <input type="text" name="name" placeholder="名前を入力">
                <label>家族コードを入力</label>
                <input type="text" name="family_code" placeholder="家族コードを入力">
                <button type="submit" class="button">申請する</button>
                </from>
                </div>
                <div class="link">
                    <a href="U02MAKE.php">未登録の方はこちら</a>
                </div>

                <div class="admin-link">
                    <a href="U15ADMIN_LOGIN.php">管理者用ページ</a>
                </div>
        </body>
</html>