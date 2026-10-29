<?php

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = $_GET["id"];
    if (isset($_GET["one"]) && $_SESSION["cart"][$id] > 1) {
        $_SESSION["cart"][$id] = $_SESSION["cart"][$id] - 1;
    } else {
        unset($_SESSION["cart"][$id]);
    }
}
header("Location: cart.php");
exit;
