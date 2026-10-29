<?php
require "connect.php";

if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    header("Location: cart.php");
    exit;
}

if (isset($_POST["customer_name"]) && !empty($_POST["customer_name"])) {
    $customerName = $_POST["customer_name"];

    $ids = implode(",", array_keys($_SESSION["cart"]));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);

    try {
        $conn->beginTransaction();

        $total = 0;
        foreach ($rows as $row) {
            $total = $total + ($row["price"] * $_SESSION["cart"][$row["id"]]);
        }

        $stmt = $conn->prepare("INSERT INTO orders (customer_name, total) VALUES (?, ?)");
        $stmt->execute([$customerName, $total]);
        $orderId = $conn->lastInsertId();

        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, qty) VALUES (?, ?, ?, ?, ?)");
        foreach ($rows as $row) {
            $qty = $_SESSION["cart"][$row["id"]];
            $stmtItem->execute([$orderId, $row["id"], $row["name"], $row["price"], $qty]);
        }

        $conn->commit();
        unset($_SESSION["cart"]);
        header("Location: orders.php?success=1&id=" . $orderId);
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        die("บันทึกออเดอร์ไม่สำเร็จ: " . $e->getMessage());
    }
} else {
    header("Location: cart.php?noname=1");
    exit;
}
