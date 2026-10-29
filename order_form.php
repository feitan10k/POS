<?php
require "connect.php";

$id = isset($_GET["id"]) ? trim($_GET["id"]) : "";
$order = null;
$itemQty = [];

if ($id != "") {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        header("Location: orders.php");
        exit;
    }
    $stmtI = $conn->prepare("SELECT product_id, qty FROM order_items WHERE order_id = ?");
    $stmtI->execute([$id]);
    foreach ($stmtI->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $itemQty[$row["product_id"]] = $row["qty"];
    }
}

$result = $conn->query("SELECT * FROM products ORDER BY category, id");
$products = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pent.T Shop - <?= $id != "" ? "แก้ไข" : "เพิ่ม" ?>ออเดอร์</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="topbar">
    <a href="index.php" class="brand-link" title="กลับหน้าแรก">
        <img class="logo" src="img/logo.jpg" alt="Pent.T Shop">
    </a>
    <nav>
        <a href="index.php">เมนูทั้งหมด</a>
        <a href="orders.php">รายการสั่งซื้อ</a>
        <a href="cart.php" class="cart-link"> ตะกร้า</a>
    </nav>
</header>

<div class="container">
<h2><?= $id != "" ? "️ แก้ไขออเดอร์ #" . $id : " เพิ่มออเดอร์ใหม่" ?></h2>

<?php if (isset($_GET["empty"])) { ?>
    <div class="alert">กรุณาระบุจำนวนอย่างน้อย 1 รายการ</div>
<?php } ?>

<form method="post" action="order_update.php">
    <input type="hidden" name="id" value="<?= $id ?>">
    <label class="field-label">ชื่อผู้สั่ง</label>
    <input type="text" name="customer_name" required
           value="<?= $order ? htmlspecialchars($order["customer_name"]) : "" ?>"
           placeholder="เช่น สมชาย ใจดี">

    <table class="table">
        <tr><th>เมนู</th><th>ราคา</th><th>จำนวน</th></tr>
<?php
$lastCat = "";
foreach ($products as $p) {
    if ($p["category"] != $lastCat) {
        $lastCat = $p["category"];
?>
        <tr class="cat-row"><td colspan="3"><?= $lastCat == "drink" ? " เครื่องดื่ม" : "️ อาหาร" ?></td></tr>
<?php } ?>
        <tr>
            <td><?= $p["name"] ?></td>
            <td><?= number_format($p["price"], 2) ?></td>
            <td><input class="qty-input" type="number" name="qty[<?= $p["id"] ?>]"
                       min="0" max="99" value="<?= isset($itemQty[$p["id"]]) ? $itemQty[$p["id"]] : 0 ?>"></td>
        </tr>
<?php } ?>
    </table>

    <button type="submit" class="btn order"> บันทึกออเดอร์</button>
    <a class="btn cancel" href="orders.php">ยกเลิก</a>
</form>
</div>

<footer>Pent.T Shop © 2026 — PHP Basic Project</footer>

</body>
</html>
