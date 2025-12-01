<?php
// U11TOUROKU.php: レシピ登録フォームの表示とDBへの登録処理を統合したファイル

// ==========================================================
// データベース接続設定 (ロリポップ情報を使用)
// ==========================================================
// 添付画像「スクリーンショット 2025-10-21 120214.png」に基づく
$db_host = 'mysql320.phy.lolipop.lan'; 
$db_user = 'LAA1685019-kondatehausu'; 
$db_pass = '6group'; 
$db_name = 'LAA1685019'; 

// 登録成功後のリダイレクト先 (ホーム画面など)
$complete_page = 'U06HOME.php'; 
// 画像保存先のディレクトリ (パーミッション 707/777 が必要)
$upload_dir = 'uploads/recipe_images/'; 

// エラーメッセージを初期化
$errors = [];
// フォームの入力値を保持するための配列
$form_data = [];

// ==========================================================
// フォームがPOST送信された場合の処理（データ登録実行）
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. データの受け取りと sanitization
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $share = intval($_POST['share'] ?? 1); 
    $time = intval($_POST['time'] ?? 0);
    $cost = intval($_POST['cost'] ?? 0);

    // 栄養成分 (DBカラム名に合わせる)
    // フォームの日本語名とDBの物理名の対応：
    // 脂質 -> carbohydrate, 炭水化物 -> carbs, たんぱく質 -> protein, 糖分 -> sugar, 塩分 -> salt
    $calorie = intval($_POST['calorie'] ?? 0);
    $carbs = floatval($_POST['carbs'] ?? 0); // 炭水化物
    $carbohydrate = floatval($_POST['fat'] ?? 0); // 脂質 (DB設計書の物理名「carbohydrate」を使用)
    $protein = floatval($_POST['protein'] ?? 0);
    $sugar = floatval($_POST['sugar'] ?? 0);
    $salt = floatval($_POST['salt'] ?? 0);

    // 入力値を保持
    $form_data = $_POST;

    // 2. 入力チェック (簡易版)
    if (empty($title)) {
        $errors[] = "料理名は必須です。";
    }
    if (empty($content)) {
        $errors[] = "調理内容は必須です。";
    }

    // ★★★ 暫定設定: NOT NULLだがフォームから渡されない値 ★★★
    $hert = 0;          
    $material = 0;      
    $group_id = 1;      // 実際はセッションから取得が必要

    // 3. 画像ファイルのアップロード処理
    $image_path = NULL; 
    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['recipe_image']['tmp_name'];
        $file_info = pathinfo($_FILES['recipe_image']['name']);
        $extension = $file_info['extension'];
        
        $file_name = time() . uniqid() . '.' . $extension;
        $destination = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp_name, $destination)) {
            $image_path = $destination; 
        } else {
            $errors[] = "画像ファイルの保存に失敗しました。フォルダ（uploads/recipe_images/）のパーミッションを確認してください。";
        }
    } else if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "画像アップロード中にエラーが発生しました（エラーコード: " . $_FILES['recipe_image']['error'] . "）。";
    }

    // 4. データベースへの登録
    if (empty($errors)) {
        try {
            // DB接続
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL文 (recipe テーブルの全カラムに対応)
            $sql = "INSERT INTO recipe (
                title, content, hert, group_id, calorie, carbohydrate, carbs, 
                sugar, protein, salt, material, share, time, cost, image_path
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title, $content, $hert, $group_id, $calorie, $carbohydrate, $carbs,
                $sugar, $protein, $salt, $material, $share, $time, $cost, $image_path
            ]);
            
            // 登録成功！完了画面へリダイレクト
            header("Location: {$complete_page}");
            exit; 

        } catch (PDOException $e) {
            $errors[] = "データベースエラーが発生しました: " . $e->getMessage();
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
    <title>U11料理登録画面</title>
        <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* カスタムフォントと基本設定 */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f8f8;
        }
        /* 栄養情報の入力欄のスタイル */
        .nutrition-input-group {
            display: flex;
            align-items: center;
            justify-content: flex-end; 
            width: 100%; /* 親要素の幅を使う */
        }
        .nutrition-input {
            width: 30px; 
            padding: 0.25rem 0; 
            margin-left: 2px; 
            margin-right: 2px; 
            border: none;
            text-align: right;
            background-color: transparent;
            color: #ef4444; 
            font-weight: 600;
            font-size: 0.875rem;
        }
        .nutrition-unit, .nutrition-approx {
            white-space: nowrap; 
            font-size: 0.875rem;
            color: #6b7280;
        }
        .nutrition-label {
            white-space: nowrap;
            color: #4b5563;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }
        /* 公開範囲、時間、費用目安の入力フィールドスタイルを統一 */
        .info-input {
            text-align: center;
            border: none;
            background-color: transparent;
            font-weight: 600;
            color: #1f2937;
            padding: 0;
            width: 100%;
        }
        /* 材料入力フィールドのスタイル */
        .ingredient-input {
            background-color: #ffffff; 
            border: 1px solid #d1d5db; 
        }
        .error-message {
            color: #dc2626; 
            background-color: #fee2e2; 
            border: 1px solid #fca5a5; 
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: left;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-red': '#ef4444', 
                    },
                }
            }
        }
        // 行追加ボタンの動作
        function addIngredientRow() {
            const container = document.getElementById('ingredient-list');
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center space-x-2 mb-2';
            newRow.innerHTML = `
                <input type="text" name="material_name[]" placeholder="材料名・分量"
                       class="w-full p-2 border-b border-gray-300 rounded-none focus:ring-0 focus:border-primary-red focus:border-b ingredient-input bg-gray-50">
            `;
            container.appendChild(newRow);
            container.scrollTop = container.scrollHeight;
        }

        // DOMContentLoaded後に初期の材料入力欄を追加
        document.addEventListener('DOMContentLoaded', () => {
            const listContainer = document.getElementById('ingredient-list');
            // POSTでエラーがあった場合、既存のデータがあればそれを使う
            // 今回はPHPで再入力を実装していないので、常に3行追加
            if (listContainer.children.length === 0) {
                for(let i = 0; i < 3; i++) {
                    addIngredientRow();
                }
            }
        });
    </script>
