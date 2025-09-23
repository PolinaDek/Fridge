<?php
require_once 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);


        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        die("Ошибка при удалении продукта: " . $e->getMessage());
    }
} else {

    header('Location: index.php');
    exit();
}
?>