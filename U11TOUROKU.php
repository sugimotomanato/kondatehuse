<?php
// U06HOME.phpからの遷移を想定したレシピ登録画面です。
// フォーム送信アクションを 'U06HOME.php' に設定することで、画面遷移を確実に行います。
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録画面</title>
    <!-- Tailwind CSS CDN -->
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
            /* flexで「約」、入力、単位を横に並べる */
            display: flex;
            align-items: center;
            /* 右端に詰める */
            justify-content: flex-end; 
            /* グループ全体が枠からはみ出さないよう最大幅を設定 */
            max-width: 50%; 
        }
        .nutrition-input {
            /* 枠に確実に収まるよう、幅をさらに縮小し、間隔を調整 */
            width: 2.5rem; /* 幅を2.5remに縮小 */
            padding: 0.25rem 0; /* 左右のパディングをゼロに近づける */
            margin-left: 2px; /* 「約」との間隔 */
            margin-right: 2px; /* 単位との間隔 */
            border: none;
            text-align: right;
            background-color: transparent;
            color: #ef4444; 
            font-weight: 600;
        }
        .nutrition-unit, .nutrition-approx {
            white-space: nowrap; /* テキストの折り返しを防ぐ */
            font-size: 0.875rem;
        }
        .nutrition-label {
            white-space: nowrap;
            color: #4b5563;
            font-size: 0.875rem;
            /* 左側のラベルが入力グループと重ならないように、最大幅を制限 */
            max-width: 50%; 
            overflow: hidden; 
            text-overflow: ellipsis; /* 必要であれば省略記号を使用 */
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
            background-color: #ffffff; /* 白地 */
            border: 1px solid #d1d5db; /* 薄いグレーの枠 */
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-red': '#ef4444', // 画像のアクセントカラー
                    },
                }
            }
        }

        // フォーム送信処理: この関数が実行された後、フォームのaction属性に基づき U06HOME.php に遷移します。
        function handleFormSubmit(event) {
            console.log('レシピデータを送信中...');
            // フォームの action="U06HOME.php" が遷移を制御します。
        }

        // 行追加ボタンの動作
        function addIngredientRow() {
            const container = document.getElementById('ingredient-list');
            const newRow = document.createElement('div');
            newRow.className = 'flex items-center space-x-2 mb-2';
            newRow.innerHTML = `
                <input type="text" placeholder="材料名・分量 (例: 鶏肉 200g)"
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-primary-red focus:border-primary-red ingredient-input">
            `;
            container.appendChild(newRow);
            container.scrollTop = container.scrollHeight;
        }

        // DOMContentLoaded後に初期の材料入力欄を追加
        document.addEventListener('DOMContentLoaded', () => {
            const listContainer = document.getElementById('ingredient-list');
            for(let i = 0; i < 3; i++) {
                const newRow = document.createElement('div');
                newRow.className = 'flex items-center space-x-2 mb-2';
                newRow.innerHTML = `
                    <input type="text" placeholder="材料名・分量"
                           class="w-full p-2 border border-gray-300 rounded-lg focus:ring-primary-red focus:border-primary-red ingredient-input">
                `;
                listContainer.appendChild(newRow);
            }
        });
    </script>
