<?php
require "connect.php";

$id = isset($_POST["id"]) ? trim($_POST["id"]) : "";
$name = isset($_POST["customer_name"]) ? trim($_POST["customer_name"]) : "";
$qtys = (isset($_POST["qty"]) && is_array($_POST["qty"])) ? $_POST["qty"] : [];

if ($name == "") {
    header("Location: orders.php");
    exit;
}

$clean = [];
foreach ($qtys as $pid => $q) {
    $pid = intval($pid);
    $q = intval($q);
    if ($pid > 0 && $q > 0) {
        $clean[$pid] = min($q, 99);
    }
}

if (empty($clean)) {
    if ($id != "") {
        header("Location: order_form.php?id=" . $id . "&empty=1");
    } else {
        header("Location: order_form.php?empty=1");
    }
    exit;
}

try {
    $conn->beginTransaction();

    $total = 0;
    $data = [];
    $stmtP = $conn->prepare("SELECT * FROM products WHERE id = ?");
    foreach ($clean as $pid => $q) {
        $stmtP->execute([$pid]);
        $p = $stmtP->fetch(PDO::FETCH_ASSOC);
        if ($p) {
            $total = $total + ($p["price"] * $q);
            $data[] = [$pid, $p["name"], $p["price"], $q];
        }
    }

    if ($id == "") {
        $stmtO = $conn->prepare("INSERT INTO orders (customer_name, total) VALUES (?, ?)");
        $stmtO->execute([$name, $total]);
        $id = $conn->lastInsertId();
    } else {
        $stmtO = $conn->prepare("UPDATE orders SET customer_name = ?, total = ? WHERE id = ?");
        $stmtO->execute([$name, $total, $id]);
        $stmtD = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmtD->execute([$id]);
    }

    $stmtI = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, qty) VALUES (?, ?, ?, ?, ?)");
    foreach ($data as $row) {
        $stmtI->execute([$id, $row[0], $row[1], $row[2], $row[3]]);
    }

    $conn->commit();
    header("Location: orders.php?saved=1&id=" . $id);
    exit;

} catch (PDOException $e) {
    $conn->rollBack();
    die("บันทึกออเดอร์ไม่สำเร็จ: " . $e->getMessage());
}
