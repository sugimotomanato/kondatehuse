<?php
// U06HOME.php

// --------------------------------------------------------------------------
// 1. データベース接続 & データ取得
// --------------------------------------------------------------------------
$db_host = 'mysql320.phy.lolipop.lan'; 
$db_user = 'LAA1685019-kondatehausu'; 
$db_pass = '6group'; 
$db_name = 'LAA1685019'; 

$recipes = [];
$family_members = [];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. レシピ取得 (ダミーデータではなくDBから取得する場合)
    // $stmt = $pdo->query("SELECT * FROM recipe ORDER BY hert DESC LIMIT 5");
    // $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // エラーハンドリング (今回はデザイン確認用のためスルー)
}

// デザイン確認用ダミーデータ (DB接続できなくても動くようにしています)
if (empty($recipes)) {
    $recipes = [
        ['id' => 1, 'title' => 'ハンバーグ定食', 'image' => '', 'rank' => 1, 'likes' => 12, 'is_liked' => false, 'is_favorited' => false],
        ['id' => 2, 'title' => 'カレーライス', 'image' => '', 'rank' => 2, 'likes' => 8, 'is_liked' => true, 'is_favorited' => true],
        ['id' => 3, 'title' => 'オムライス', 'image' => '', 'rank' => 3, 'likes' => 24, 'is_liked' => false, 'is_favorited' => false],
        ['id' => 4, 'title' => '生姜焼き', 'image' => '', 'rank' => 4, 'likes' => 5, 'is_liked' => false, 'is_favorited' => false],
    ];
}

// 家族メンバーデータ (状態含む)
// アイコンは画像に合わせて「自分」「名前」などのラベル付き
$family_members = [
    ['name' => '自分', 'emoji' => '😷', 'is_self' => true, 'request' => 'ハンバーグ定食'],
    ['name' => '名前', 'emoji' => '🤕', 'is_self' => false, 'request' => ''],
    ['name' => '名前', 'emoji' => '😥', 'is_self' => false, 'request' => ''],
    ['name' => '名前', 'emoji' => '😩', 'is_self' => false, 'request' => ''],
    ['name' => '名前', 'emoji' => '😐', 'is_self' => false, 'request' => ''],
    ['name' => '名前', 'emoji' => '😆', 'is_self' => false, 'request' => ''],
];

// 遷移先URL設定
$url_calendar = 'U07.php'; 
$url_ranking  = 'U09.php';  
$url_fav      = 'U08.php';
$url_search   = 'U11SEARCH.php';   
$url_detail   = 'U04DELEATE.php';
$url_touroku  = 'U11TOUROKU.php'; // 今日の献立登録

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>

        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #f3f4f6;
            padding-bottom: 20px;
        }

        /* 背景のぼかしヘッダー画像 (ダミー) */
        .top-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 300px;
                        background-image: url('haikei1.jpg'); 
            background-size: cover;
            background-position: center;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0));
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));
            z-index: -1;
            opacity: 0.6;
            filter: blur(4px);
        }

        /* 横スクロールのスクロールバー非表示 */
        .hide-scroll::-webkit-scrollbar {
            display: none;
        }
        .hide-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* 今日の献立エリアの背景 (食卓) */
        .table-section {
            background-image: url('https://img.freepik.com/free-photo/white-table-and-chairs_1203-3453.jpg'); /* 食卓の画像 */
            background-size: cover;
            background-position: center;
            position: relative;
        }
        /* 白いフェードアウト */
        .table-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 40px;
            background: linear-gradient(to bottom, #f3f4f6, transparent);
            z-index: 1;
        }

        /* 吹き出しスタイル */
        .bubble {
            position: absolute;
            bottom: 85px; /* アイコンの上に配置 */
            left: 20px;
            background: white;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            white-space: nowrap;
            z-index: 10;
        }
        .bubble::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 15px;
            border-width: 6px 6px 0;
            border-style: solid;
            border-color: white transparent transparent transparent;
        }
    </style>