</head>
<body class="min-h-screen bg-gray-50 flex justify-center py-4 sm:py-8">

    <div class="w-full max-w-sm bg-white shadow-lg rounded-xl p-4 sm:p-6 mx-4">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 text-left">登録画面</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <p class="font-bold">エラーが発生しました:</p>
                <ul class="list-disc ml-5 mt-1">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

                <form action="U11TOUROKU.php" method="POST" enctype="multipart/form-data">

                        <div class="mb-4">
                <input type="text" id="recipe-name" name="title" placeholder="料理名"
                       value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-primary-red focus:border-primary-red text-base bg-gray-100 font-medium text-gray-800" required>
            </div>

                        <div class="mb-4">
                <div class="h-48 bg-gray-200 rounded-xl flex items-center justify-center cursor-pointer hover:bg-gray-300 transition duration-150 relative overflow-hidden">
                    <input type="file" id="recipe-image" name="recipe_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                    <div class="text-gray-500 flex flex-col items-center">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-sm mt-1">写真/ファイルを登録</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between text-center text-sm mb-4">
                <div class="w-1/3 p-1">
                    <p class="text-gray-600 mb-1">公開範囲</p>
                    <select id="scope" name="share" class="info-input text-primary-red underline">
                        <option value="1" <?php echo (isset($form_data['share']) && $form_data['share'] == '1') ? 'selected' : ''; ?>>グローバル</option>
                        <option value="0" <?php echo (isset($form_data['share']) && $form_data['share'] == '0') ? 'selected' : ''; ?>>ローカル</option>
                    </select>
                </div>
                <div class="w-1/3 p-1">
                    <p class="text-gray-600 mb-1">調理時間</p>
                    <div class="flex justify-center items-center">
                        <input type="number" name="time" value="<?php echo htmlspecialchars($form_data['time'] ?? '0'); ?>" class="info-input w-8 mx-0.5 text-gray-800 underline" min="0" required>分
                    </div>
                </div>
                <div class="w-1/3 p-1">
                    <p class="text-gray-600 mb-1">費用目安</p>
                    <div class="flex justify-center items-center">
                        約<input type="number" name="cost" value="<?php echo htmlspecialchars($form_data['cost'] ?? '0'); ?>" class="info-input w-8 mx-0.5 text-gray-800 underline" min="0" required>円
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <textarea name="content" rows="3" placeholder="説明を書く..."
                          class="w-full p-2 border border-gray-300 rounded-lg focus:ring-primary-red focus:border-primary-red text-sm bg-gray-100" required><?php echo htmlspecialchars($form_data['content'] ?? ''); ?></textarea>
            </div>

                        <div class="mb-4 bg-red-50 rounded-lg p-3 border border-red-200">
                <div class="grid grid-cols-2 gap-y-2 gap-x-2 text-sm font-medium text-gray-700">
                    
                                        <div class="flex items-center justify-between">
                        <span class="nutrition-label"><span class="mr-1">🔥</span> カロリー:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="calorie" value="<?php echo htmlspecialchars($form_data['calorie'] ?? '0'); ?>" class="nutrition-input" min="0">
                            <span class="nutrition-unit">kcal</span>
                        </div>
                    </div>

                                        <div class="flex items-center justify-between">
                        <span class="nutrition-label"><span class="mr-1">🍚</span> 炭水化物:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="carbs" value="<?php echo htmlspecialchars($form_data['carbs'] ?? '0'); ?>" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>

                                        <div class="flex items-center justify-between">
                        <span class="nutrition-label"><span class="mr-1">🥩</span> 脂質:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="fat" value="<?php echo htmlspecialchars($form_data['fat'] ?? '0'); ?>" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>

                                        <div class="flex items-center justify-between">
                        <span class="nutrition-label"><span class="mr-1">🍗</span> たんぱく質:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="protein" value="<?php echo htmlspecialchars($form_data['protein'] ?? '0'); ?>" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>

                                        <div class="flex items-center justify-between">
                        <span class="nutrition-label"><span class="mr-1">🍬</span> 糖分:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="sugar" value="<?php echo htmlspecialchars($form_data['sugar'] ?? '0'); ?>" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>

                                        <div class="flex items-center justify-between">
                        <span class="nutrition-label"><span class="mr-1">🧂</span> 塩分:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="salt" value="<?php echo htmlspecialchars($form_data['salt'] ?? '0'); ?>" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>
                </div>
            </div>
            
                        <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">材料 【1人分】</label>
                
                <div id="ingredient-list" class="max-h-32 overflow-y-auto pr-2 bg-gray-50 border border-gray-200 rounded-lg p-2">
                                    </div>

                <button type="button" onclick="addIngredientRow()"
                        class="w-full text-center text-primary-red border-b border-gray-300 py-1 mt-2 mb-4 text-xs hover:bg-gray-100 transition duration-150 rounded-md">
                    + 行追加
                </button>
            </div>


                        <button type="submit"
                    class="w-full bg-primary-red text-white font-bold py-3 rounded-full shadow-lg hover:bg-red-600 transition duration-150 ease-in-out tracking-wide">
                登録してホームに戻る
            </button>
        </form>

    </div>

</body>
</html>