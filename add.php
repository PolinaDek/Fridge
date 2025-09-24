<?php
require_once 'config.php';

$errors = [];
$formData = [
    'name' => '',
    'quantity' => 1,
    'expiry_date' => '',
    'description' => '',
    'status' => 'ok'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['name'] = htmlspecialchars(trim($_POST['name']));
    $formData['quantity'] = (int)$_POST['quantity'];
    $formData['expiry_date'] = $_POST['expiry_date'];
    $formData['description'] = htmlspecialchars(trim($_POST['description']));
    $formData['status'] = $_POST['status'];

    if (empty($formData['name'])) $errors[] = 'Название не может быть пустым.';
    if ($formData['quantity'] <= 0) $errors[] = 'Количество должно быть больше 0.';
    if (empty($formData['expiry_date'])) $errors[] = 'Укажите срок годности.';

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO products (name, quantity, expiry_date, description, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $formData['name'], 
                $formData['quantity'], 
                $formData['expiry_date'], 
                $formData['description'],
                $formData['status']
            ]);

            header('Location: index.php?success=product_added');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Ошибка при добавлении продукта: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить продукт - Умный Холодильник</title>
    <style>
        body {
            font-family: 'Unbounded', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5em;
            margin: 0 0 10px 0;
            color: #e8f4fc;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-family: 'Unbounded', sans-serif;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-family: 'Unbounded', sans-serif;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .error-message {
            background: #fadbd8;
            color: #e74c3c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 5px solid #e74c3c;
        }

        .error-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .error-list li {
            padding: 5px 0;
        }

        .status-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .status-option {
            padding: 10px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .status-option:hover {
            border-color: #3498db;
        }

        .status-option.selected {
            border-color: #3498db;
            background: #ebf5fb;
        }

        .status-option input {
            display: none;
        }

        .hint {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .container {
                max-width: 100%;
                padding: 0 10px;
            }
            
            .form-card {
                padding: 25px;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .status-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ДОБАВИТЬ НОВЫЙ ПРОДУКТ</h1>
        <p>Заполните информацию о продукте</p>
    </div>

    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <strong>Ошибки при добавлении:</strong>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label for="name">Название продукта:</label>
                    <input type="text" id="name" name="name" 
                           value="<?= htmlspecialchars($formData['name']) ?>" 
                           placeholder="Например: Молоко, Яблоки, Хлеб..." required>
                </div>

                <div class="form-group">
                    <label for="quantity">Количество:</label>
                    <input type="number" id="quantity" name="quantity" min="1" 
                           value="<?= htmlspecialchars($formData['quantity']) ?>" required>
                    <div class="hint">Укажите количество в штуках</div>
                </div>

                <div class="form-group">
                    <label for="expiry_date">Срок годности:</label>
                    <input type="date" id="expiry_date" name="expiry_date" 
                           value="<?= htmlspecialchars($formData['expiry_date']) ?>" required>
                    <div class="hint">Дата, когда продукт испортится</div>
                </div>

                <div class="form-group">
                    <label>Статус:</label>
                    <div class="status-options">
                        <label class="status-option <?= $formData['status'] == 'ok' ? 'selected' : '' ?>">
                            <input type="radio" name="status" value="ok" <?= $formData['status'] == 'ok' ? 'checked' : '' ?>>
                            🟢 В норме
                        </label>
                        <label class="status-option <?= $formData['status'] == 'eat_soon' ? 'selected' : '' ?>">
                            <input type="radio" name="status" value="eat_soon" <?= $formData['status'] == 'eat_soon' ? 'checked' : '' ?>>
                            🟡 Съесть скорее
                        </label>
                        <label class="status-option <?= $formData['status'] == 'expired' ? 'selected' : '' ?>">
                            <input type="radio" name="status" value="expired" <?= $formData['status'] == 'expired' ? 'checked' : '' ?>>
                            🔴 Просрочен
                        </label>
                        <label class="status-option <?= $formData['status'] == 'out_of_stock' ? 'selected' : '' ?>">
                            <input type="radio" name="status" value="out_of_stock" <?= $formData['status'] == 'out_of_stock' ? 'checked' : '' ?>>
                            ⚫ Закончился
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Описание (необязательно):</label>
                    <textarea id="description" name="description" 
                              placeholder="Дополнительная информация: бренд, особенности хранения, заметки..."><?= htmlspecialchars($formData['description']) ?></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">➕ Добавить продукт</button>
                    <a href="index.php" class="btn btn-secondary">← Назад к списку</a>
                </div>
            </form>
        </div>
    </div>

    <script>
  
        document.getElementById('name').focus();
        
     
        const expiryDateInput = document.getElementById('expiry_date');
        const quantityInput = document.getElementById('quantity');
        
        expiryDateInput.addEventListener('change', function() {
            const today = new Date().toISOString().split('T')[0];
            if (this.value && this.value < today) {
                this.style.borderColor = '#e74c3c';
            } else {
                this.style.borderColor = '#ecf0f1';
            }
        });
        
        quantityInput.addEventListener('input', function() {
            if (this.value <= 0) {
                this.style.borderColor = '#e74c3c';
            } else {
                this.style.borderColor = '#ecf0f1';
            }
        });

   
        document.querySelectorAll('.status-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.status-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input').checked = true;
            });
        });

        if (!expiryDateInput.value) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 7); // +7 дней по умолчанию
            expiryDateInput.value = tomorrow.toISOString().split('T')[0];
        }
    </script>
</body>
</html>
