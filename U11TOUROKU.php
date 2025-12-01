<?php
// U11TOUROKU.php: レシピ登録フォーム (DB接続・登録処理・表示統合版)

// ==========================================================
// データベース接続設定
// ==========================================================
// ==========================================================
// データベース接続設定 (修正版)
// ==========================================================
$db_host = 'mysql320.phy.lolipop.lan'; 

// ★修正: ユーザー名はハイフンなしのIDのみであることが多いです
$db_user = 'LAA1685019'; 

// ★修正: パスワード（変更していなければそのまま）
$db_pass = '6group'; 

// ★修正: データベース名の方にハイフン付きの名前を指定します
$db_name = 'LAA1685019-kondatehausu';
// 登録成功後のリダイレクト先
$complete_page = 'U06HOME.php'; 
// 画像保存先のディレクトリ (パーミッション 707/777 が必要)
$upload_dir = 'uploads/recipe_images/'; 

// エラーメッセージ初期化
$errors = [];
$form_data = [];

// ==========================================================
// フォーム送信時の処理 (POST)
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. データの受け取り
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $share = intval($_POST['share'] ?? 1); 
    $time = intval($_POST['time'] ?? 0);
    $cost = intval($_POST['cost'] ?? 0);

    // 栄養成分
    $calorie = intval($_POST['calorie'] ?? 0);
    $carbs = floatval($_POST['carbs'] ?? 0);
    $carbohydrate = floatval($_POST['fat'] ?? 0); // 脂質 (DB物理名: carbohydrate)
    $protein = floatval($_POST['protein'] ?? 0);
    $sugar = floatval($_POST['sugar'] ?? 0);
    $salt = floatval($_POST['salt'] ?? 0);

    // 入力値を保持（エラー時の再表示用）
    $form_data = $_POST;

    // 2. 入力チェック
    if (empty($title)) {
        $errors[] = "料理名は必須です。";
    }
    if (empty($content)) {
        $errors[] = "調理内容は必須です。";
    }

    // 固定値設定
    $hert = 0;          
    $material = 0;      
    $group_id = 1;      

    // 3. 画像アップロード処理
    $image_path = NULL; 
    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_info = pathinfo($_FILES['recipe_image']['name']);
        $extension = $file_info['extension'];
        $file_name = time() . uniqid() . '.' . $extension;
        $destination = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['recipe_image']['tmp_name'], $destination)) {
            $image_path = $destination; 
        } else {
            $errors[] = "画像の保存に失敗しました。ディレクトリの権限を確認してください。";
        }
    }

    // 4. データベース登録
    if (empty($errors)) {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
            
            // 成功時リダイレクト
            header("Location: {$complete_page}");
            exit; 

        } catch (PDOException $e) {
            $errors[] = "DBエラー: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レシピ登録</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        
        /* 栄養情報のスタイル */
        .nutrition-input-group { display: flex; align-items: center; justify-content: flex-end; width: 100%; }
        .nutrition-input { width: 36px; padding: 0; margin: 0 2px; border: none; text-align: right; background: transparent; color: #ef4444; font-weight: 700; font-size: 0.9rem; }
        .nutrition-unit, .nutrition-approx { white-space: nowrap; font-size: 0.75rem; color: #6b7280; }
        .nutrition-label { white-space: nowrap; color: #4b5563; font-size: 0.8rem; display: flex; align-items: center; font-weight: 600; }
        
        /* 共通インプットスタイル */
        .info-input { text-align: center; border: none; background: transparent; font-weight: 600; color: #1f2937; padding: 0; width: 100%; outline: none; }
        .ingredient-input { background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px; }
        
        /* エラーメッセージ */
        .error-message { color: #dc2626; background-color: #fee2e2; border: 1px solid #fca5a5; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'primary-red': '#ef4444' }
                }
            }
        };

        // 行追加ボタンの動作
        function addIngredientRow() {
            const container = document.getElementById('ingredient-list');
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center space-x-2 mb-2';
            newRow.innerHTML = `
                <input type="text" name="material_name[]" placeholder="材料名・分量"
                       class="w-full p-2 text-sm border border-gray-200 rounded focus:ring-1 focus:ring-primary-red focus:border-primary-red ingredient-input bg-gray-50">
            `;
            container.appendChild(newRow);
            container.scrollTop = container.scrollHeight;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const listContainer = document.getElementById('ingredient-list');
            if (listContainer.children.length === 0) {
                for(let i = 0; i < 3; i++) { addIngredientRow(); }
            }
            
            // 画像プレビュー機能
            const imageInput = document.getElementById('recipe-image');
            const previewContainer = document.getElementById('image-preview');
            const uploadText = document.getElementById('upload-text');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.style.backgroundImage = `url('${e.target.result}')`;
                        previewContainer.style.backgroundSize = 'cover';
                        previewContainer.style.backgroundPosition = 'center';
                        uploadText.style.display = 'none';
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</head>
<body class="min-h-screen flex justify-center py-4 px-2">

    <div class="w-full max-w-sm bg-white shadow-xl rounded-2xl overflow-hidden flex flex-col">
        <div class="px-5 py-3 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10">
            <h1 class="text-lg font-bold text-gray-800">レシピ登録</h1>
            <a href="U06HOME.php" class="text-sm text-gray-400 hover:text-gray-600">キャンセル</a>
        </div>

        <div class="p-5 overflow-y-auto">
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <ul class="list-disc ml-4">
                        <?php foreach ($errors as $error): echo "<li>".htmlspecialchars($error)."</li>"; endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="U11TOUROKU.php" method="POST" enctype="multipart/form-data" class="space-y-4">

                <div>
                    <input type="text" name="title" placeholder="料理名を入力"
                           value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>"
                           class="w-full p-3 border-b-2 border-gray-200 focus:border-primary-red outline-none text-lg font-bold text-gray-800 placeholder-gray-400 bg-transparent transition-colors" required>
                </div>

                <div id="image-preview" class="h-40 bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden group">
                    <input type="file" id="recipe-image" name="recipe_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                    <div id="upload-text" class="text-gray-400 flex flex-col items-center group-hover:text-primary-red transition">
                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-xs font-bold">写真を登録</span>
                    </div>
                </div>

                <div class="flex justify-between bg-gray-50 p-3 rounded-xl text-center shadow-sm">
                    <div class="w-1/3 border-r border-gray-200">
                        <p class="text-[10px] text-gray-500 mb-1">公開範囲</p>
                        <select name="share" class="info-input text-xs cursor-pointer text-primary-red">
                            <option value="1" <?php echo (isset($form_data['share']) && $form_data['share'] == '1') ? 'selected' : ''; ?>>全員</option>
                            <option value="0" <?php echo (isset($form_data['share']) && $form_data['share'] == '0') ? 'selected' : ''; ?>>自分</option>
                        </select>
                    </div>
                    <div class="w-1/3 border-r border-gray-200 px-1">
                        <p class="text-[10px] text-gray-500 mb-1">調理時間</p>
                        <div class="flex justify-center items-end">
                            <input type="number" name="time" value="<?php echo htmlspecialchars($form_data['time'] ?? '15'); ?>" class="info-input w-8 border-b border-gray-300 focus:border-primary-red" min="0">
                            <span class="text-xs text-gray-600 ml-0.5">分</span>
                        </div>
                    </div>
                    <div class="w-1/3 px-1">
                        <p class="text-[10px] text-gray-500 mb-1">費用目安</p>
                        <div class="flex justify-center items-end">
                            <input type="number" name="cost" value="<?php echo htmlspecialchars($form_data['cost'] ?? '300'); ?>" class="info-input w-10 border-b border-gray-300 focus:border-primary-red" min="0">
                            <span class="text-xs text-gray-600 ml-0.5">円</span>
                        </div>
                    </div>
                </div>

                <textarea name="content" rows="3" placeholder="レシピのポイントや手順を簡単に..."
                          class="w-full p-3 border border-gray-200 rounded-xl focus:ring-1 focus:ring-primary-red focus:border-primary-red text-sm bg-gray-50 resize-none" required><?php echo htmlspecialchars($form_data['content'] ?? ''); ?></textarea>

                <div class="bg-red-50 rounded-xl p-3 border border-red-100">
                    <p class="text-xs font-bold text-red-400 mb-2 text-center">- 栄養成分 (目安) -</p>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                        <div class="flex justify-between items-center border-b border-red-100 pb-1">
                            <span class="nutrition-label"> カロリー</span>
                            <div class="nutrition-input-group"><input type="number" name="calorie" value="<?php echo htmlspecialchars($form_data['calorie'] ?? ''); ?>" class="nutrition-input" placeholder="0"><span class="nutrition-unit">kcal</span></div>
                        </div>
                        <div class="flex justify-between items-center border-b border-red-100 pb-1">
                            <span class="nutrition-label"> 炭水化物</span>
                            <div class="nutrition-input-group"><input type="number" name="carbs" value="<?php echo htmlspecialchars($form_data['carbs'] ?? ''); ?>" class="nutrition-input" placeholder="0"><span class="nutrition-unit">g</span></div>
                        </div>
                        <div class="flex justify-between items-center border-b border-red-100 pb-1">
                            <span class="nutrition-label"> 脂質</span>
                            <div class="nutrition-input-group"><input type="number" name="fat" value="<?php echo htmlspecialchars($form_data['fat'] ?? ''); ?>" class="nutrition-input" placeholder="0"><span class="nutrition-unit">g</span></div>
                        </div>
                        <div class="flex justify-between items-center border-b border-red-100 pb-1">
                            <span class="nutrition-label"> タンパク</span>
                            <div class="nutrition-input-group"><input type="number" name="protein" value="<?php echo htmlspecialchars($form_data['protein'] ?? ''); ?>" class="nutrition-input" placeholder="0"><span class="nutrition-unit">g</span></div>
                        </div>
                        <div class="flex justify-between items-center border-b border-red-100 pb-1">
                            <span class="nutrition-label"> 糖分</span>
                            <div class="nutrition-input-group"><input type="number" name="sugar" value="<?php echo htmlspecialchars($form_data['sugar'] ?? ''); ?>" class="nutrition-input" placeholder="0"><span class="nutrition-unit">g</span></div>
                        </div>
                        <div class="flex justify-between items-center border-b border-red-100 pb-1">
                            <span class="nutrition-label"> 塩分</span>
                            <div class="nutrition-input-group"><input type="number" name="salt" value="<?php echo htmlspecialchars($form_data['salt'] ?? ''); ?>" class="nutrition-input" placeholder="0"><span class="nutrition-unit">g</span></div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-sm font-bold text-gray-700">材料 <span class="text-xs font-normal text-gray-500">(1人分)</span></label>
                        <button type="button" onclick="addIngredientRow()" class="text-xs text-primary-red hover:bg-red-50 px-2 py-1 rounded font-bold transition">+ 追加</button>
                    </div>
                    <div id="ingredient-list" class="max-h-32 overflow-y-auto pr-1 space-y-1">
                        </div>
                </div>

                <button type="submit" class="w-full bg-primary-red text-white font-bold py-3 rounded-xl shadow-lg hover:bg-red-600 transition transform active:scale-95">
                    登録する
                </button>
            </form>
        </div>
    </div>
</body>
</html>