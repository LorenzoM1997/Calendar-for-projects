<?php

include('db.php');

$start= $_POST['start'];
$end = $_POST['end'];
$title_phase = $_POST['title_phase'];
$id = $_POST['id'];

$mydata = $mysqli->query("INSERT INTO `fasi` (`id`, `id_project`, `name`, `start`, `end`, `complete`) VALUES (NULL, '".$id."', '".$title_phase."', '".$start."', '".$end."', 0)");
header("location: ../calendar/index.php");
?>