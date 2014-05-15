<?php
session_start();
include_once 'Facultad.php';
$facultad = $_SESSION['facultad'];
$s = serialize($facultad);
file_put_contents("/home/abs/facultad", $s);