</head>
<body class="min-h-screen bg-gray-50 flex justify-center py-4 sm:py-8">

    <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-4 sm:p-6 mx-4">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">登録画面</h1>

        <!-- フォーム開始: action="U06HOME.php" で遷移を制御しています -->
        <form action="U06HOME.php" method="POST" onsubmit="handleFormSubmit(event)">

            <!-- 1. 料理名入力 -->
            <div class="mb-6">
                <input type="text" id="recipe-name" name="recipe_name" placeholder="料理名"
                       class="w-full p-3 border-2 border-gray-300 rounded-lg focus:ring-primary-red focus:border-primary-red text-lg bg-gray-100 font-medium text-gray-800">
            </div>

            <!-- 2. 画像アップロードエリア (灰色の四角) -->
            <div class="mb-6">
                <div class="h-48 bg-gray-200 rounded-xl flex items-center justify-center cursor-pointer hover:bg-gray-300 transition duration-150 relative overflow-hidden">
                    <input type="file" id="recipe-image" name="recipe_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                    <div class="text-gray-500 flex flex-col items-center">
                        <!-- プラスアイコン -->
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-sm mt-1">写真/ファイルを登録</p>
                    </div>
                </div>
            </div>

            <!-- 3. 公開範囲、時間、費用目安エリア -->
            <div class="mb-6 flex justify-between text-center text-sm">
                <!-- 公開範囲 -->
                <div class="w-1/3 p-2">
                    <p class="text-gray-600 mb-1">公開範囲</p>
                    <select id="scope" name="scope" class="info-input text-primary-red underline">
                        <option value="global">グローバル</option>
                        <option value="local">ローカル</option>
                    </select>
                </div>
                <!-- 調理時間 (数値入力) -->
                <div class="w-1/3 p-2">
                    <p class="text-gray-600 mb-1">調理時間</p>
                    <div class="flex justify-center items-center">
                        約<input type="number" name="cooking_time" value="0" class="info-input w-8 mx-0.5 text-gray-800" min="1">分
                    </div>
                </div>
                <!-- 費用目安 (数値入力) -->
                <div class="w-1/3 p-2">
                    <p class="text-gray-600 mb-1">費用目安</p>
                    <div class="flex justify-center items-center">
                        約<input type="number" name="cost" value="0" class="info-input w-10 mx-0.5 text-gray-800" min="0">円
                    </div>
                </div>
            </div>

            <!-- 4. 栄養情報入力エリア (2列3行グリッド - 最終調整) -->
            <div class="mb-6 bg-red-50 rounded-lg p-2 border border-red-200">
                <!-- gap-x-2を維持しつつ、要素内で間隔を詰めることで枠内収まりを改善 -->
                <div class="grid grid-cols-2 gap-y-1 gap-x-2 text-sm font-medium text-gray-700">
                    
                    <!-- 1行目: カロリー / 炭水化物 -->
                    <div class="flex items-center justify-between">
                        <span class="nutrition-label flex items-center"><span class="text-primary-red mr-1">🔥</span> カロリー:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="calory" value="0" class="nutrition-input" min="0">
                            <span class="nutrition-unit">kcal</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="nutrition-label flex items-center"><span class="text-yellow-600 mr-1">🍚</span> 炭水化物:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="carb" value="0" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>

                    <!-- 2行目: 脂質 / たんぱく質 -->
                    <div class="flex items-center justify-between">
                        <span class="nutrition-label flex items-center"><span class="text-primary-red mr-1">🥩</span> 脂質:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="fat" value="0" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="nutrition-label flex items-center"><span class="text-yellow-600 mr-1">🍗</span> たんぱく質:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="protein" value="0" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>

                    <!-- 3行目: 糖分 / 塩分 -->
                    <div class="flex items-center justify-between">
                        <span class="nutrition-label flex items-center"><span class="text-primary-red mr-1">🍬</span> 糖分:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="sugar" value="0" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="nutrition-label flex items-center"><span class="text-yellow-600 mr-1">🧂</span> 塩分:</span>
                        <div class="nutrition-input-group">
                            <span class="nutrition-approx">約</span>
                            <input type="number" name="salt" value="0" class="nutrition-input" min="0" step="0.1">
                            <span class="nutrition-unit">g</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 5. 材料入力エリア -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">材料 【1人分】</label>
                
                <!-- 材料リストコンテナ (JavaScriptで初期行を追加) -->
                <div id="ingredient-list" class="max-h-40 overflow-y-auto pr-2">
                    <!-- JSによって動的に材料入力行が挿入されます -->
                </div>

                <!-- 行追加ボタン (画像下部の点線で囲まれた部分を再現) -->
                <button type="button" onclick="addIngredientRow()"
                        class="w-full text-center text-primary-red border-b border-gray-300 py-1 mt-2 mb-4 hover:bg-gray-100 transition duration-150 rounded-md">
                    ここを押すと行追加できる
                </button>

                <!-- 詳細メモ/説明 (灰色の四角) -->
                <textarea name="detailed_memo" rows="4" placeholder="詳細にメモのように書く..."
                          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-primary-red focus:border-primary-red text-sm bg-gray-100"></textarea>
            </div>


            <!-- 6. 登録ボタン (U06HOME.phpへ遷移します) -->
            <button type="submit"
                    class="w-full bg-primary-red text-white font-bold py-3 rounded-full shadow-lg hover:bg-red-600 transition duration-150 ease-in-out tracking-wide">
                登録してホームに戻る
            </button>
        </form>

    </div>

</body>
</html>
