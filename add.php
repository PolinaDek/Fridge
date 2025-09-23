<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = htmlspecialchars(trim($_POST['name']));
    $quantity = (int)$_POST['quantity']; 
    $expiry_date = $_POST['expiry_date'];
    $description = htmlspecialchars(trim($_POST['description']));

 
    $errors = [];
    if (empty($name)) $errors[] = 'Название не может быть пустым.';
    if ($quantity <= 0) $errors[] = 'Количество должно быть больше 0.';
    if (empty($expiry_date)) $errors[] = 'Укажите срок годности.';


    if (empty($errors)) {
        try {
            $sql = "INSERT INTO products (name, quantity, expiry_date, description) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $quantity, $expiry_date, $description]);

          
            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            die("Ошибка при добавлении продукта: " . $e->getMessage());
        }
    } else {
       
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить продукт</title>
</head>
<body>
    <h1>Добавить новый продукт</h1>
    <a href="index.php">Назад к списку</a>
    <br><br>
    <form method="POST">
        <label>Название:</label><br>
        <input type="text" name="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required><br><br>

        <label>Количество:</label><br>
        <input type="number" name="quantity" min="1" value="<?= isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : 1 ?>" required><br><br>

        <label>Срок годности:</label><br>
        <input type="date" name="expiry_date" value="<?= isset($_POST['expiry_date']) ? htmlspecialchars($_POST['expiry_date']) : '' ?>" required><br><br>

        <label>Описание (необязательно):</label><br>
        <textarea name="description"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea><br><br>

        <button type="submit">Добавить продукт</button>
        <label>Статус:</label><br>
<select name="status">
    <option value="active" selected>Активен</option>
    <option value="used">Использован</option>
    <option value="thrown">Выброшен</option>
</select><br><br>
    </form>
</body>
</html>