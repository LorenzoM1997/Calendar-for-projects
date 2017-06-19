<?php

include('db.php');

$title = $_POST['title'];
$description = $_POST['description'];

$mydata = $mysqli->query("INSERT INTO `projects` (`id`, `nome`, `descrizione`) VALUES (NULL, '".$title."', '".$description."')");
header("location: ../calendar/index.php");
?>