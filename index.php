<?php
require_once 'config.php';
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusLabels = [
    'ok' => '🟢 В норме',
    'eat_soon' => '🟡 Съесть скорее', 
    'expired' => '🔴 Просрочен',
    'out_of_stock' => '⚫ Закончился'
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Умный Холодильник</title>
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

        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .btn-primary {
            background: #3498db;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .stats {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-radius: 8px;
            color: #2c3e50;
        }

     
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 5px solid #3498db;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

    
        .product-card.ok { border-left-color: #27ae60; }
        .product-card.eat_soon { border-left-color: #f39c12; }
        .product-card.expired { border-left-color: #e74c3c; }
        .product-card.out_of_stock { border-left-color: #95a5a6; }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .product-name {
            font-size: 1.3em;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }

        .product-quantity {
            background: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
        }

        .card-info {
            margin-bottom: 15px;
        }

        .expiry-date {
            font-size: 0.95em;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .days-left {
            font-weight: bold;
        }

        .expired-badge {
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8em;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status-ok { background: #d5f4e6; color: #27ae60; }
        .status-eat_soon { background: #fdebd0; color: #f39c12; }
        .status-expired { background: #fadbd8; color: #e74c3c; }
        .status-out_of_stock { background: #ebedef; color: #95a5a6; }

        .product-description {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .card-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .status-select {
            flex: 1;
            padding: 8px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-family: 'Unbounded', sans-serif;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-edit, .btn-delete {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: #3498db;
            color: white;
        }

        .btn-edit:hover {
            background: #2980b9;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        .add-date {
            font-size: 0.8em;
            color: #bdc3c7;
            text-align: right;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>УМНЫЙ ХОЛОДИЛЬНИК</h1>
        <p>Ваши продукты под контролем</p>
    </div>

    <div class="actions-bar">
        <a href="add.php" class="btn-primary">+ Добавить продукт</a>
        <div class="stats">
            <div class="stat-item">Всего: <?= count($products) ?></div>
            <div class="stat-item">Свежих: <?= count(array_filter($products, fn($p) => $p['status'] == 'ok')) ?></div>
            <div class="stat-item">Срочных: <?= count(array_filter($products, fn($p) => $p['status'] == 'eat_soon')) ?></div>
        </div>
    </div>

    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <?php
            $expiry = strtotime($product['expiry_date']);
            $today = strtotime('today');
            $daysLeft = floor(($expiry - $today) / (60 * 60 * 24));
            ?>
            
            <div class="product-card <?= $product['status'] ?>">
                <div class="card-header">
                    <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                    <span class="product-quantity"><?= htmlspecialchars($product['quantity']) ?> шт.</span>
                </div>

                <div class="card-info">
                    <div class="expiry-date">
                        📅 Срок: <?= htmlspecialchars($product['expiry_date']) ?>
                        <?php if ($daysLeft >= 0): ?>
                            <span class="days-left">(осталось <?= $daysLeft ?> дн.)</span>
                        <?php else: ?>
                            <span class="expired-badge">ПРОСРОЧЕНО</span>
                        <?php endif; ?>
                    </div>

                    <div class="status-badge status-<?= $product['status'] ?>">
                        <?= $statusLabels[$product['status']] ?>
                    </div>
                </div>

                <?php if (!empty($product['description'])): ?>
                    <div class="product-description">
                        <?= htmlspecialchars($product['description']) ?>
                    </div>
                <?php endif; ?>

                <div class="card-actions">
                    <select class="status-select" onchange="updateStatus(<?= $product['id'] ?>, this.value)">
                        <option value="ok" <?= $product['status'] == 'ok' ? 'selected' : '' ?>>В норме</option>
                        <option value="eat_soon" <?= $product['status'] == 'eat_soon' ? 'selected' : '' ?>>Съесть скорее</option>
                        <option value="expired" <?= $product['status'] == 'expired' ? 'selected' : '' ?>>Просрочен</option>
                        <option value="out_of_stock" <?= $product['status'] == 'out_of_stock' ? 'selected' : '' ?>>Закончился</option>
                    </select>

                    <div class="action-buttons">
                        <a href="edit.php?id=<?= $product['id'] ?>" class="btn-edit">✏️</a>
                        <form action="delete.php" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn-delete" onclick="return confirm('Удалить продукт?')">🗑️</button>
                        </form>
                    </div>
                </div>

                <div class="add-date">
                    Добавлено: <?= date('d.m.Y', strtotime($product['created_at'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
    function updateStatus(productId, status) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'update_status.php';
        
        const idInput = document.createElement('input');
        idInput.name = 'id';
        idInput.value = productId;
        
        const statusInput = document.createElement('input');
        statusInput.name = 'status';
        statusInput.value = status;
        
        form.appendChild(idInput);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
    </script>
</body>
</html>