<?php
require "connect.php";

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
$orders = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pent.T Shop - รายการสั่งซื้อ</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="topbar">
    <a href="index.php" class="brand-link" title="กลับหน้าแรก">
        <img class="logo" src="img/logo.jpg" alt="Pent.T Shop">
    </a>
    <nav>
        <a href="index.php">เมนูทั้งหมด</a>
        <a href="orders.php" class="active">รายการสั่งซื้อ</a>
        <a href="cart.php" class="cart-link"> ตะกร้า</a>
    </nav>
</header>

<div class="container">

<div class="page-head">
    <h2> รายการสั่งซื้อทั้งหมด</h2>
    <a class="btn new" href="order_form.php"> เพิ่มออเดอร์</a>
</div>

<?php if (isset($_GET["success"]) && $_GET["success"] == "1") { ?>
    <div class="alert ok">สั่งออเดอร์เรียบร้อยแล้ว  เลขที่ออเดอร์ #<?= $_GET["id"] ?></div>
<?php } ?>
<?php if (isset($_GET["saved"])) { ?>
    <div class="alert ok">บันทึกออเดอร์#<?= $_GET["id"] ?> เรียบร้อยแล้ว </div>
<?php } ?>
<?php if (isset($_GET["deleted"])) { ?>
    <div class="alert ok">ลบออเดอร์เรียบร้อยแล้ว ️</div>
<?php } ?>

<?php if (empty($orders)) { ?>
    <div class="alert">ยังไม่มีประวัติการสั่งซื้อ <a href="index.php">ไปสั่งเลย</a> หรือ <a href="order_form.php">เพิ่มออเดอร์ใหม่</a></div>
<?php } else { foreach ($orders as $order) {

    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order["id"]]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="order-card">
        <div class="order-head">
            <b>ออเดอร์ #<?= $order["id"] ?></b>
            <span>ผู้สั่ง: <?= $order["customer_name"] ?></span>
            <span><?= $order["created_at"] ?></span>
        </div>
        <table class="table small">
<?php foreach ($items as $item) { ?>
            <tr>
                <td><?= $item["product_name"] ?></td>
                <td>x <?= $item["qty"] ?></td>
                <td><?= number_format($item["price"], 2) ?> บาท</td>
            </tr>
<?php } ?>
            <tr class="total-row">
                <td colspan="2">ยอดรวม</td>
                <td><?= number_format($order["total"], 2) ?> บาท</td>
            </tr>
        </table>
        <div class="actions">
            <a class="btn edit" href="order_form.php?id=<?= $order["id"] ?>">️ แก้ไข</a>
            <a class="btn del" href="order_delete.php?id=<?= $order["id"] ?>"
               onclick="return confirm('ยืนยันลบออเดอร์ #<?= $order["id"] ?> ?')">️ ลบ</a>
        </div>
    </div>
<?php } } ?>

</div>

<footer>Pent.T Shop © 2026 — PHP Basic Project</footer>

</body>
</html>
