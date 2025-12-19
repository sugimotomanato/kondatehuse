<?php
// U06HOME.php
session_start();

// -----------------------------------------------------------
// 1. 【ログイン/アクセス権限チェックと変数初期化】
// -----------------------------------------------------------
$is_parent = $_SESSION["is_parent"] ?? false;
$is_applicant = $_SESSION["is_applicant"] ?? false; 

// 親アカウントでも、申請中で一時的にアクセスしている状態でもなければ、ログイン画面へ戻す
if (!$is_parent && !$is_applicant) {
    // 申請機能付きのログイン画面へリダイレクト
    header("Location: U01LOGIN.php"); 
    exit;
}

// ユーザー情報の取得（申請中ユーザーも含む）
$user_name_display = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : 'ゲスト';
$my_family_code = $_SESSION["family_code"] ?? ''; // セッションから家族コードを取得

// フラッシュメッセージの処理
$flash_message = "";
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}


// -----------------------------------------------------------
// 2. データベース接続設定
// -----------------------------------------------------------
$db_host = 'mysql320.phy.lolipop.lan';
$db_user = 'LAA1685019';               
$db_pass = '6group';                   
$db_name = 'LAA1685019-kondatehausu';  

if ($_SERVER['SERVER_NAME'] === 'localhost') {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
}

$pdo = null;
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // 接続エラー時はAjax応答または通常のページエラー
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(['success' => false, 'message' => 'DB接続エラー: ' . $e->getMessage()]);
        exit;
    }
    // 通常の表示の場合はエラーメッセージを設定し、処理を続行（データは取得できない）
    $flash_message = "データベース接続に失敗しました。";
}


// -----------------------------------------------------------
// 3. 【Ajax POST リクエストの処理】(お気に入り更新 ＆ 申請処理)
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);

    try {
        // A. お気に入り更新処理 (既存機能)
        if (isset($input['recipe_id']) && isset($input['okini'])) {
            $recipe_id = $input['recipe_id'];
            $okini = $input['okini']; 
            $sql = "UPDATE recipe SET okini = :okini WHERE recipe_id = :recipe_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':okini', $okini, PDO::PARAM_INT);
            $stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'お気に入り状態を更新しました']);
            exit;
        }

        // B. ★申請の承認・拒否処理 (ここが承認ロジックの中心)★
        if (isset($input['action']) && $input['action'] === 'process_application') {
            
            // 親アカウント以外は操作不可
            if (!$is_parent) {
                echo json_encode(['success' => false, 'message' => '権限がありません']);
                exit;
            }

            $app_id = $input['application_id'];
            $decision = $input['decision']; // 'approve' or 'reject'

            // 申請情報を取得 (family_codeが自分のグループかどうかもチェックすべきだが、今回はIDのみで)
            $stmt_app = $pdo->prepare("SELECT * FROM applications WHERE application_ID = :id AND status = 0");
            $stmt_app->bindValue(':id', $app_id, PDO::PARAM_INT);
            $stmt_app->execute();
            $application = $stmt_app->fetch(PDO::FETCH_ASSOC);

            if (!$application) {
                echo json_encode(['success' => false, 'message' => '申請が見つからないか処理済みです']);
                exit;
            }

            $pdo->beginTransaction();

            if ($decision === 'approve') {
                // 1. 子アカウントテーブルに登録 (child_account_nameはapplicant_nameと同じ値を使用)
                $sql_child = "INSERT INTO child_account 
                    (`child_account_name`, `name`, `account_status`, `telephone number`, `favorites_ID`, `hert`, `icon`) 
                    VALUES (:c_name, :name, 1, '', 0, 0, '')";
                
                $stmt_child = $pdo->prepare($sql_child);
                $stmt_child->bindValue(':c_name', $application['applicant_name'], PDO::PARAM_STR);
                $stmt_child->bindValue(':name', $application['applicant_name'], PDO::PARAM_STR);
                $stmt_child->execute();

                // 2. 申請ステータスを承認(1)に変更
                $stmt_update = $pdo->prepare("UPDATE applications SET status = 1 WHERE application_ID = :id");
                $stmt_update->bindValue(':id', $app_id, PDO::PARAM_INT);
                $stmt_update->execute();

                $msg = "承認しました。アカウントが作成されました。";

            } elseif ($decision === 'reject') {
                // 拒否(2)に変更
                $stmt_update = $pdo->prepare("UPDATE applications SET status = 2 WHERE application_ID = :id");
                $stmt_update->bindValue(':id', $app_id, PDO::PARAM_INT);
                $stmt_update->execute();

                $msg = "申請を拒否しました。";
            } else {
                 throw new Exception("無効なアクション");
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => $msg]);
            exit;
        }

    } catch (Exception $e) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Approval Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => '処理中にエラーが発生しました']);
        exit;
    }
}

