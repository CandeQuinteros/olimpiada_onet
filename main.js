document.addEventListener('DOMContentLoaded', () => {
    const productosList = document.getElementById('listaProductos');
    const carritoItems = document.getElementById('carritoItems');
    const totalElement = document.getElementById('total');
    const finalizarCompraBtn = document.getElementById('finalizarCompra');
    const resumenCompra = document.getElementById('resumenCompra');
    const resumenItems = document.getElementById('resumenItems');
    const volverCarritoBtn = document.getElementById('volverCarrito');
    let carrito = [];

    // Definir los productos disponibles
    const productos = [
        { id: 1, descripcion: 'Zapatillas Deportivas', detalles: 'Alta calidad y confort para tus entrenamientos.', precio: 50000, imagen: 'zapatillas.jpg' },
        { id: 2, descripcion: 'Camiseta Deportiva', detalles: 'Ideal para todo tipo de deportes.', precio: 80000, imagen: 'camiseta.jpg' },
        { id: 3, descripcion: 'Balón de Fútbol', detalles: 'Perfecto para partidos en cualquier tipo de terreno.', precio: 30000, imagen: 'balon.jpg' },
        // Agrega más productos aquí
    ];

    function cargarProductos() {
        productosList.innerHTML = productos.map(producto => `
            <div class="producto">
                <img src="${producto.imagen}" alt="${producto.descripcion}">
                <div class="producto-info">
                    <h3>${producto.descripcion}</h3>
                    <p>${producto.detalles}</p>
                </div>
                <div class="producto-precio">$${producto.precio}</div>
                <button class="producto-agregar" data-id="${producto.id}" data-descripcion="${producto.descripcion}" data-precio="${producto.precio}">Agregar al Carrito</button>
            </div>
        `).join('');
    }

    function agregarAlCarrito(id, descripcion, precio) {
        const itemExistente = carrito.find(item => item.id === id);

        if (itemExistente) {
            itemExistente.cantidad++;
        } else {
            carrito.push({ id, descripcion, precio, cantidad: 1 });
        }
        actualizarCarrito();
    }

    function eliminarDelCarrito(id) {
        carrito = carrito.filter(item => item.id !== id);
        actualizarCarrito();
    }

    function actualizarCarrito() {
        carritoItems.innerHTML = carrito.map(item => `
            <li>
                ${item.descripcion} - $${item.precio} x ${item.cantidad} 
                <button class="producto-eliminar" data-id="${item.id}">Eliminar</button>
            </li>
        `).join('');

        const total = carrito.reduce((acc, item) => acc + item.precio * item.cantidad, 0);
        totalElement.textContent = total.toFixed(2);
    }

    function generarTablaResumen() {
        resumenItems.innerHTML = carrito.map(item => `
            <tr>
                <td>${item.descripcion}</td>
                <td>$${item.precio}</td>
                <td><input type="number" min="1" value="${item.cantidad}" data-id="${item.id}" class="cantidad-modificar"></td>
                <td>$${(item.precio * item.cantidad).toFixed(2)}</td>
                <td><button class="producto-eliminar" data-id="${item.id}">Eliminar</button></td>
            </tr>
        `).join('');

        // Crear y agregar el botón de Confirmar Compra si aún no existe
        let confirmarCompraBtn = document.getElementById('confirmarCompra');
        if (!confirmarCompraBtn) {
            confirmarCompraBtn = document.createElement('button');
            confirmarCompraBtn.id = 'confirmarCompra';
            confirmarCompraBtn.textContent = 'Confirmar Compra';
            confirmarCompraBtn.style.display = 'block';
            confirmarCompraBtn.style.marginTop = '20px';
            resumenCompra.appendChild(confirmarCompraBtn);

            confirmarCompraBtn.addEventListener('click', () => {
                registrarCompra();
            });
        }
    }

    productosList.addEventListener('click', (e) => {
        if (e.target.classList.contains('producto-agregar')) {
            const id = parseInt(e.target.dataset.id);
            const descripcion = e.target.dataset.descripcion;
            const precio = parseFloat(e.target.dataset.precio);
            agregarAlCarrito(id, descripcion, precio);
        }
    });

    carritoItems.addEventListener('click', (e) => {
        if (e.target.classList.contains('producto-eliminar')) {
            const id = parseInt(e.target.dataset.id);
            eliminarDelCarrito(id);
        }
    });

    finalizarCompraBtn.addEventListener('click', () => {
        if (carrito.length > 0) {
            generarTablaResumen();
            resumenCompra.classList.remove('oculto');
            document.getElementById('contenedor').classList.add('oculto');
        } else {
            alert('El carrito está vacío');
        }
    });

    resumenItems.addEventListener('change', (e) => {
        if (e.target.classList.contains('cantidad-modificar')) {
            const id = parseInt(e.target.dataset.id);
            const nuevaCantidad = parseInt(e.target.value);
            const item = carrito.find(item => item.id === id);
            if (item) {
                item.cantidad = nuevaCantidad;
                actualizarCarrito();
                generarTablaResumen();
            }
        }
    });

    resumenItems.addEventListener('click', (e) => {
        if (e.target.classList.contains('producto-eliminar')) {
            const id = parseInt(e.target.dataset.id);
            eliminarDelCarrito(id);
            generarTablaResumen();
        }
    });

    volverCarritoBtn.addEventListener('click', () => {
        resumenCompra.classList.add('oculto');
        document.getElementById('contenedor').classList.remove('oculto');
        actualizarCarrito();
    });

    function registrarCompra() {
        const total = carrito.reduce((acc, item) => acc + item.precio * item.cantidad, 0);

        fetch('registrar_compra.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ carrito, total })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Compra registrada como pendiente de entrega');
                  carrito = [];
                  resumenCompra.classList.add('oculto');
                  document.getElementById('contenedor').classList.remove('oculto');
                  actualizarCarrito();
              } else {
                  alert('Error al registrar la compra: ' + data.message);
              }
          });
    }

    cargarProductos();
});
