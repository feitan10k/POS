<?php
require "connect.php";

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET["id"]]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if (!isset($_SESSION["cart"][$product["id"]])) {
            $_SESSION["cart"][$product["id"]] = 0;
        }
        $_SESSION["cart"][$product["id"]] = $_SESSION["cart"][$product["id"]] + 1;
    }
}
if (isset($_GET["back"]) && $_GET["back"] == "cart") {
    header("Location: cart.php");
} else {
    header("Location: index.php?added=1");
}
exit;
