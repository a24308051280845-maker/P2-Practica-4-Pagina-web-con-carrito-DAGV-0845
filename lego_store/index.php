<?php 
session_start(); 
include 'conexion.php'; 

// Detectar categoría seleccionada
$cat_actual = isset($_GET['cat']) ? $_GET['cat'] : 'todos';

if ($cat_actual == 'todos') {
    $sql = "SELECT * FROM productos";
} else {
    $sql = "SELECT * FROM productos WHERE categoria = '$cat_actual'";
}
$res = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>LEGO Store - Catálogo Completo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #e5e7eb; margin: 0; padding: 0; }
        
        /* Banner y Header Rojo */
        header { background: #e3000b; color: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; display: flex; align-items: center; gap: 8px; }
        .logo span { background: #ffcf00; color: #e3000b; padding: 4px 10px; border-radius: 4px; }
        
        .header-btns { display: flex; gap: 10px; align-items: center; }
        .btn-cart { background: #ffcf00; color: #111; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 14px; cursor: pointer; border: none; }
        .btn-auth { background: #e3000b; color: white; border: 2px solid white; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 14px; cursor: pointer; }
        .btn-dark { background: #111; color: white; border: none; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 14px; cursor: pointer; }

        /* Hero Banner */
        .hero { background: #e3000b; color: white; text-align: center; padding: 20px 20px 40px 20px; margin-bottom: 20px; }
        .hero h1 { font-size: 32px; margin: 0 0 10px 0; }
        .hero p { font-size: 16px; opacity: 0.9; margin: 0; }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px 40px 20px; }

        /* Barra de navegación de categorías y búsqueda */
        .filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .categories { display: flex; gap: 10px; flex-wrap: wrap; }
        .cat-btn { background: white; color: #333; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .cat-btn.active, .cat-btn:hover { background: #e3000b; color: white; }

        .search-box input { padding: 9px 15px; border-radius: 20px; border: 1px solid #ccc; width: 220px; outline: none; }

        /* Grid de Productos */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .card { background: white; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; }
        .card img { width: 100%; height: 160px; object-fit: contain; margin-bottom: 15px; }
        .card .tag { font-size: 11px; font-weight: bold; color: #2563eb; text-transform: uppercase; margin-bottom: 5px; }
        .card h4 { font-size: 16px; margin: 0 0 5px 0; color: #111; }
        .card .piezas { font-size: 13px; color: #6b7280; margin-bottom: 15px; }
        .card .price { font-size: 18px; font-weight: bold; color: #e3000b; margin-bottom: 15px; }
        .btn-add { background: #ffcf00; color: #111; border: none; padding: 10px; font-weight: bold; border-radius: 8px; cursor: pointer; width: 100%; transition: background 0.2s; }
        .btn-add:hover { background: #eab308; }

        /* Modales */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); justify-content: center; align-items: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-box { background: white; padding: 25px; border-radius: 10px; width: 340px; max-height: 90vh; overflow-y: auto; border-top: 5px solid #e3000b; position: relative; }
        .close { position: absolute; top: 10px; right: 15px; cursor: pointer; font-size: 20px; font-weight: bold; color: #555; }
        .form-group { margin-bottom: 12px; text-align: left; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; margin-bottom: 4px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn-submit { background: #e3000b; color: white; border: none; width: 100%; padding: 10px; font-weight: bold; cursor: pointer; border-radius: 6px; margin-top: 5px; }

        /* Estilos internos del Carrito */
        .cart-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 8px 0; font-size: 13px; }
        .btn-del { background: #ef4444; color: white; border: none; border-radius: 4px; padding: 3px 7px; cursor: pointer; font-weight: bold; }
        .cart-total { font-size: 16px; font-weight: bold; text-align: right; margin: 15px 0; color: #111; }
    </style>
</head>
<body>

    <!-- Header Rojo con Logotipo, Carrito y Login -->
    <header>
        <div class="logo"><span>LEGO</span> Store</div>
        <div class="header-btns">
            <button class="btn-cart" onclick="openCartModal()">🛒 Carrito ( <span id="cartCount">0</span> )</button>
            <?php if (isset($_SESSION['usuario_nombre'])): ?>
                <span style="font-weight:bold; color:white;">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                <a href="logout.php" class="btn-dark">Salir</a>
            <?php else: ?>
                <button class="btn-auth" onclick="openModal('loginTab')">🔓 Iniciar Sesión</button>
                <button class="btn-dark" onclick="openModal('registerTab')">👤 Registrarse</button>
            <?php endif; ?>
        </div>
    </header>

    <!-- Banner Principal -->
    <div class="hero">
        <h1>¡Construye tus Sueños con Lego! 🧱</h1>
        <p>Explora todos nuestros sets exclusivos conectados con MySQL y XAMPP.</p>
    </div>

    <div class="container">
        <!-- Filtros de Categorías -->
        <div class="filter-bar">
            <div class="categories">
                <a href="index.php?cat=todos" class="cat-btn <?php echo ($cat_actual=='todos')?'active':''; ?>">Todos</a>
                <a href="index.php?cat=starwars" class="cat-btn <?php echo ($cat_actual=='starwars')?'active':''; ?>">Star Wars</a>
                <a href="index.php?cat=city" class="cat-btn <?php echo ($cat_actual=='city')?'active':''; ?>">City</a>
                <a href="index.php?cat=technic" class="cat-btn <?php echo ($cat_actual=='technic')?'active':''; ?>">Technic</a>
                <a href="index.php?cat=harrypotter" class="cat-btn <?php echo ($cat_actual=='harrypotter')?'active':''; ?>">Harry Potter</a>
                <a href="index.php?cat=marvel" class="cat-btn <?php echo ($cat_actual=='marvel')?'active':''; ?>">Marvel</a>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" onkeyup="filtrarProductos()" placeholder="🔍 Buscar set de Lego...">
            </div>
        </div>

        <!-- Catálogo Dinámico -->
        <div class="grid" id="productGrid">
            <?php while($p = mysqli_fetch_assoc($res)): ?>
            <div class="card">
                <div>
                    <img src="<?php echo $p['imagen']; ?>" alt="<?php echo $p['nombre']; ?>">
                    <div class="tag"><?php echo strtoupper($p['categoria']); ?></div>
                    <h4><?php echo $p['nombre']; ?></h4>
                    <div class="piezas">🧩 <?php echo $p['piezas']; ?> piezas</div>
                </div>
                <div>
                    <div class="price">$<?php echo $p['precio']; ?></div>
                    <button class="btn-add" onclick="agregarAlCarrito('<?php echo addslashes($p['nombre']); ?>', <?php echo $p['precio']; ?>)">🛒 Añadir al Carrito</button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal Formulario Auth -->
    <div class="modal" id="authModal">
        <div class="modal-box">
            <span class="close" onclick="closeModal('authModal')">&times;</span>
            <div id="loginTab">
                <h3>Iniciar Sesión</h3>
                <form action="login.php" method="POST">
                    <div class="form-group"><label>Correo</label><input type="email" name="email" required></div>
                    <div class="form-group"><label>Contraseña</label><input type="password" name="password" required></div>
                    <button type="submit" class="btn-submit">Ingresar</button>
                </form>
            </div>
            <div id="registerTab" style="display:none;">
                <h3>Registro</h3>
                <form action="registro.php" method="POST">
                    <div class="form-group"><label>Nombre</label><input type="text" name="nombre" required></div>
                    <div class="form-group"><label>Correo</label><input type="email" name="email" required></div>
                    <div class="form-group"><label>Contraseña</label><input type="password" name="password" required></div>
                    <button type="submit" class="btn-submit">Registrar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Carrito y Método de Pago -->
    <div class="modal" id="cartModal">
        <div class="modal-box" style="width: 380px;">
            <span class="close" onclick="closeModal('cartModal')">&times;</span>
            <h3>🛒 Tu Carrito de Compras</h3>
            
            <div id="cartItemsList"></div>
            <div class="cart-total">Total a pagar: $<span id="cartTotal">0.00</span></div>

            <!-- Formulario de Pago (se muestra solo si hay productos) -->
            <form id="checkoutForm" action="compra_exitosa.php" method="POST" style="display:none; border-top: 2px solid #eee; padding-top: 15px;">
                <h4 style="margin: 0 0 10px 0; color: #e3000b;">💳 Método de Pago</h4>
                
                <input type="hidden" name="total_pagado" id="inputTotalPagado">
                <input type="hidden" name="cantidad_articulos" id="inputCantidadArticulos">

                <div class="form-group">
                    <label>Nombre completo del titular</label>
                    <input type="text" name="titular" placeholder="Ej. Juan Pérez" required>
                </div>

                <div class="form-group">
                    <label>Selecciona Método de Pago</label>
                    <select name="metodo_pago" id="metodoPago" onchange="toggleCamposTarjeta()" required>
                        <option value="Tarjeta de Crédito / Débito">Tarjeta de Crédito / Débito</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Transferencia / OXXO">Transferencia / Pago en OXXO</option>
                    </select>
                </div>

                <div id="datosTarjeta">
                    <div class="form-group">
                        <label>Número de Tarjeta (16 dígitos)</label>
                        <input type="text" name="num_tarjeta" maxlength="16" placeholder="4532 0151 1283 0366" required>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Vencimiento</label>
                            <input type="text" name="vencimiento" placeholder="MM/AA" maxlength="5" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>CVV</label>
                            <input type="password" name="cvv" placeholder="123" maxlength="3" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit" onclick="limpiarCarritoAlPagar()">✅ Confirmar y Pagar</button>
            </form>
        </div>
    </div>

    <script>
        // --- LÓGICA DE MODALES ---
        function openModal(tab) {
            document.getElementById('authModal').classList.add('active');
            document.getElementById('loginTab').style.display = tab === 'loginTab' ? 'block' : 'none';
            document.getElementById('registerTab').style.display = tab === 'registerTab' ? 'block' : 'none';
        }
        function closeModal(id) { 
            document.getElementById(id).classList.remove('active'); 
        }

        // --- LÓGICA DEL CARRITO (localStorage para que no se borre al cambiar categoría) ---
        let carrito = JSON.parse(localStorage.getItem('lego_carrito')) || [];
        actualizarContador();

        function agregarAlCarrito(nombre, precio) {
            carrito.push({ nombre: nombre, precio: parseFloat(precio) });
            localStorage.setItem('lego_carrito', JSON.stringify(carrito));
            actualizarContador();
            alert('🧱 "' + nombre + '" agregado al carrito.');
        }

        function eliminarDelCarrito(index) {
            carrito.splice(index, 1);
            localStorage.setItem('lego_carrito', JSON.stringify(carrito));
            actualizarContador();
            renderizarCarrito();
        }

        function actualizarContador() {
            document.getElementById('cartCount').innerText = carrito.length;
        }

        function openCartModal() {
            renderizarCarrito();
            document.getElementById('cartModal').classList.add('active');
        }

        function renderizarCarrito() {
            const lista = document.getElementById('cartItemsList');
            const form = document.getElementById('checkoutForm');
            lista.innerHTML = '';
            let total = 0;

            if (carrito.length === 0) {
                lista.innerHTML = '<p style="text-align:center; color:#6b7280; font-size:14px;">Tu carrito está vacío.</p>';
                form.style.display = 'none';
            } else {
                carrito.forEach((item, index) => {
                    total += item.precio;
                    lista.innerHTML += `
                        <div class="cart-item">
                            <span><strong>${item.nombre}</strong></span>
                            <span>$${item.precio.toFixed(2)} <button class="btn-del" onclick="eliminarDelCarrito(${index})">X</button></span>
                        </div>
                    `;
                });
                form.style.display = 'block';
            }

            document.getElementById('cartTotal').innerText = total.toFixed(2);
            document.getElementById('inputTotalPagado').value = total.toFixed(2);
            document.getElementById('inputCantidadArticulos').value = carrito.length;
        }

        function toggleCamposTarjeta() {
            const metodo = document.getElementById('metodoPago').value;
            const campos = document.getElementById('datosTarjeta');
            const inputs = campos.querySelectorAll('input');

            if (metodo === 'Tarjeta de Crédito / Débito') {
                campos.style.display = 'block';
                inputs.forEach(i => i.required = true);
            } else {
                campos.style.display = 'none';
                inputs.forEach(i => i.required = false);
            }
        }

        function limpiarCarritoAlPagar() {
            const form = document.getElementById('checkoutForm');
            if (form.checkValidity()) {
                localStorage.removeItem('lego_carrito');
            }
        }

        // --- BUSCADOR EN TIEMPO REAL ---
        function filtrarProductos() {
            const texto = document.getElementById('searchInput').value.toLowerCase();
            const tarjetas = document.querySelectorAll('#productGrid .card');
            tarjetas.forEach(card => {
                const titulo = card.querySelector('h4').innerText.toLowerCase();
                card.style.display = titulo.includes(texto) ? 'flex' : 'none';
            });
        }
    </script>
</body>
</html>