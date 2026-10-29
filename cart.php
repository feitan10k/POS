<?php
require "connect.php";

$cart = isset($_SESSION["cart"]) ? $_SESSION["cart"] : "";
$items = [];
$total = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $place = implode(",", $ids);
    $result = $conn->query("SELECT * FROM products WHERE id IN ($place)");
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $row["qty"] = $cart[$row["id"]];
        $row["sum"] = $row["price"] * $row["qty"];
        $total = $total + $row["sum"];
        $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pent.T Shop - ตะกร้า</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="topbar">
    <div class="brand"> Pent.T Shop</div>
    <nav>
        <a href="index.php">เมนูทั้งหมด</a>
        <a href="orders.php">รายการสั่งซื้อ</a>
        <a href="cart.php" class="cart-link active"> ตะกร้า</a>
    </nav>
</header>

<div class="container">
<h2> ตะกร้าของคุณ</h2>

<?php if (empty($items)) { ?>
    <div class="alert">ยังไม่มีรายการในตะกร้า<a href="index.php">ไปเลือกเมนู</a></div>
<?php } else { ?>

<table class="table">
    <tr>
        <th>เมนู</th><th>ราคา</th><th>จำนวน</th><th>รวม</th><th></th>
    </tr>
<?php foreach ($items as $item) { ?>
    <tr>
        <td><?= $item["name"] ?></td>
        <td><?= number_format($item["price"], 2) ?></td>
        <td class="qty">
            <a href="cart_remove.php?id=<?= $item["id"] ?>&one=1"></a>
            <?= $item["qty"] ?>
            <a href="cart_add.php?id=<?= $item["id"] ?>&back=cart"></a>
        </td>
        <td><?= number_format($item["sum"], 2) ?></td>
        <td><a class="btn del" href="cart_remove.php?id=<?= $item["id"] ?>">ลบ</a></td>
    </tr>
<?php } ?>
    <tr class="total-row">
        <td colspan="3">ยอดรวมทั้งหมด</td>
        <td colspan="2"><?= number_format($total, 2) ?> บาท</td>
    </tr>
</table>

<h3>ข้อมูลผู้สั่ง</h3>
<form method="post" action="order_save.php" class="order-form">
    <label>ชื่อผู้สั่ง</label>
    <input type="text" name="customer_name" placeholder="เช่น สมชาย ใจดี" required>
    <button type="submit" class="btn order">สั่งออเดอร์️</button>
</form>

<?php } ?>
</div>

<footer>Pent.T Shop © 2026 — PHP Basic Project</footer>

</body>
</html>
