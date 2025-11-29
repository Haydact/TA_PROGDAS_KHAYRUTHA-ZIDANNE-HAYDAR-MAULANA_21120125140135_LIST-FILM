<?php
require "Film.php";
session_start();

$id = $_GET["id"];

$_SESSION["films"][$id]->favorite = !$_SESSION["films"][$id]->favorite;

header("Location: index.php");
exit;
?>
