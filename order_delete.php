<?php
require "connect.php";

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    try {
        $stmtI = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmtI->execute([$_GET["id"]]);
        $stmtO = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmtO->execute([$_GET["id"]]);
    } catch (PDOException $e) {
        die("ลบออเดอร์ไม่สำเร็จ: " . $e->getMessage());
    }
}
header("Location: orders.php?deleted=1");
exit;
