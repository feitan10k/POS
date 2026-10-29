<?php
require "connect.php";
$cat = isset($_GET["cat"]) ? $_GET["cat"] : "";

$sections = [];
if ($cat == "drink" || $cat == "food") {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY id");
    $stmt->execute([$cat]);
    $title = $cat == "drink" ? " เครื่องดื่ม" : "️ อาหาร";
    $sections[] = ["title" => $title, "items" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
} else {
    $cat = "";
    foreach ([["drink", " เครื่องดื่ม"], ["food", "️ อาหาร"]] as $t) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY id");
        $stmt->execute([$t[0]]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            $sections[] = ["title" => $t[1], "items" => $rows];
        }
    }
}

$cartCount = 0;
if (isset($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $qty) {
        $cartCount = $cartCount + $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pent.T Shop - เมนู</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="topbar">
    <a href="index.php" class="brand-link" title="กลับหน้าแรก">
        <img class="logo" src="img/logo.jpg" alt="Pent.T Shop">
    </a>
    <nav>
        <a href="index.php" class="<?= $cat == "" ? "active" : "" ?>">เมนูทั้งหมด</a>
        <a href="index.php?cat=drink" class="<?= $cat == "drink" ? "active" : "" ?>">เครื่องดื่ม</a>
        <a href="index.php?cat=food" class="<?= $cat == "food" ? "active" : "" ?>">อาหาร</a>
        <a href="orders.php">รายการสั่งซื้อ</a>
        <a href="cart.php" class="cart-link"> ตะกร้า
            <?php if ($cartCount > 0) { ?>
                <span class="badge"><?= $cartCount ?></span>
            <?php } ?>
        </a>
    </nav>
</header>

<div class="hero">
    <h1>ยินดีต้อนรับสู่ Pent.T Shop</h1>
    <p>เลือกเมนูเครื่องดื่มและขนม ใส่ตะกร้าแล้วกดสั่งออเดอร์ได้เลย</p>
</div>

<div class="container">

<?php if (isset($_GET["added"]) && $_GET["added"] == "1") { ?>
    <div class="alert ok">เพิ่มเมนูลงตะกร้าแล้ว </div>
<?php } ?>

<?php foreach ($sections as $section) { ?>
    <h2 class="section-title"><?= $section["title"] ?></h2>
    <div class="grid">
    <?php foreach ($section["items"] as $item) { ?>
        <div class="card">
            <?php if (!empty($item["image"])) { ?>
                <img class="pic" src="img/<?= $item["image"] ?>" alt="<?= $item["name"] ?>">
            <?php } ?>
            <h3><?= $item["name"] ?></h3>
            <p class="detail"><?= $item["detail"] ?></p>
            <?php if ($item["price"] > 0) { ?>
                <p class="price"><?= number_format($item["price"], 2) ?> บาท</p>
                <a class="btn add" href="cart_add.php?id=<?= $item["id"] ?>">+ ใส่ตะกร้า</a>
            <?php } else { ?>
                <a class="btn view" href="img/<?= $item["image"] ?>" target="_blank"> ดูรายละเอียด</a>
            <?php } ?>
        </div>
    <?php } ?>
    </div>
<?php } ?>

</div>

<footer>Pent.T Shop © 2026 — PHP Basic Project</footer>

</body>
</html>
