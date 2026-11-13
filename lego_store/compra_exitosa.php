<?php
session_start();

// Evitar acceso directo sin enviar formulario
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

$titular = htmlspecialchars($_POST['titular']);
$metodo = htmlspecialchars($_POST['metodo_pago']);
$total = htmlspecialchars($_POST['total_pagado']);
$articulos = htmlspecialchars($_POST['cantidad_articulos']);
$folio = "LEGO-" . rand(100000, 999999);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>LEGO Store - Compra Realizada</title>
    <style>
        body { font-family: Arial, sans-serif; background: #e5e7eb; margin: 0; padding: 0; }
        header { background: #e3000b; color: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; display: flex; align-items: center; gap: 8px; }
        .logo span { background: #ffcf00; color: #e3000b; padding: 4px 10px; border-radius: 4px; }
        
        .success-box { max-width: 500px; margin: 60px auto; background: white; padding: 35px; border-radius: 12px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.08); border-top: 6px solid #16a34a; }
        .icon-check { font-size: 55px; margin-bottom: 10px; }
        .success-box h1 { color: #16a34a; margin: 0 0 10px 0; font-size: 26px; }
        .success-box p { color: #4b5563; font-size: 15px; margin-bottom: 20px; }
        
        .ticket { background: #f9fafb; border: 1px dashed #d1d5db; padding: 15px; border-radius: 8px; text-align: left; margin-bottom: 25px; font-size: 14px; }
        .ticket div { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .ticket div:last-child { margin-bottom: 0; border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 16px; color: #e3000b; }

        .btn-back { display: inline-block; background: #ffcf00; color: #111; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 15px; }
        .btn-back:hover { background: #eab308; }
    </style>
</head>
<body>

    <header>
        <div class="logo"><span>LEGO</span> Store</div>
    </header>

    <div class="success-box">
        <div class="icon-check">✅</div>
        <h1>¡Compra Realizada con Éxito!</h1>
        <p>Gracias por tu compra, <strong><?php echo $titular; ?></strong>. Tus sets de LEGO ya se están preparando para el envío.</p>

        <div class="ticket">
            <div><span>Folio de Pedido:</span> <strong><?php echo $folio; ?></strong></div>
            <div><span>Sets adquiridos:</span> <strong><?php echo $articulos; ?> artículo(s)</strong></div>
            <div><span>Método de Pago:</span> <strong><?php echo $metodo; ?></strong></div>
            <div><span>Total Pagado:</span> <strong>$<?php echo $total; ?></strong></div>
        </div>

        <a href="index.php" class="btn-back">🧱 Seguir Comprando</a>
    </div>

</body>
</html>