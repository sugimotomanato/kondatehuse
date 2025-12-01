<?php
$errors = [];
$code = '';
$name = '';
$complete_page = 'complete.php'; 
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019-kondatehausu'; 
$db_pass = '6group'; 
$db_name = 'LAA1685019';
?>
<div class="title">料理詳細</div>

<input type="text" class="text-input" placeholder="料理名">

<div class="preview-img-box">
    <img src="/mnt/data/c6066667-22d7-402e-937b-012ad536bc00.png" alt="料理画像">
    <div class="action-btns">
        <button class="icon-btn">❤️</button>
    </div>
</div>

<div class="info-area">
    <div>
        公開範囲<br>一般
    </div>
    <div>
        調理時間<br>約 ○ 分
    </div>
    <div>
        費用目安<br>約 ○ 円
    </div>
</div>

<div class="desc">ハンバーグ定食です。</div>

<div class="nutrition-card">
    <div class="nut-row">🔥 カロリー： 約 ○ kcal</div>
    <div class="nut-row">🍞 糖質： 約 ○ g</div>
    <div class="nut-row">🧂 脂質： 約 ○ g</div>
    <div class="nut-row">🍚 炭水化物： 約 ○ g</div>
    <div class="nut-row">🥩 たんぱく質： 約 ○ g</div>
    <div class="nut-row">🧂 塩分： 約 ○ g</div>
</div>

<div class="ingredients-title">材料（1人分）</div>
<input class="ingredient-input" placeholder="例：牛ひき肉 100g">
<input class="ingredient-input" placeholder="例：玉ねぎ 1/4個">

<button class="home-btn">ホームに戻る</button>