<?php
// U23SYOUSAI.php: 料理詳細画面

// 1. データベース接続
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';
$db_pass = '6group';
$db_name = 'LAA1685019-kondatehausu';

$recipe = null;
$error_message = "";

// 2. URLパラメータからIDを取得
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // レシピ情報を取得
        $sql = "SELECT * FROM recipe WHERE recipe_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$recipe) {
            $error_message = "指定された料理が見つかりませんでした。";
        }

    } catch (PDOException $e) {
        $error_message = "データベースエラー: " . $e->getMessage();
    }
} else {
    $error_message = "無効なIDです。";
}

// デフォルト値の設定（データが空の場合の表示崩れ防止）
$title = $recipe['title'] ?? '不明な料理';
$content = $recipe['content'] ?? '説明がありません。';
$image_path = $recipe['image_path'] ?? '';
$time = $recipe['time'] ?? '-';
$cost = $recipe['cost'] ?? '-';
$share = ($recipe['share'] ?? 1) == 1 ? 'グローバル' : 'ローカル';

// 栄養価（未入力なら0を表示）
$calorie = $recipe['calorie'] ?? 0;
$carbohydrate = $recipe['carbohydrate'] ?? 0; // 炭水化物
$carbs = $recipe['carbs'] ?? 0;        // 糖質
$protein = $recipe['protein'] ?? 0;    // たんぱく質
$sugar = $recipe['sugar'] ?? 0;        // 糖分
$salt = $recipe['salt'] ?? 0;          // 塩分

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - 詳細</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; }
        
        /* タイトル下の青い下線 */
        .title-underline {
            position: relative;
            display: inline-block;
        }
        .title-underline::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 2px;
            width: 100%;
            height: 4px;
            background-color: #3b82f6; /* 青色 */
            border-radius: 2px;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen items-center py-6 px-4">

    <div class="w-full max-w-md">

        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 title-underline relative z-10">
                料理詳細
            </h1>
        </div>

        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php else: ?>

            <div class="bg-gray-200 rounded-md py-3 px-4 mb-6 text-center shadow-inner">
                <h2 class="text-lg text-gray-600 font-medium tracking-wide">
                    <?php echo htmlspecialchars($title); ?>
                </h2>
            </div>

            <div class="relative w-full aspect-[4/3] bg-gray-200 rounded-2xl overflow-hidden shadow-sm mb-2 group">
                <?php if ($image_path): ?>
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="料理画像" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="flex items-center justify-center h-full text-gray-400">No Image</div>
                <?php endif; ?>

                <div class="absolute bottom-3 right-3 flex space-x-3">
                    <button class="w-12 h-12 bg-white rounded-full shadow-md flex items-center justify-center text-gray-400 hover:text-red-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button class="w-12 h-12 bg-white rounded-full shadow-md flex items-center justify-center text-gray-400 hover:text-yellow-400 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-current" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex justify-between items-start px-2 mb-2 text-sm text-gray-700">
                <div class="text-left w-1/3">
                    <p class="text-xs font-bold mb-0.5">公開範囲</p>
                    <span class="bg-gray-200 text-gray-600 text-[10px] px-2 py-0.5 rounded">
                        <?php echo $share; ?>
                    </span>
                </div>
                <div class="text-center w-1/3 border-l border-r border-transparent">
                    <p class="text-[10px] text-gray-500 mb-0.5">🕒調理時間</p>
                    <p class="font-bold">約 <span class="text-lg"><?php echo htmlspecialchars($time); ?></span> 分</p>
                </div>
                <div class="text-right w-1/3">
                    <p class="text-[10px] text-gray-500 mb-0.5">💴費用目安</p>
                    <p class="font-bold">約 <span class="text-lg"><?php echo htmlspecialchars($cost); ?></span> 円</p>
                </div>
            </div>

            <div class="px-2 mb-6">
                <p class="text-sm text-gray-500 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($content)); ?>
                </p>
            </div>

            <div class="border border-gray-400 rounded-xl p-4 mb-6 relative">
                <div class="grid grid-cols-2 gap-y-3 gap-x-6 text-sm">
                    
                    <div class="flex items-center justify-between">
                        <span class="flex items-center text-gray-800 font-medium">
                            <span class="text-orange-500 mr-1">🔥</span>カロリー
                        </span>
                        <span class="text-gray-600">約<span class="bg-gray-200 px-1 ml-1 rounded text-gray-800 font-bold"><?php echo $calorie; ?></span>kcal</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center text-gray-800 font-medium">
                            <span class="text-yellow-700 mr-1">🍞</span>炭水化物
                        </span>
                        <span class="text-gray-600">約<span class="bg-gray-200 px-1 ml-1 rounded text-gray-800 font-bold"><?php echo $carbohydrate; ?></span>g</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center text-gray-800 font-medium">
                            <span class="text-red-500 mr-1">🍖</span>糖質
                        </span>
                        <span class="text-gray-600">約<span class="bg-gray-200 px-1 ml-1 rounded text-gray-800 font-bold"><?php echo $carbs; ?></span>g</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center text-gray-800 font-medium">
                            <span class="text-yellow-100 bg-yellow-400 rounded-full w-4 h-4 flex items-center justify-center mr-1 text-[10px]">🥚</span>たんぱく質
                        </span>
                        <span class="text-gray-600">約<span class="bg-gray-200 px-1 ml-1 rounded text-gray-800 font-bold"><?php echo $protein; ?></span>g</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="flex items-center text-gray-800 font-medium">
                            <span class="text-pink-400 mr-1">🍬</span>糖分
                        </span>
                        <span class="text-gray-600">約<span class="bg-gray-200 px-1 ml-1 rounded text-gray-800 font-bold"><?php echo $sugar; ?></span>g</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center text-gray-800 font-medium">
                            <span class="text-gray-400 mr-1">🧂</span>塩分
                        </span>
                        <span class="text-gray-600">約<span class="bg-gray-200 px-1 ml-1 rounded text-gray-800 font-bold"><?php echo $salt; ?></span>g</span>
                    </div>

                </div>
            </div>

            <div class="mb-10 relative">
                <h3 class="text-sm font-bold text-gray-800 mb-2">材料 【1人分】</h3>
                
                <div class="space-y-0">
                    <div class="border-b border-gray-300 h-8"></div>
                    <div class="border-b border-gray-300 h-8"></div>
                    <div class="border-b border-gray-300 h-8 relative">
                         <div class="absolute right-0 bottom-1 w-5 h-5 bg-gray-200 text-gray-500 rounded flex items-center justify-center text-xs font-bold cursor-pointer hover:bg-gray-300">
                            +
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <a href="U06HOME.php" class="block w-full">
            <button class="w-full bg-white border border-gray-200 text-black font-bold py-4 rounded shadow-lg hover:bg-gray-50 transition duration-200 text-xl tracking-wider">
                ホームに戻る
            </button>
        </a>

    </div>

</body>
</html>