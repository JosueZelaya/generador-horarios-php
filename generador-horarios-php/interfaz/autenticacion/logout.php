<?php

chdir(dirname(__FILE__));
require_once '../../reglas_negocio/ManejadorSesion.php';
ManejadorSesion::cerrarSesion();
header("location: ../index.php");

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

