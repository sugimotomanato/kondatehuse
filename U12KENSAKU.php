<?php
// U12KENSAKU.php - 献立検索結果画面（DB連携版）
session_start();

// 1. データベース接続設定 (U06HOME.phpから流用)
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';               
$db_pass = '6group';                   
$db_name = 'LAA1685019-kondatehausu';  

if ($_SERVER['SERVER_NAME'] === 'localhost') {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
}

// --- 2. 【Ajax POST リクエストの処理】 (お気に入り更新) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['recipe_id']) && isset($input['okini'])) {
            $recipe_id = $input['recipe_id'];
            $okini = $input['okini']; 

            $sql = "UPDATE recipe SET okini = :okini WHERE recipe_id = :recipe_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':okini', $okini, PDO::PARAM_INT);
            $stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'お気に入り状態を更新しました']);
        } else {
            echo json_encode(['success' => false, 'message' => 'データが不足しています (IDまたはokini値)']);
        }

    } catch (PDOException $e) {
        // DBエラーが発生した場合は、フロントエンドにエラーを返す
        echo json_encode(['success' => false, 'message' => 'DBエラー: ' . $e->getMessage()]);
    }
    exit; 
}


// --- 3. 状態の決定とGETパラメータの取得 ---
$view = $_GET['view'] ?? 'local';
$isGlobal = ($view === 'global');
$pageTitle = $isGlobal ? 'U13検索結果画面(グローバル)' : 'U12検索結果画面(ローカル)';

$localBtnClass = ($view === 'local') ? 'bg-primary-pink text-white' : 'bg-white text-zinc-800';
$globalBtnClass = ($view === 'global') ? 'bg-primary-pink text-white' : 'bg-white text-zinc-800';

// 検索・絞り込みパラメータの取得
$keyword = $_GET['keyword'] ?? '';
$calorieMax = $_GET['calorie'] ?? '';
$costMax = $_GET['cost'] ?? '';
$timeMax = $_GET['time'] ?? '';

$searchResults = [];
$error_message = '';
$fallbackImage = "https://placehold.co/600x400/f0f0f0/333?text=No+Image";


// --- 4. データベース検索処理 ---
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQLクエリの構築
    $sql = "SELECT recipe_id, title, image_path, okini, calorie, cost, time FROM recipe WHERE 1=1";
    $params = [];

    // ★★★ 修正箇所: 検索キーワードの処理 - descriptionカラムの参照を削除 ★★★
    if (!empty($keyword)) {
        // titleのみで検索を実行
        $sql .= " AND title LIKE :keyword";
        $params[':keyword'] = '%' . $keyword . '%';
    }
    // ★★★ 修正箇所 終了 ★★★

    // カロリーの絞り込み
    if (!empty($calorieMax)) {
        $sql .= " AND calorie <= :calorieMax";
        $params[':calorieMax'] = (int)$calorieMax;
    }

    // 費用の絞り込み
    if (!empty($costMax)) {
        $sql .= " AND cost <= :costMax";
        $params[':costMax'] = (int)$costMax;
    }

    // 時間の絞り込み
    if (!empty($timeMax)) {
        $sql .= " AND time <= :timeMax";
        $params[':timeMax'] = (int)$timeMax;
    }
    
    // 順序
    $sql .= " ORDER BY recipe_id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // データベースエラーが発生した場合、メッセージを保存
    $error_message = 'データベース接続エラー: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-pink': '#EC4899', 
                        'secondary-gray': '#D1D5DB', 
                        'accent-yellow': '#FFD700', // 星の色
                    },
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans JP', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', 'Noto Sans JP', sans-serif;
            background-color: #f0f0f0; 
        }
        .bg-pattern {
            background-image: url('haikei1.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -2;
        }
        .bg-overlay {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(8px); 
            -webkit-backdrop-filter: blur(8px);
            z-index: -1;
        }
        .main-container {
            position: relative;
            z-index: 1;
        }

        .meal-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 0.75rem; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
            overflow: hidden;
            border: 1px solid #E5E7EB; 
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 240px; 
        }
        .meal-card:hover {
            box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }
        .card-image-container {
            width: 100%;
            height: 60%; 
            background-size: cover;
            background-position: center;
            position: relative;
            background-color: #F3F4F6;
        }
    </style>
