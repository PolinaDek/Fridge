<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php?error=no_id');
    exit();
}

$id = (int)$_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php?error=product_not_found');
    exit();
}


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $quantity = (int)$_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];
    $description = htmlspecialchars(trim($_POST['description']));

    if (empty($name)) $errors[] = 'Название не может быть пустым.';
    if ($quantity <= 0) $errors[] = 'Количество должно быть больше 0.';
    if (empty($expiry_date)) $errors[] = 'Укажите срок годности.';

    if (empty($errors)) {
        try {
            $sql = "UPDATE products SET name = ?, quantity = ?, expiry_date = ?, description = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $quantity, $expiry_date, $description, $id]);

            header('Location: index.php?success=product_updated');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Ошибка при обновлении продукта: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать продукт - Умный Холодильник</title>
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

        .product-preview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
        }

        .preview-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .preview-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            font-size: 0.9em;
            color: #7f8c8d;
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
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>РЕДАКТИРОВАНИЕ ПРОДУКТА</h1>
        <p>Измените информацию о продукте</p>
    </div>

    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <strong>Ошибки при сохранении:</strong>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="product-preview">
            <div class="preview-title">Текущие данные продукта:</div>
            <div class="preview-info">
                <div><strong>Название:</strong> <?= htmlspecialchars($product['name']) ?></div>
                <div><strong>Количество:</strong> <?= htmlspecialchars($product['quantity']) ?> шт.</div>
                <div><strong>Срок годности:</strong> <?= htmlspecialchars($product['expiry_date']) ?></div>
                <div><strong>Статус:</strong> 
                    <?php
                    $statusLabels = [
                        'ok' => '🟢 В норме',
                        'eat_soon' => '🟡 Съесть скорее', 
                        'expired' => '🔴 Просрочен',
                        'out_of_stock' => '⚫ Закончился'
                    ];
                    echo $statusLabels[$product['status']] ?? $product['status'];
                    ?>
                </div>
            </div>
        </div>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label for="name">Название продукта:</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Количество:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="<?= htmlspecialchars($product['quantity']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="expiry_date">Срок годности:</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="<?= htmlspecialchars($product['expiry_date']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Описание (необязательно):</label>
                    <textarea id="description" name="description" placeholder="Дополнительная информация о продукте..."><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>
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
            const expiryDate = this.value;
            
            if (expiryDate < today) {
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
    </script>
</body>
</html>