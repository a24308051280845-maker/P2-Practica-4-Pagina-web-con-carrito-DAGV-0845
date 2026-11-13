<?php
$conexion = @mysqli_connect("127.0.0.1", "root", "", "tienda_lego");

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>