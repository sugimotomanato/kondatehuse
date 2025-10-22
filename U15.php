<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
          background-image: url('images/background.jpg');
          background-size: cover;      /* 画面全体にフィット */
          background-position: center; /* 中央に配置 */
          background-repeat: no-repeat;/* 繰り返さない */
        }
      </style>
</head>
<body>
<div style="text-align: center;">
    <img src="images/kondatehausu-1.png" alt="料理の写真" width="400" style="margin-top: 120px; margin-bottom: 120px;"><br>
    <form action="U16.php" method="post">
        <label for="">
            管理者ID
            <p><input type="text" name="ID" id=""></p>
            </label>
        <label for="">
            パスワード（半角英数8文字以上）
            <p><input type="password" name="passward" id=""></p> 
        </label>
        <button type="submit">ログイン</button>
    </form>

    <a href="U19.php">管理者登録はこちら</a>
</div>

</body>
</html>
