document.addEventListener('DOMContentLoaded', () => {
    const carrito = document.querySelector('#carrito tbody');
    const listaProductos = document.querySelectorAll('.agregar-carrito');
    const vaciarBtn = document.querySelector('#vaciar-carrito');
    const enviarBtn = document.querySelector('#enviar-pedido');
    const modal = document.querySelector('#modal-cliente');
    const closeModal = document.querySelector('.close-modal');
    const formCliente = document.querySelector('#form-cliente');
    let carritoItems = JSON.parse(localStorage.getItem('carrito')) || [];

    // Cargar el carrito al abrir la página
    actualizarCarrito();

    // Escuchar cambios en el localStorage
    window.addEventListener('storage', (e) => {
        if (e.key === 'carrito') {
            carritoItems = JSON.parse(e.newValue);
            actualizarCarrito();
        }
    });

    listaProductos.forEach(btn => {
        btn.addEventListener('click', agregarProducto);
    });

    vaciarBtn.addEventListener('click', (e) => {
        e.preventDefault();
        carritoItems = [];
        localStorage.setItem('carrito', JSON.stringify(carritoItems));
        actualizarCarrito();
    });

    enviarBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (carritoItems.length === 0) {
            alert('El carrito está vacío.');
            return;
        }
        modal.style.display = 'block';
    });

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Cerrar el modal haciendo clic fuera de él
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Agregar la posibilidad de añadir descripción a los productos
    const agregarDescripcion = (index) => {
        const descripcion = prompt('Agrega instrucciones especiales para este producto:', 
                                  carritoItems[index].descripcion || '');
        if (descripcion !== null) {
            carritoItems[index].descripcion = descripcion;
            localStorage.setItem('carrito', JSON.stringify(carritoItems));
            actualizarCarrito();
        }
    };

    formCliente.addEventListener('submit', (e) => {
        e.preventDefault();
        const nombre = document.querySelector('#nombre').value;
        const telefono = document.querySelector('#telefono').value;
        const direccion = document.querySelector('#direccion').value;

        // Verificar si los campos obligatorios están completos
        if (!nombre || !telefono) {
            alert('Por favor, complete los campos obligatorios.');
            return;
        }

        // Mostrar mensaje de carga
        const submitBtn = formCliente.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Enviando...';
        submitBtn.disabled = true;

        // Enviar datos del pedido
        fetch('guardar_pedido.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nombre,
                telefono,
                direccion,
                pedido: carritoItems
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(`Pedido #${data.id_pedido} enviado correctamente.`);
                modal.style.display = 'none';
                carritoItems = [];
                localStorage.setItem('carrito', JSON.stringify(carritoItems));
                actualizarCarrito();
                formCliente.reset();
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            alert('Error al enviar el pedido. Intente nuevamente.');
            console.error(error);
        })
        .finally(() => {
            // Restaurar el botón
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });

    function agregarProducto(e) {
        e.preventDefault();
        const producto = e.target.closest('.product, .ofert-1');
        const nombre = producto.querySelector('h3').textContent;
        const precioTexto = producto.querySelector('.precio') ? producto.querySelector('.precio').textContent : '0';
        const precio = parseInt(precioTexto.replace(/[^\d]/g, '')) || 0;
        const imagen = producto.querySelector('img').src;
        const categoria = producto.getAttribute('data-categoria');
        
        // Comprobar si se requieren instrucciones especiales
        const descripcion = ''; // Inicialmente vacío
        
        const item = { nombre, precio, imagen, categoria, descripcion };
        carritoItems.push(item);
        localStorage.setItem('carrito', JSON.stringify(carritoItems));
        actualizarCarrito();
        
        // Mensaje de confirmación
        const mensajeConfirmacion = document.createElement('div');
        mensajeConfirmacion.className = 'mensaje-confirmacion';
        mensajeConfirmacion.textContent = `¡${nombre} agregado al carrito!`;
        document.body.appendChild(mensajeConfirmacion);
        
        // Eliminar el mensaje después de 2 segundos
        setTimeout(() => {
            mensajeConfirmacion.remove();
        }, 2000);
    }

    function actualizarCarrito() {
        carrito.innerHTML = '';
        
        carritoItems.forEach((item, index) => {
            const row = document.createElement('tr');
            
            // Añadir la descripción si existe
            const descripcionCorta = item.descripcion && item.descripcion.length > 20 
                ? item.descripcion.substring(0, 20) + '...' 
                : item.descripcion || '';
                
            row.innerHTML = `
                <td><img src="${item.imagen}" width="50"></td>
                <td>${item.nombre}</td>
                <td>${item.categoria}</td>
                <td>${item.precio} COP</td>
                <td>
                    <a href="#" class="btn-descripcion" data-index="${index}" title="Agregar instrucciones especiales">
                        <small>${descripcionCorta}</small> ✏️
                    </a>
                </td>
                <td><a href="#" class="eliminar" data-index="${index}">X</a></td>
            `;
            carrito.appendChild(row);
        });

        // Actualizar botón del carrito con cantidad
        const cantidadProductos = carritoItems.length;
        const imgCarrito = document.querySelector('#img-carrito');
        if (imgCarrito && cantidadProductos > 0) {
            // Si existe un badge anterior, lo eliminamos
            const badgeAnterior = document.querySelector('.carrito-cantidad');
            if (badgeAnterior) badgeAnterior.remove();
            
            // Crear badge con la cantidad
            const badge = document.createElement('span');
            badge.className = 'carrito-cantidad';
            badge.textContent = cantidadProductos;
            badge.style.position = 'absolute';
            badge.style.top = '-8px';
            badge.style.right = '-8px';
            badge.style.backgroundColor = 'red';
            badge.style.color = 'white';
            badge.style.borderRadius = '50%';
            badge.style.width = '20px';
            badge.style.height = '20px';
            badge.style.display = 'flex';
            badge.style.justifyContent = 'center';
            badge.style.alignItems = 'center';
            badge.style.fontSize = '12px';
            
            // Asegurar que el elemento padre tenga posición relativa
            imgCarrito.parentElement.style.position = 'relative';
            imgCarrito.parentElement.appendChild(badge);
        }

        // Añadir eventos a los botones
        document.querySelectorAll('.eliminar').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const index = e.target.dataset.index;
                carritoItems.splice(index, 1);
                localStorage.setItem('carrito', JSON.stringify(carritoItems));
                actualizarCarrito();
            });
        });
        
        // Añadir eventos a los botones de descripción
        document.querySelectorAll('.btn-descripcion').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const index = e.target.closest('.btn-descripcion').dataset.index;
                agregarDescripcion(index);
            });
        });
        
        // Calcular y mostrar el total
        const total = carritoItems.reduce((acc, item) => acc + item.precio, 0);
        let rowTotal = document.createElement('tr');
        rowTotal.className = 'carrito-total';
        rowTotal.innerHTML = `
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>${total} COP</strong></td>
            <td colspan="2"></td>
        `;
        carrito.appendChild(rowTotal);
    }
});