</head>
<body>

    <div class="top-bg"></div>

    <header class="flex justify-between items-center px-4 py-4 z-10 relative">
        <h1 class="text-3xl font-bold text-black drop-shadow-sm">ホーム</h1>
        <button id="menu-btn" class="text-gray-800 text-2xl">
            <i class="fa-solid fa-bars"></i>
        </button>
    </header>

    <main class="relative z-0">
        <div class="mb-4">
            <div class="flex items-center px-4 mb-2">
                <div class="relative inline-block">
                    <select class="appearance-none bg-transparent font-bold text-sm text-gray-800 pr-4 focus:outline-none">
                        <option>今日</option>
                        <option>週間</option>
                        <option>月間</option>
                    </select>
                    <i class="fa-solid fa-caret-down absolute right-0 top-1/2 -translate-y-1/2 text-xs text-white bg-gray-400 rounded-sm"></i>
                </div>
                <span class="text-sm font-bold text-gray-800 ml-1">の人気献立</span>
                <a href="<?php echo $url_ranking; ?>" class="ml-auto text-gray-500"><i class="fa-solid fa-chevron-right"></i></a>
            </div>

            <div class="flex overflow-x-auto px-4 space-x-3 hide-scroll pb-2">
                <?php foreach ($recipes as $index => $recipe): ?>
                    <div class="flex-none w-40 bg-white rounded-xl shadow-md overflow-hidden relative">
                        <div class="h-24 bg-gray-200 w-full">
                            </div>
                        
                        <div class="absolute top-2 right-2 bg-white rounded-full px-2 py-0.5 text-xs font-bold shadow-sm z-10">
                            <?php echo $recipe['rank']; ?>位
                        </div>

                        <button class="absolute top-9 right-2 w-7 h-7 bg-white rounded-full shadow-sm flex items-center justify-center z-10" onclick="toggleLike(this, <?php echo $recipe['id']; ?>)">
                            <i class="<?php echo $recipe['is_liked'] ? 'fa-solid text-red-500' : 'fa-regular text-gray-400'; ?> fa-heart text-sm"></i>
                        </button>

                        <div class="p-2 text-center">
                            <h3 class="text-sm font-bold text-gray-800 truncate"><?php echo $recipe['title']; ?></h3>
                            <a href="<?php echo $url_detail; ?>?id=<?php echo $recipe['id']; ?>" class="text-[10px] text-gray-400 block mt-1">レシピ詳細</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-4">
            <div class="flex items-center justify-between px-4 mb-2">
                <h2 class="text-sm font-bold text-gray-800">お気に入り</h2>
                <a href="<?php echo $url_fav; ?>" class="text-gray-500"><i class="fa-solid fa-chevron-right"></i></a>
            </div>

            <div class="flex overflow-x-auto px-4 space-x-3 hide-scroll pb-2">
                <?php foreach ($recipes as $recipe): ?>
                    <div class="flex-none w-40 bg-white rounded-xl shadow-md overflow-hidden relative">
                        <div class="h-24 bg-gray-200 w-full"></div>
                        
                        <div class="absolute top-2 right-2 bg-white rounded-full px-2 py-0.5 text-xs font-bold shadow-sm z-10">
                            3位
                        </div>

                        <button class="absolute top-9 right-2 w-7 h-7 bg-white rounded-full shadow-sm flex items-center justify-center z-10" onclick="toggleFavorite(this, <?php echo $recipe['id']; ?>)">
                            <i class="<?php echo $recipe['is_favorited'] ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-400'; ?> fa-star text-sm"></i>
                        </button>

                        <div class="p-2 text-center">
                            <h3 class="text-sm font-bold text-gray-800 truncate"><?php echo $recipe['title']; ?></h3>
                            <a href="<?php echo $url_detail; ?>?id=<?php echo $recipe['id']; ?>" class="text-[10px] text-gray-400 block mt-1">レシピ詳細</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-between px-4 mb-2">
                <h2 class="text-sm font-bold text-gray-800">カレンダー</h2>
                <a href="<?php echo $url_calendar; ?>" class="text-gray-500"><i class="fa-solid fa-chevron-right"></i></a>
            </div>

            <div class="flex overflow-x-auto px-4 space-x-3 hide-scroll pb-2">
                <div class="flex-none w-40 bg-white rounded-xl shadow-md overflow-hidden relative border-2 border-yellow-300">
                    <div class="h-24 bg-gray-200 w-full"></div>
                    
                    <div class="absolute top-2 right-2 bg-white rounded-full px-2 py-0.5 text-xs font-bold shadow-sm flex items-center gap-1">
                        2(火) <i class="fa-regular fa-lightbulb text-yellow-400 text-[10px]"></i>
                    </div>

                    <div class="p-2 text-center">
                        <h3 class="text-sm font-bold text-gray-800 truncate">ハンバーグ定食</h3>
                        <a href="<?php echo $url_detail; ?>" class="text-[10px] text-gray-400 block mt-1">レシピ詳細</a>
                    </div>
                </div>

                 <div class="flex-none w-40 bg-white rounded-xl shadow-md overflow-hidden relative">
                    <div class="h-24 bg-gray-200 w-full"></div>
                    <div class="absolute top-2 right-2 bg-white rounded-full px-2 py-0.5 text-xs font-bold shadow-sm">
                        1(月)
                    </div>
                    <div class="p-2 text-center">
                        <h3 class="text-sm font-bold text-gray-800 truncate">ハンバーグ定食</h3>
                        <a href="<?php echo $url_detail; ?>" class="text-[10px] text-gray-400 block mt-1">レシピ詳細</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative w-full h-[400px] table-section flex flex-col items-center">
            
            <h2 class="text-center font-bold text-gray-800 mt-4 relative z-10">今日の献立</h2>
            
            <a href="<?php echo $url_touroku; ?>" class="mt-8 w-16 h-12 bg-white/60 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg relative z-10 hover:bg-white/80 transition">
                <i class="fa-solid fa-plus text-2xl text-white drop-shadow-md"></i>
            </a>

            <div class="absolute bottom-0 w-full pb-4 bg-gradient-to-t from-white via-white/80 to-transparent pt-10">
                
                <?php foreach ($family_members as $member): ?>
                    <?php if ($member['is_self'] && !empty($member['request'])): ?>
                        <div class="bubble">
                            <?php echo $member['request']; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="flex overflow-x-auto px-4 space-x-4 hide-scroll mb-4 items-end">
                    <?php foreach ($family_members as $member): ?>
                        <div class="flex flex-col items-center flex-none">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center text-3xl shadow-md border-2 
                                <?php echo $member['is_self'] ? 'bg-orange-100 border-orange-400' : 'bg-gray-100 border-gray-300'; ?>">
                                <?php echo $member['emoji']; ?>
                            </div>
                            <span class="text-xs text-gray-600 font-bold mt-1"><?php echo $member['name']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="px-4">
                    <form action="<?php echo $url_search; ?>" method="GET" class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="q" placeholder="検索" 
                            class="w-full bg-gray-200/80 backdrop-blur-sm rounded-full py-2 pl-10 pr-4 text-sm focus:outline-none focus:bg-white shadow-inner">
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleLike(btn, id) {
            const icon = btn.querySelector('i');
            if (icon.classList.contains('fa-solid')) {
                icon.classList.remove('fa-solid', 'text-red-500');
                icon.classList.add('fa-regular', 'text-gray-400');
            } else {
                icon.classList.remove('fa-regular', 'text-gray-400');
                icon.classList.add('fa-solid', 'text-red-500');
            }
            // ここにAjax処理を追加
        }

        function toggleFavorite(btn, id) {
            const icon = btn.querySelector('i');
            if (icon.classList.contains('fa-solid')) {
                icon.classList.remove('fa-solid', 'text-yellow-400');
                icon.classList.add('fa-regular', 'text-gray-400');
            } else {
                icon.classList.remove('fa-regular', 'text-gray-400');
                icon.classList.add('fa-solid', 'text-yellow-400');
            }
             // ここにAjax処理を追加
        }
    </script>
</body>
</html>