// -----------------------------------------------------------
// 4. 【通常のページ表示 (GETリクエスト) の処理】
// -----------------------------------------------------------
$latest_title = "【登録】"; 
$latest_image = "teisyoku/sake.jpg"; 
$latest_id = 7; 
$latest_okini = 0;
$favorite_recipes = [];
$pending_applications = []; // ★申請リスト初期化

if ($pdo) {
    try {
        // 4-1. 最新レシピ取得
        $stmt_latest = $pdo->query("SELECT recipe_id, title, image_path, okini FROM recipe ORDER BY recipe_id DESC LIMIT 1");
        $row_latest = $stmt_latest->fetch(PDO::FETCH_ASSOC);
        if ($row_latest) {
            $latest_title = "【登録】" . htmlspecialchars($row_latest['title'], ENT_QUOTES, 'UTF-8');
            $latest_id = htmlspecialchars($row_latest['recipe_id'], ENT_QUOTES, 'UTF-8');
            $latest_okini = isset($row_latest['okini']) ? $row_latest['okini'] : 0; 
            if (!empty($row_latest['image_path'])) $latest_image = htmlspecialchars($row_latest['image_path'], ENT_QUOTES, 'UTF-8');
        }

        // 4-2. お気に入り取得
        $stmt_favorites = $pdo->query("SELECT recipe_id, title, image_path, okini FROM recipe WHERE okini = 1 ORDER BY recipe_id DESC");
        $favorite_recipes = $stmt_favorites->fetchAll(PDO::FETCH_ASSOC);

        // 4-3. ★未承認の申請を取得 (status = 0) - 親アカウントの場合のみ実行★
        if ($is_parent && $my_family_code) {
            $sql_apps = "SELECT application_ID, applicant_name FROM applications WHERE family_code = :code AND status = 0";
            $stmt_apps = $pdo->prepare($sql_apps);
            $stmt_apps->bindValue(':code', $my_family_code, PDO::PARAM_STR);
            $stmt_apps->execute();
            $pending_applications = $stmt_apps->fetchAll(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {
        // DBエラーハンドリング (表示用)
        $flash_message = "データ取得中にエラーが発生しました。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>献立ホーム画面</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-pink': '#EC4899', 
                        'secondary-gray': '#D1D5DB', 
                        'accent-yellow': '#FFD700',
                        'light-bg': '#F9FAFB', 
                        'card-border': '#E5E7EB', 
                        'notify-red': '#EF4444', 
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        body, html { height: 100%; }
        .main-content {
            padding-bottom: 200px; 
            min-height: calc(100vh - 72px);
            background-image: url('haikei1.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: scroll; 
            background-color: transparent; 
        }
        .meal-card {
            width: 240px; height: 160px; border-radius: 1rem; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
            overflow: hidden; border: 1px solid #E5E7EB; 
            background-color: rgba(255, 255, 255, 0.7); cursor: pointer;
        }
        .drawer {
            transition: transform 0.3s ease-out;
            transform: translateX(100%);
            width: 80%; 
        }
        .drawer.is-open { transform: translateX(0); }
        .notification-bell { position: relative; }
        .notification-bell.has-notification::after {
            content: ''; position: absolute; top: 4px; right: 4px;
            width: 8px; height: 8px; background-color: #EF4444; 
            border-radius: 50%; border: 1px solid white; 
        }
        .user-icon-container { background-size: cover; background-position: center; background-repeat: no-repeat; }
        .date-picker-menu { position: absolute; top: 100%; left: 0; z-index: 10; min-width: 120px; }
        @keyframes popUp {
            0% { opacity: 0; transform: translate(-50%, 10px) scale(0.8); }
            100% { opacity: 1; transform: translate(-50%, 0) scale(1); }
        }
        .speech-bubble { animation: popUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; z-index: 100; }
    </style>
</head>
<body class="bg-light-bg font-sans">

    <?php if (!empty($flash_message)): ?>
        <div id="flash-message" class="fixed top-0 left-0 right-0 z-50 p-4 text-center text-white font-semibold bg-primary-pink shadow-lg">
            <?php echo htmlspecialchars($flash_message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="main-content max-w-md mx-auto shadow-lg overflow-x-hidden relative">

        <header class="p-4 flex flex-row justify-between items-center sticky top-0 z-10 border-b border-gray-100 header-bg" style="background-color: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">ホーム</h1>
            <button id="menu-button" class="p-2 text-gray-600 hover:text-gray-800 rounded-full transition duration-150 ui-element-bg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>
        
        <div class="p-4 space-y-6 ui-element-bg">
            
             <section>
                <div class="flex items-center space-x-1 mb-3">
                    <h2 id="date-picker-trigger" class="text-xl font-bold text-primary-pink cursor-pointer relative">
                        今日 <span class="text-sm text-gray-700 ml-1">▼</span>
                        <div id="date-picker-menu" class="date-picker-menu bg-white border border-gray-200 rounded-lg shadow-xl hidden p-1">
                            <button class="date-option block w-full text-left p-2 hover:bg-gray-100 rounded-md" data-value="今日">今日</button>
                            </div>
                    </h2>
                    <h2 class="text-xl font-bold text-gray-700">の人気献立
                        <a href="U09.php" class="text-sm font-normal text-primary-pink ml-2 hover:underline">&gt;</a>
                    </h2>
                </div>
                </section>
            
            <section>
                 <h2 class="text-xl font-bold mb-3 text-gray-700">お気に入り
                    <a href="U08okini.php" class="text-sm font-normal text-primary-pink ml-2 hover:underline">&gt;</a>
                </h2>
                <div id="favorite-scroll" class="flex overflow-x-scroll hide-scrollbar space-x-4 pb-2 -mx-4 px-4">
                    <?php if (!empty($favorite_recipes)): ?>
                        <?php foreach ($favorite_recipes as $recipe): 
                            $recipe_id = htmlspecialchars($recipe['recipe_id'], ENT_QUOTES, 'UTF-8');
                            $title = htmlspecialchars($recipe['title'], ENT_QUOTES, 'UTF-8');
                            $image_path = htmlspecialchars($recipe['image_path'], ENT_QUOTES, 'UTF-8');
                            $is_okini = $recipe['okini'] == 1;
                        ?>
                            <div class="flex-shrink-0 meal-card relative" data-meal-id="<?php echo $recipe_id; ?>">
                                <div class="h-2/3 bg-gray-200" style="background-image: url('<?php echo $image_path; ?>'); background-size: cover; background-position: center;"></div>
                                <div class="p-2">
                                    <h3 class="font-semibold text-gray-800 text-sm truncate"><?php echo $title; ?></h3>
                                </div>
                                <div class="absolute top-2 right-2 p-1 rounded-full bg-white/70 backdrop-blur-sm shadow-md flex items-center space-x-1">
                                    <button class="star-button p-0.5 transition duration-150 <?php echo $is_okini ? 'text-accent-yellow' : 'text-secondary-gray'; ?>" data-okini="<?php echo $is_okini ? '1' : '0'; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?php echo $is_okini ? 'fill-current' : 'fill-none stroke-current'; ?>" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
            
            <section class="mt-8">
                <h2 class="text-xl font-bold mb-3 text-gray-700">今日の献立</h2>
                <div id="register-area" class="h-[300px] rounded-2xl shadow-xl flex justify-center items-center relative overflow-hidden bg-white" style="background-image: url('https://placehold.co/600x400/f0f0f0/333?text=Dining+Table+Image'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-white/30 backdrop-blur-sm"></div>
                    <button id="register-button" onclick="window.location.href='U11TOUROKU.php'" class="relative w-10 h-10 bg-primary-pink/90 text-white rounded-full shadow-2xl shadow-primary-pink/50 flex items-center justify-center transition duration-300 transform hover:scale-105 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </section>
        </div>

        <footer class="fixed bottom-0 left-0 right-0 max-w-md mx-auto border-t border-gray-200 shadow-2xl p-3 z-20 ui-element-bg bg-white/95 backdrop-blur-sm">
             <div class="flex flex-col space-y-3">
                <div class="flex items-end justify-start w-full">
                    <div class="flex-shrink-0 text-center w-16 relative mr-2">
                        <a href="U10BYOUKI.php">
                            <button class="reaction-item w-12 h-12 text-3xl p-1 bg-primary-pink/10 border-2 border-primary-pink rounded-full transition duration-150 transform hover:scale-105 flex items-center justify-center">
                                <span id="my-reaction-emoji" role="img" aria-label="自分">😊</span>
                            </button>
                        </a>
                        <p id="my-reaction-name" class="text-xs font-medium text-primary-pink mt-1"><?php echo $user_name_display; ?></p>
                    </div>
                    </div>
            </div>
        </footer>

    </div>

    <div id="drawer-backdrop" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden" onclick="closeDrawer()"></div>
    <div id="drawer" class="fixed top-0 right-0 h-full bg-white shadow-2xl z-40 drawer flex flex-col">
        <div class="flex-shrink-0">
            <?php if ($is_applicant && !$is_parent): ?>
                <div id="applicant-message" class="bg-yellow-100 text-yellow-800 p-4 text-sm font-semibold border-b border-yellow-300">
                    現在、グループへの参加申請中です。管理者の承認をお待ちください。
                </div>
            <?php endif; ?>
            
            <?php if ($is_parent): ?>
                <div id="application-notification" class="<?php echo empty($pending_applications) ? 'hidden' : ''; ?> bg-gray-100 border-b border-gray-200 text-sm">
                    <?php if (!empty($pending_applications)): ?>
                        <?php foreach ($pending_applications as $app): ?>
                            <div class="flex justify-between items-center py-2 px-4 border-b border-gray-200 last:border-0" id="app-row-<?php echo $app['application_ID']; ?>">
                                <span class="text-gray-700">
                                    <span class="font-bold"><?php echo htmlspecialchars($app['applicant_name'], ENT_QUOTES); ?></span>から申請
                                </span>
                                <div class="flex space-x-2">
                                    <button class="text-sm text-green-600 font-bold hover:underline" 
                                            onclick="handleApplication('approve', <?php echo $app['application_ID']; ?>)">承認</button>
                                    <button class="text-sm text-red-600 font-bold hover:underline" 
                                            onclick="handleApplication('reject', <?php echo $app['application_ID']; ?>)">拒否</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="py-2 px-4 text-gray-500 text-center">新しい申請はありません</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <button id="notification-bell-button" class="p-1 rounded-full notification-bell <?php echo !empty($pending_applications) ? 'has-notification' : ''; ?>" <?php echo $is_parent ? 'onclick="toggleApplicationNotification()"' : 'disabled'; ?>>
                        <span id="bell-icon" class="text-3xl">🔔</span>
                    </button>
                    <button class="text-gray-600 hover:text-gray-800" onclick="closeDrawer()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                
                <p class="text-sm text-gray-600 mb-8">家族コード <span class="font-bold text-gray-800"><?php echo htmlspecialchars($my_family_code); ?></span></p>

                <div class="flex flex-col items-center mb-10">
                    <button id="user-icon-button" class="relative w-28 h-28 rounded-full shadow-md flex items-center justify-center mb-4 transition duration-150 user-icon-container" onclick="changeIconImage()">
                        <div id="user-icon-background" class="w-full h-full rounded-full bg-gray-300 transition-opacity duration-300"></div>
                        <div id="user-emoji" class="absolute text-5xl transition-opacity duration-300"></div>
                    </button>

                    <p id="user-name" class="text-lg font-bold text-gray-700 p-1 border-b border-gray-300 cursor-pointer hover:bg-gray-100 transition duration-150" onclick="editName()">
                        <?php echo $user_name_display; ?>
                    </p>
                </div>

                <nav class="space-y-6 text-gray-700 text-lg font-semibold">
                    <a href="U14MEMO.php">買い物リスト</a><br>
                    <a href="U04DELEATE.php">グループ削除</a>
                    <a href="U01LOGIN.php" class="block mt-6 text-sm text-red-500">ログアウト</a>
                </nav>
            </div>
        </div>
    </div>

    <div id="message-box" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300" onclick="closeMessageBox()">
        <div class="bg-white p-6 rounded-xl shadow-2xl max-w-xs w-full text-center transform transition-transform duration-300" onclick="event.stopPropagation()">
            <p id="message-text" class="text-gray-800 font-semibold mb-4"></p>
            <button class="bg-primary-pink text-white px-4 py-2 rounded-lg font-bold hover:bg-primary-pink/80 transition duration-150" onclick="closeMessageBox()">OK</button>
        </div>
    </div>

    <script>
        // --- 基本設定 ---
        let userName = "<?php echo $user_name_display; ?>";
        let currentEmoji = "😊"; 
        let hasNotification = <?php echo !empty($pending_applications) ? 'true' : 'false'; ?>; 
        let userIconUrl = ""; 
        let currentSelection = "今日"; 
        const isParent = <?php echo $is_parent ? 'true' : 'false'; ?>;

        const ICON_OPTIONS = {
            "デフォルト (灰色)": "",
            "アイコンA": "https://placehold.co/100x100/1e40af/ffffff?text=IconA",
            "アイコンB": "https://placehold.co/100x100/dc2626/ffffff?text=IconB",
            "アイコンC": "https://placehold.co/100x100/059669/ffffff?text=IconC"
        };
        const ICON_NAMES = Object.keys(ICON_OPTIONS);

        // 要素取得
        const drawer = document.getElementById('drawer');
        const drawerBackdrop = document.getElementById('drawer-backdrop');
        const menuButton = document.getElementById('menu-button');
        const userNameElement = document.getElementById('user-name');
        const myReactionNameElement = document.getElementById('my-reaction-name');
        const userEmojiElement = document.getElementById('user-emoji');
        const userIconBackground = document.getElementById('user-icon-background'); 
        const bellButton = document.getElementById('notification-bell-button');
        const applicationNotification = document.getElementById('application-notification');
        const datePickerTrigger = document.getElementById('date-picker-trigger'); 
        const datePickerMenu = document.getElementById('date-picker-menu');
        const flashMessage = document.getElementById('flash-message');

        document.addEventListener('DOMContentLoaded', () => {
            userNameElement.textContent = userName;
            myReactionNameElement.textContent = userName;
            
            updateBellNotification();
            updateUserIcon();
            loadUserData();

            // フラッシュメッセージを5秒後に非表示
            if (flashMessage) {
                setTimeout(() => {
                    flashMessage.style.display = 'none';
                }, 5000);
            }
        });

        // ★JS側の通知処理★
        window.toggleApplicationNotification = function() {
            if (isParent && applicationNotification) {
                applicationNotification.classList.toggle('hidden');
            }
        };

        // ★承認・拒否ボタンの処理 (Ajax対応)★
        window.handleApplication = function(action, appId) {
            if (!isParent) return; // 親アカウント以外は無視

            fetch('U06HOME.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'process_application',
                    application_id: appId,
                    decision: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessageBox(data.message);
                    // 該当する行を消す
                    const row = document.getElementById('app-row-' + appId);
                    if (row) row.remove();
                    
                    // 通知がなくなったかチェック
                    const remaining = document.querySelectorAll('[id^="app-row-"]').length;
                    if (remaining === 0) {
                        hasNotification = false;
                        updateBellNotification();
                        if (applicationNotification) {
                             applicationNotification.innerHTML = '<div class="py-2 px-4 text-gray-500 text-center">新しい申請はありません</div>';
                        }
                    }
                } else {
                    showMessageBox('エラー: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                showMessageBox('通信エラーが発生しました');
            });
        };

        function updateBellNotification() {
            // CSSクラスの付け外しで赤丸を制御
            if (bellButton) {
                 if (hasNotification) {
                    bellButton.classList.add('has-notification');
                } else {
                    bellButton.classList.remove('has-notification');
                }
            }
        }

        // --- 以下、既存の機能 (お気に入り、ドロワー開閉、検索など) ---

        // STARボタン (お気に入り)
        document.querySelectorAll('.star-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); 
                const btn = e.currentTarget;
                const card = btn.closest('.meal-card');
                const recipeId = card.getAttribute('data-meal-id');
                const svg = btn.querySelector('svg');
                
                const isCurrentlyStarred = btn.getAttribute('data-okini') === '1';
                const newOkiniValue = isCurrentlyStarred ? 0 : 1; 

                fetch('U06HOME.php', { 
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
                        if (newOkiniValue === 1) {
                            btn.classList.add('text-accent-yellow');
                            btn.classList.remove('text-secondary-gray');
                            svg.classList.add('fill-current');
                            svg.classList.remove('fill-none', 'stroke-current');
                            showMessageBox('お気に入りに追加しました！');
                            btn.setAttribute('data-okini', '1');
                        } else {
                            btn.classList.remove('text-accent-yellow');
                            btn.classList.add('text-secondary-gray');
                            svg.classList.remove('fill-current');
                            svg.classList.add('fill-none', 'stroke-current');
                            showMessageBox('お気に入りを解除しました。');
                            btn.setAttribute('data-okini', '0');
                        }
                    } else {
                        showMessageBox('更新失敗');
                    }
                })
                .catch(error => showMessageBox('通信エラー'));
        });
        });

        function showMessageBox(message) {
            const box = document.getElementById('message-box');
            document.getElementById('message-text').textContent = message;
            box.classList.remove('hidden'); box.classList.add('flex');
        }
        function closeMessageBox() {
            const box = document.getElementById('message-box');
            box.classList.remove('flex'); box.classList.add('hidden');
        }

        menuButton.addEventListener('click', () => {
            drawer.classList.add('is-open');
            drawerBackdrop.classList.remove('hidden');
        });
        window.closeDrawer = function() {
            drawer.classList.remove('is-open');
            drawerBackdrop.classList.add('hidden');
            if (applicationNotification) {
                 applicationNotification.classList.add('hidden');
            }
        };

        window.editName = function() {
            const newName = prompt("新しい名前を入力してください:", userName);
            if (newName && newName.trim() !== "") {
                userName = newName.trim();
                userNameElement.textContent = userName;
                myReactionNameElement.textContent = userName;
                // TODO: ここでDBに新しい名前を保存する処理を追加
            }
        };
        window.changeIconImage = function() {
            const selection = prompt(`新しいアイコン画像を選択してください:\n${ICON_NAMES.join(', ')}`, ICON_NAMES[0]);
            if (selection && ICON_OPTIONS.hasOwnProperty(selection)) {
                userIconUrl = ICON_OPTIONS[selection];
                updateUserIcon();
                // TODO: ここでDBに新しいアイコンを保存する処理を追加
            }
        };
        function updateUserIcon() {
            if (userIconUrl) {
                userIconBackground.style.backgroundImage = `url('${userIconUrl}')`;
                userIconBackground.style.backgroundColor = 'transparent';
            } else {
                userIconBackground.style.backgroundImage = 'none';
                userIconBackground.style.backgroundColor = '#D1D5DB';
            }
            userEmojiElement.textContent = currentEmoji ? currentEmoji : "";
            userEmojiElement.classList.toggle('opacity-0', !currentEmoji);
        }

        document.querySelectorAll('.meal-card').forEach(card => {
            card.addEventListener('click', (e) => {
                const id = e.currentTarget.getAttribute('data-meal-id');
                window.location.href = `U24SYOUSAI.php?id=${id}`;
            });
        });

        datePickerTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            datePickerMenu.classList.toggle('hidden');
        });
        document.querySelectorAll('.date-option').forEach(opt => {
            opt.addEventListener('click', (e) => {
                updatePopularHeading(e.target.getAttribute('data-value'));
                datePickerMenu.classList.add('hidden');
            });
        });
        document.addEventListener('click', () => {
            if (!datePickerMenu.classList.contains('hidden')) datePickerMenu.classList.add('hidden');
        });
        function updatePopularHeading(val) {
            currentSelection = val;
            datePickerTrigger.childNodes[0].nodeValue = val + " "; 
        }

        function loadUserData() {
            // ... (リアクションやリクエストのロード処理は省略せずそのまま残す) ...
            const statusJson = localStorage.getItem('userStatus');
            if (statusJson) {
                try {
                    const statusData = JSON.parse(statusJson);
                    if (statusData.emoji) {
                        currentEmoji = statusData.emoji;
                        document.getElementById('my-reaction-emoji').textContent = currentEmoji;
                        updateUserIcon();
                    }
                } catch (e) {}
            }
        }
    </script>
</body>
</html>