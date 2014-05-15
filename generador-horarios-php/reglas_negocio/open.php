<?php
session_start();
include_once 'Facultad.php';
$u = file_get_contents("/home/abs/facultad");
$facultad = unserialize($u);
$_SESSION['facultad'] = $facultad;