</head>
<body class="antialiased">

    <div class="bg-pattern"></div>
    <div class="bg-overlay"></div>

    <div class="main-container min-h-screen max-w-md mx-auto">
        
        <header class="p-4 flex justify-between items-center sticky top-0 bg-white/70 backdrop-blur-md z-10 border-b border-gray-200 shadow-sm">
            <h1 class="text-xl text-zinc-800 font-bold"><?php echo htmlspecialchars($pageTitle); ?></h1>
            <div class="flex space-x-2">
                 <a href="U06HOME.php" class="px-3 py-1 text-sm bg-primary-pink/10 border border-primary-pink rounded-full text-primary-pink hover:bg-primary-pink hover:text-white transition">ホーム</a>
            </div>
        </header>

        <div class="p-4 pt-2">
            <div class="grid grid-cols-2 gap-0 bg-white/70 backdrop-blur-sm p-1.5 rounded-full shadow-lg border border-gray-200">
                <?php 
                    $query_params = "keyword=" . urlencode($keyword) . 
                                    "&calorie=" . urlencode($calorieMax) . 
                                    "&cost=" . urlencode($costMax) . 
                                    "&time=" . urlencode($timeMax);
                ?>
                <a href="U12KENSAKU.php?view=global&<?php echo $query_params; ?>" class="py-2.5 px-4 text-center text-base font-bold rounded-full transition-all <?php echo $globalBtnClass; ?>">
                    グローバル
                </a>
                <a href="U12KENSAKU.php?view=local&<?php echo $query_params; ?>" class="py-2.5 px-4 text-center text-base font-bold rounded-full transition-all <?php echo $localBtnClass; ?>">
                    ローカル
                </a>
            </div>
        </div>

        <form method="GET" action="U12KENSAKU.php" class="px-4 pb-4 bg-white/70 backdrop-blur-sm shadow-md">
            <input type="hidden" name="view" value="<?php echo htmlspecialchars($view); ?>">
            
            <div class="mb-3 pt-4">
                <input type="text" name="keyword" placeholder="キーワード検索 (例: ハンバーグ)" 
                       value="<?php echo htmlspecialchars($keyword); ?>"
                       class="w-full p-3 border border-gray-300 rounded-lg shadow-inner focus:ring-primary-pink focus:border-primary-pink text-sm">
            </div>

            <div class="flex space-x-2 text-sm">
                <select name="calorie" class="flex-1 p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-pink focus:border-primary-pink">
                    <option value="">カロリー上限なし</option>
                    <option value="400" <?php if ($calorieMax == 400) echo 'selected'; ?>>～400kcal</option>
                    <option value="600" <?php if ($calorieMax == 600) echo 'selected'; ?>>～600kcal</option>
                    <option value="800" <?php if ($calorieMax == 800) echo 'selected'; ?>>～800kcal</option>
                    <option value="1000" <?php if ($calorieMax == 1000) echo 'selected'; ?>>～1,000kcal</option>
                </select>
                <select name="cost" class="flex-1 p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-pink focus:border-primary-pink">
                    <option value="">費用上限なし</option>
                    <option value="500" <?php if ($costMax == 500) echo 'selected'; ?>>～500円</option>
                    <option value="1000" <?php if ($costMax == 1000) echo 'selected'; ?>>～1,000円</option>
                    <option value="1500" <?php if ($costMax == 1500) echo 'selected'; ?>>～1,500円</option>
                    <option value="2000" <?php if ($costMax == 2000) echo 'selected'; ?>>～2,000円</option>
                </select>
                <select name="time" class="flex-1 p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-primary-pink focus:border-primary-pink">
                    <option value="">時間上限なし</option>
                    <option value="15" <?php if ($timeMax == 15) echo 'selected'; ?>>～15分</option>
                    <option value="30" <?php if ($timeMax == 30) echo 'selected'; ?>>～30分</option>
                    <option value="60" <?php if ($timeMax == 60) echo 'selected'; ?>>～60分</option>
                </select>
            </div>
            
            <button type="submit" class="w-full mt-3 py-2 bg-primary-pink text-white font-bold rounded-lg hover:bg-primary-pink/90 transition mb-4">
                献立を検索
            </button>
        </form>

        <main class="p-4 pt-0">
            <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200 mb-4">
                <p class="text-xs font-bold text-gray-700 mb-1">現在の検索条件:</p>
                <p class="text-sm text-gray-600">
                    キーワード: <span class="font-mono text-zinc-800"><?= !empty($keyword) ? htmlspecialchars($keyword) : 'なし' ?></span>
                    <br>
                    カロリー: <span class="font-mono text-zinc-800"><?= !empty($calorieMax) ? '～' . htmlspecialchars($calorieMax) . 'kcal' : '上限なし' ?></span>
                    / 費用: <span class="font-mono text-zinc-800"><?= !empty($costMax) ? '～' . htmlspecialchars($costMax) . '円' : '上限なし' ?></span>
                    / 時間: <span class="font-mono text-zinc-800"><?= !empty($timeMax) ? '～' . htmlspecialchars($timeMax) . '分' : '上限なし' ?></span>
                </p>
            </div>


            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-lg">
                    <?= $error_message ?>
                </div>
            <?php elseif (empty($searchResults)): ?>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <p class="text-gray-500 font-semibold">該当する献立は見つかりませんでした。</p>
                    <p class="text-sm text-gray-400 mt-1">検索条件を変更して再度お試しください。</p>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-600 mb-3"><?php echo count($searchResults); ?> 件の献立が見つかりました。</p>
                <div id="results-grid" class="grid grid-cols-2 gap-4">
                    <?php 
                    $rank = 1;
                    foreach($searchResults as $recipe): 
                        $recipe_id = htmlspecialchars($recipe['recipe_id'], ENT_QUOTES, 'UTF-8');
                        $title = htmlspecialchars($recipe['title'], ENT_QUOTES, 'UTF-8');
                        $imageUrl = !empty($recipe['image_path']) ? htmlspecialchars($recipe['image_path'], ENT_QUOTES, 'UTF-8') : $fallbackImage;
                        $okini = $recipe['okini'] ?? 0;
                        
                        $starColorClass = ($okini == 1) ? 'text-accent-yellow fill-current' : 'text-secondary-gray fill-none stroke-current';
                        $starDataValue = $okini;
                    ?>
                        <div class="meal-card" data-meal-id="<?= $recipe_id ?>" onclick="window.location.href='U24SYOUSAI.php?id=<?= $recipe_id ?>'">
                            <div class="card-image-container" style="background-image: url('<?= $imageUrl ?>'); background-size: cover; background-position: center;">
                                
                                <?php if ($isGlobal): ?>
                                    <div class="absolute top-2 left-2 bg-amber-400 text-zinc-900 text-sm font-bold px-3 py-1 rounded-full shadow">
                                        <?php echo $rank++; ?>位
                                    </div>
                                <?php endif; ?>

                                <div class="absolute top-2 right-2 p-1 rounded-full bg-white/70 backdrop-blur-sm shadow-md">
                                    <button class="star-button p-0.5 transition duration-150" data-okini="<?= $starDataValue ?>" onclick="event.stopPropagation(); toggleFavorite(this, <?= $recipe_id ?>);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?= $starColorClass ?>" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-zinc-800 text-base truncate"><?= $title ?></h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <?php 
                                    echo (!empty($recipe['calorie']) ? $recipe['calorie'] . 'kcal / ' : '') .
                                         (!empty($recipe['cost']) ? $recipe['cost'] . '円 / ' : '') .
                                         (!empty($recipe['time']) ? $recipe['time'] . '分' : '');
                                    ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <div id="message-box" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300" onclick="closeMessageBox()">
        <div class="bg-white p-6 rounded-xl shadow-2xl max-w-xs w-full text-center transform transition-transform duration-300" onclick="event.stopPropagation()">
            <p id="message-text" class="text-gray-800 font-semibold mb-4"></p>
            <button class="bg-primary-pink text-white px-4 py-2 rounded-lg font-bold hover:bg-primary-pink/80 transition duration-150" onclick="closeMessageBox()">OK</button>
        </div>
    </div>

    <script>
        const PRIMARY_PINK = '#EC4899';
        const SECONDARY_GRAY = '#D1D5DB';
        const ACCENT_YELLOW = '#FFD700';

        function showMessageBox(message) {
            const box = document.getElementById('message-box');
            document.getElementById('message-text').textContent = message;
            box.classList.remove('hidden');
            box.classList.add('flex');
        }
        function closeMessageBox() {
            const box = document.getElementById('message-box');
            box.classList.remove('flex');
            box.classList.add('hidden');
        }

        // お気に入り (星ボタン) の切り替え処理
        function toggleFavorite(button, recipeId) {
            const currentOkini = parseInt(button.getAttribute('data-okini'));
            const newOkiniValue = 1 - currentOkini; 
            const svg = button.querySelector('svg');

            // サーバーにデータを送る
            fetch(location.href, { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    recipe_id: recipeId, 
                    okini: newOkiniValue 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // UIの更新
                    button.setAttribute('data-okini', newOkiniValue);
                    
                    if (newOkiniValue === 1) {
                        svg.classList.remove('text-secondary-gray', 'fill-none', 'stroke-current');
                        svg.classList.add('text-accent-yellow', 'fill-current');
                        showMessageBox('お気に入りに追加しました！');
                    } else {
                        svg.classList.remove('text-accent-yellow', 'fill-current');
                        svg.classList.add('text-secondary-gray', 'fill-none', 'stroke-current');
                        showMessageBox('お気に入りから解除しました。');
                    }
                } else {
                    console.error('エラー:', data.message);
                    showMessageBox('更新に失敗しました (' + data.message + ')');
                }
            })
            .catch(error => {
                console.error('通信エラー:', error);
                showMessageBox('通信エラーが発生しました: ' + error.message);
            });
        }
    </script>
</body>
</html>