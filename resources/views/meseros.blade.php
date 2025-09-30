{{-- resources/views/meseros.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Meseros</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Token CSRF de Laravel para PUT/POST/DELETE con fetch --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root { --bg:#0b1220; --card:#111a2b; --text:#e8eefc; --muted:#a9b2c7; --primary:#47a3ff; --success:#35c38a; --warn:#ffc857; }
    *{box-sizing:border-box} body{margin:0;background:var(--bg);color:var(--text);font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Helvetica Neue",Arial}
    a{color:var(--primary)}
    .topbar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #1b2741;background:#0c1526;position:sticky;top:0;z-index:10}
    .topbar h1{font-size:18px;margin:0}
    .user{text-align:right;font-size:14px;color:var(--muted)}
    .wrap{max-width:1100px;margin:24px auto;padding:0 20px}
    .filters{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px}
    .btn{border:1px solid #2a3b61;background:transparent;color:var(--text);padding:8px 12px;border-radius:10px;cursor:pointer}
    .btn:hover{border-color:var(--primary);color:var(--primary)}
    .btn.primary{background:var(--primary);border-color:var(--primary);color:#031024}
    .btn.success{background:var(--success);border-color:var(--success);color:#031024}
    .btn.warn{background:var(--warn);border-color:var(--warn);color:#1a1200}
    .filters .btn.active{background:var(--primary);border-color:var(--primary);color:#031024}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px}
    .card{background:var(--card);border:1px solid #1b2741;border-radius:14px;padding:14px;display:flex;gap:10px;flex-direction:column}
    .muted{color:var(--muted);font-size:13px}
    .row{display:flex;gap:8px;flex-wrap:wrap}
    .pill{font-size:12px;border:1px solid #2a3b61;padding:3px 8px;border-radius:999px;color:var(--muted)}
    .status{font-size:12px;padding:3px 8px;border-radius:999px;border:1px solid}
    .status.pendiente{border-color:#7a7f8a;color:#7a7f8a}
    .status.listo{border-color:var(--primary);color:var(--primary)}
    .status.en_entrega{border-color:var(--warn);color:var(--warn)}
    .status.entregado{border-color:var(--success);color:var(--success)}
    .empty{padding:40px;text-align:center;color:var(--muted);border:1px dashed #233358;border-radius:12px}
    /* Modal */
    .modal{position:fixed;inset:0;background:rgba(0,0,0,.55);display:none;align-items:center;justify-content:center;padding:20px}
    .modal.show{display:flex}
    .panel{background:#0c1526;border:1px solid #1b2741;border-radius:16px;max-width:820px;width:100%;max-height:90vh;overflow:auto}
    .panel .hd{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #1b2741}
    .panel .bd{padding:16px}
    table{width:100%;border-collapse:collapse;border:1px solid #1b2741;border-radius:10px;overflow:hidden}
    th,td{padding:10px;text-align:left;border-bottom:1px solid #1b2741;color:var(--text)}
    th{background:#0f1a2e}
    .toast{position:fixed;right:16px;bottom:16px;background:#0f1a2e;border:1px solid #24406b;padding:10px 12px;border-radius:10px;color:var(--text);display:none}
    .toast.show{display:block}
  </style>
</head>
<body>

  <div class="topbar">
    <h1>Meseros · Pedidos</h1>
    <div class="user">
      @php $u = session('user'); @endphp
      <div><strong>{{ $u['nombre'] ?? 'Mesero' }}</strong></div>
      <div class="muted">{{ strtoupper($u['rol'] ?? '-') }}</div>
    </div>
  </div>

  <div class="wrap">
    <div class="filters">
      <button class="btn" data-f="pendiente">Pendientes</button>
      <button class="btn" data-f="listo">Listos</button>
      <button class="btn" data-f="en_entrega">En entrega</button>
      <button class="btn" data-f="entregado">Entregados</button>
      <button class="btn primary" id="reload">Actualizar</button>
    </div>

    <div id="grid" class="grid"></div>

    <div id="empty" class="empty" style="display:none;">No hay pedidos para mostrar.</div>
  </div>

  {{-- Modal detalle --}}
  <div id="modal" class="modal" aria-hidden="true">
    <div class="panel">
      <div class="hd">
        <div>
          <strong>Pedido <span id="m_id"></span></strong>
          <span id="m_status" class="status" style="margin-left:8px"></span>
        </div>
        <button class="btn" id="m_close">Cerrar ✕</button>
      </div>
      <div class="bd">
        <div class="row" style="margin-bottom:8px">
          <div class="pill"><strong>Cliente:</strong> <span id="m_cliente">-</span></div>
          <div class="pill"><strong>Mesa:</strong> <span id="m_mesa">-</span></div>
          <div class="pill"><strong>Fecha:</strong> <span id="m_fecha">-</span></div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>Precio</th>
              <th>Subtotal</th>
              <th>Notas</th>
            </tr>
          </thead>
          <tbody id="m_items"></tbody>
          <tfoot>
            <tr>
              <td colspan="3" style="text-align:right"><strong>Total:</strong></td>
              <td id="m_total" colspan="2"></td>
            </tr>
          </tfoot>
        </table>

        <div class="row" style="margin-top:14px">
          <button id="btn_enentrega" class="btn warn">Marcar “En entrega”</button>
          <button id="btn_entregado" class="btn success">Marcar “Entregado”</button>
          <button id="btn_imprimir" class="btn">Imprimir</button>
        </div>
      </div>
    </div>
  </div>

  <div id="toast" class="toast"></div>

<script>
const API_BASE = '/api';
const grid   = document.getElementById('grid');
const empty  = document.getElementById('empty');
const emptyDefault = empty ? empty.textContent : '';
const toast  = document.getElementById('toast');

const modal  = document.getElementById('modal');
const mClose = document.getElementById('m_close');
const mId    = document.getElementById('m_id');
const mStatus= document.getElementById('m_status');
const mCliente=document.getElementById('m_cliente');
const mMesa  = document.getElementById('m_mesa');
const mFecha = document.getElementById('m_fecha');
const mItems = document.getElementById('m_items');
const mTotal = document.getElementById('m_total');
const btnEnEntrega = document.getElementById('btn_enentrega');
const btnEntregado = document.getElementById('btn_entregado');
const btnImprimir  = document.getElementById('btn_imprimir');

const filterButtons = Array.from(document.querySelectorAll('.filters .btn[data-f]'));
let currentEstado = 'pendiente';
let currentPedidoId = null;
let csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

filterButtons.forEach(b=>{
  b.addEventListener('click', ()=>{
    currentEstado = b.dataset.f;
    setActiveFilter();
    loadPedidos();
  });
});

function setActiveFilter(){
  filterButtons.forEach(btn=>{
    btn.classList.toggle('active', btn.dataset.f === currentEstado);
  });
}

document.getElementById('reload').addEventListener('click', ()=> loadPedidos());

mClose.addEventListener('click', ()=> modal.classList.remove('show'));
btnImprimir.addEventListener('click', ()=> window.print());

btnEnEntrega.addEventListener('click', ()=> updateEstado('en_entrega'));
btnEntregado.addEventListener('click', ()=> updateEstado('entregado'));

function showToast(msg){
  toast.textContent = msg;
  toast.classList.add('show');
  setTimeout(()=> toast.classList.remove('show'), 2500);
}

function statusPill(estado){
  if(!estado) return `<span class="status pendiente">-</span>`;
  return `<span class="status ${estado}">${estado.replace('_',' ')}</span>`;
}

function formatFecha(valor){
  if(!valor) return '-';
  try {
    const date = new Date(valor);
    if(Number.isNaN(date.getTime())) return valor;
    return date.toLocaleString('es-CO', { hour12: false });
  } catch (e) {
    return valor;
  }
}

async function loadPedidos(){
  grid.innerHTML = '';
  empty.style.display = 'none';
  try{
    const res = await fetch(`${API_BASE}/pedidos?estado=${encodeURIComponent(currentEstado)}`, {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    if(!res.ok) throw new Error(data?.message || `HTTP ${res.status}`);
    const items = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);

    if(!items.length){
      empty.textContent = emptyDefault;
      empty.style.display = 'block';
      return;
    }

    items.forEach(p => {
      const fecha = p.fecha ?? p.created_at ?? null;
      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML = `
        <div class="row" style="justify-content:space-between">
          <div><strong>#${p.id}</strong></div>
          <div>${statusPill(p.estado || 'pendiente')}</div>
        </div>
        <div class="muted">Cliente: ${p.cliente?.nombre_cliente ?? '-'}</div>
        <div class="row">
          <span class="pill">Mesa: ${p.mesa ?? '-'}</span>
          <span class="pill">Fecha: ${fecha ? formatFecha(fecha) : '-'}</span>
        </div>
        <div class="row">
          <button class="btn primary" data-ver="${p.id}">Ver detalle</button>
          ${p.estado !== 'entregado' ? `<button class="btn success" data-ent="${p.id}">Entregado</button>` : ``}
        </div>
      `;
      grid.appendChild(card);
    });

    grid.querySelectorAll('[data-ver]').forEach(btn=>{
      btn.addEventListener('click', ()=> openPedido(parseInt(btn.dataset.ver,10)));
    });
    grid.querySelectorAll('[data-ent]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        await quickUpdate(parseInt(btn.dataset.ent,10), 'entregado');
        loadPedidos();
      });
    });

  }catch(e){
    console.error(e);
    showToast('Error cargando pedidos');
    if(empty){
      empty.textContent = 'No se pudieron cargar los pedidos en este momento.';
      empty.style.display = 'block';
    }
  }
}

async function openPedido(id){
  try{
    const [pRes, dRes] = await Promise.all([
      fetch(`${API_BASE}/pedidos/${id}`, { headers: { 'Accept': 'application/json' } }),
      fetch(`${API_BASE}/pedidos/${id}/detalle`, { headers: { 'Accept': 'application/json' } })
    ]);
    const pdata = await pRes.json();
    const ddata = await dRes.json();

    if(!pRes.ok) throw new Error(pdata?.message || `HTTP ${pRes.status}`);
    if(!dRes.ok) throw new Error(ddata?.message || `HTTP ${dRes.status}`);

    const pedido = pdata.data ?? pdata;
    const detalle = ddata.data ?? ddata;

    currentPedidoId = pedido.id;
    mId.textContent = pedido.id;
    mStatus.className = `status ${pedido.estado}`;
    mStatus.textContent = (pedido.estado || '').replace('_',' ');
    mCliente.textContent = pedido.cliente?.nombre_cliente ?? '-';
    mMesa.textContent = pedido.mesa ?? '-';
    const fechaPedido = pedido.fecha ?? pedido.created_at ?? null;
    mFecha.textContent = fechaPedido ? formatFecha(fechaPedido) : '-';

    // Render detalle
    let total = 0;
    mItems.innerHTML = (detalle || []).map(it=>{
      const cant = Number(it.cantidad || 0);
      const precioUnit = Number(it.precio ?? it.precio_unitario ?? 0);
      const importe = Number(it.importe ?? it.subtotal ?? NaN);
      const sub = Number.isFinite(importe) ? importe : cant * precioUnit;
      const nombre = it.nombre_producto ?? it.nombre ?? it.menu_item?.nombre ?? '-';
      total += sub;
      return `
        <tr>
          <td>${nombre}</td>
          <td>${cant}</td>
          <td>${precioUnit.toFixed(2)}</td>
          <td>${sub.toFixed(2)}</td>
          <td>${it.descripcion ?? ''}</td>
        </tr>`;
    }).join('');

    mTotal.textContent = total.toFixed(2);

    // Mostrar/ocultar botones por estado
    const e = (pedido.estado || '').toLowerCase();
    btnEnEntrega.style.display = (e === 'listo' || e === 'pendiente') ? 'inline-block' : 'none';
    btnEntregado.style.display = (e === 'en_entrega' || e === 'listo') ? 'inline-block' : 'none';

    modal.classList.add('show');

  }catch(e){
    console.error(e);
    showToast('No se pudo abrir el pedido');
  }
}

async function updateEstado(nuevo){
  if(!currentPedidoId) return;
  try{
    const res = await fetch(`${API_BASE}/pedidos/${currentPedidoId}`,{
      method:'PUT',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept':'application/json'
      },
      body: JSON.stringify({ estado: nuevo })
    });
    if(!res.ok) throw new Error('HTTP '+res.status);
    showToast(`Estado actualizado a "${nuevo}"`);
    modal.classList.remove('show');
    loadPedidos();
  }catch(e){
    console.error(e);
    showToast('No se pudo actualizar el estado');
  }
}

async function quickUpdate(id, nuevo){
  try{
    const res = await fetch(`${API_BASE}/pedidos/${id}`,{
      method:'PUT',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept':'application/json'
      },
      body: JSON.stringify({ estado: nuevo })
    });
    if(!res.ok) throw new Error('HTTP '+res.status);
    showToast(`Pedido #${id} → ${nuevo}`);
  }catch(e){
    console.error(e);
    showToast('Error al actualizar');
  }
}

// Carga inicial
setActiveFilter();
loadPedidos();
</script>
</body>
</html>
