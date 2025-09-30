<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Ordenar - Menú</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu; margin:0; padding:16px; background:#f7f7f8;}
    header{display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;}
    h1{font-size:20px; margin:0;}
    .grid{display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:12px;}
    .card{background:#fff; border-radius:12px; padding:12px; box-shadow:0 1px 3px rgba(0,0,0,.08);}
    .name{font-weight:600; margin:0 0 6px;}
    .muted{color:#555; font-size:13px;}
    .price{font-weight:700; margin-top:6px;}
    .row{display:flex; gap:8px; align-items:center; margin-top:8px;}
    button{cursor:pointer; border:0; border-radius:8px; padding:8px 10px; background:#111827; color:#fff;}
    button.secondary{background:#e5e7eb; color:#111827;}
    .cart{position:sticky; bottom:0; left:0; right:0; background:#fff; border-top:1px solid #e5e7eb; padding:12px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;}
    input, select{padding:8px; border-radius:8px; border:1px solid #d1d5db;}
    .tag{display:inline-block; font-size:12px; background:#eef2ff; color:#4338ca; padding:2px 8px; border-radius:999px;}
  </style>
</head>
<body>
  <header>
    <h1>Menú del restaurante</h1>
    <div>
      <span class="tag">Mesa: <strong id="mesa-tag">{{ $mesa ?? 'N/A' }}</strong></span>
    </div>
  </header>

  <section class="row" style="margin-bottom:12px;">
    <input id="search" type="text" placeholder="Buscar..." />
    <select id="filtro-categoria">
      <option value="">Todas las categorías</option>
      <option value="plato">Platos</option>
      <option value="bebida">Bebidas</option>
      <option value="postre">Postres</option>
    </select>
    <button class="secondary" id="btn-filtrar">Filtrar</button>
  </section>

  <section id="menu" class="grid"></section>

  <div class="cart">
    <div>Cliente ID: <input id="cliente_id" type="number" placeholder="Ej: 12" style="width:110px"></div>
    <div>Mesa: <input id="mesa" type="text" value="{{ $mesa }}" placeholder="Ej: A1" style="width:80px"></div>
    <div>Total ítems: <strong id="total-items">0</strong></div>
    <button id="btn-ver-carrito" class="secondary">Ver carrito</button>
    <button id="btn-enviar">Enviar pedido</button>
  </div>

  <script>
    const $menu = document.getElementById('menu');
    const $search = document.getElementById('search');
    const $cat = document.getElementById('filtro-categoria');
    const $btnFiltrar = document.getElementById('btn-filtrar');
    const $totalItems = document.getElementById('total-items');
    const $btnEnviar = document.getElementById('btn-enviar');
    const $btnVerCarrito = document.getElementById('btn-ver-carrito');
    const $mesaInput = document.getElementById('mesa');
    const $mesaTag = document.getElementById('mesa-tag');
    const $clienteId = document.getElementById('cliente_id');

    // Carrito simple en memoria
    const cart = []; // { menu_item_id, nombre, precio, categoria, descripcion, cantidad }

    function renderItems(items){
      $menu.innerHTML = '';
      if (!items || !items.length){
        $menu.innerHTML = '<p>No hay resultados.</p>';
        return;
      }
      for (const it of items){
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
          <p class="name">${it.nombre}</p>
          <p class="muted">${it.descripcion ?? ''}</p>
          <p class="muted">Categoría: ${it.categoria ?? '-'}</p>
          <p class="price">$ ${Number(it.precio).toFixed(2)}</p>
          <div class="row">
            <button
              data-id="${it.id}"
              data-nombre="${it.nombre}"
              data-precio="${it.precio}"
              data-categoria="${it.categoria ?? ''}"
              data-descripcion="${it.descripcion ?? ''}"
            >Agregar</button>
          </div>
        `;
        card.querySelector('button').addEventListener('click', (e)=>{
          const id = parseInt(e.target.dataset.id,10);
          const nombre = e.target.dataset.nombre;
          const precio = Number(e.target.dataset.precio ?? 0);
          const categoria = e.target.dataset.categoria || '';
          const descripcion = e.target.dataset.descripcion || null;
          const found = cart.find(x=>x.menu_item_id===id);
          if (found) found.cantidad += 1;
          else cart.push({ menu_item_id: id, nombre, precio, categoria, descripcion, cantidad: 1 });
          $totalItems.textContent = cart.reduce((a,b)=>a+b.cantidad,0);
        });
        $menu.appendChild(card);
      }
    }

    async function loadMenu(){
      const params = new URLSearchParams();
      if ($search.value.trim()) params.set('q', $search.value.trim());
      if ($cat.value) params.set('categoria', $cat.value);
      // tu API ya maneja GET /api/menu-items con filtros básicos
      const res = await fetch('/api/menu-items?'+params.toString());
      const json = await res.json();
      // soporta tanto {data:[...]} como [...]
      const items = Array.isArray(json) ? json : (json.data ?? []);
      renderItems(items);
    }

    $btnFiltrar.addEventListener('click', loadMenu);
    window.addEventListener('DOMContentLoaded', loadMenu);

    $btnVerCarrito.addEventListener('click', ()=>{
      if (!cart.length) return alert('El carrito está vacío.');
      const lines = cart.map(x=>`• ${x.nombre} x${x.cantidad}`).join('\n');
      alert('Carrito:\n'+lines);
    });

    $btnEnviar.addEventListener('click', async ()=>{
      const cliente_id = parseInt($clienteId.value,10);
      const mesa = $mesaInput.value.trim();
      if (!cliente_id) return alert('Debes indicar el ID del cliente.');
      if (!cart.length) return alert('El carrito está vacío.');

      // payload para tu POST /api/pedidos
      const payload = {
        cliente_id,
        mesa: mesa || null,
        items: cart.map(x=>({
          menu_item_id: x.menu_item_id,
          cantidad: x.cantidad,
          precio: x.precio,
        }))
      };

      try{
        const res = await fetch('/api/pedidos', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept':'application/json' },
          body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (!res.ok){
          const validation = json?.errors ? Object.values(json.errors).flat().join('\n') : null;
          const message = validation || json.message || JSON.stringify(json);
          return alert('Error: '+message);
        }
        cart.length = 0;
        $totalItems.textContent = '0';
        alert('¡Pedido creado! ID: '+ (json?.data?.id ?? '(desconocido)'));
      }catch(err){
        alert('Error de red: '+err.message);
      }
    });

    // sincroniza etiqueta de mesa con input
    $mesaInput.addEventListener('input', ()=>{ $mesaTag.textContent = $mesaInput.value || 'N/A'; });
  </script>
</body>
</html>
