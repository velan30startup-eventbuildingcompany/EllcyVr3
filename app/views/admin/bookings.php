<?php
$page_title  = 'Bookings';
$active_page = 'bookings';
require VIEWS_PATH . '/admin/layout_start.php';
?>

<div class="data-card">
  <div class="data-card-hdr">
    <div class="data-card-title"><i class="fa-solid fa-calendar-check" style="color:#6a1b9a;margin-right:8px"></i>All Bookings</div>
    <div style="display:flex;gap:8px">
      <span class="badge badge-amber"><?= (int)($status_counts['pending']??0) ?> Pending</span>
      <span class="badge badge-green"><?= (int)($status_counts['confirmed']??0) ?> Confirmed</span>
    </div>
  </div>

  <div class="filter-bar">
    <input class="filter-search" type="text" id="bkSearch"
           placeholder="Search by name, phone, ref…"
           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"/>
    <select class="form-select" style="width:auto;padding:8px 13px" id="bkStatus" onchange="applyFilters()">
      <option value="">All Status</option>
      <?php foreach (['pending','confirmed','in_progress','completed','cancelled'] as $st): ?>
      <option value="<?= $st ?>" <?= ($_GET['status']??'')===$st?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-outline btn-sm" onclick="applyFilters()">Filter</button>
  </div>

  <div style="overflow-x:auto">
  <table class="data-table">
    <thead>
      <tr>
        <th>Ref</th>
        <th>Client</th>
        <th>Event</th>
        <th>Date</th>
        <th>Total</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td><strong style="color:#6a1b9a"><?= htmlspecialchars($o['order_ref']) ?></strong></td>
        <td>
          <div style="font-weight:600"><?= htmlspecialchars($o['name']) ?></div>
          <div style="font-size:.76rem;color:#888"><?= htmlspecialchars($o['phone']) ?></div>
        </td>
        <td style="font-size:.83rem"><?= htmlspecialchars($o['event_type'] ?: '—') ?></td>
        <td style="font-size:.83rem;white-space:nowrap">
          <?= $o['event_date'] ? date('d M Y', strtotime($o['event_date'])) : '—' ?>
        </td>
        <td style="font-weight:700">
          <?= $o['total'] > 0 ? '₹'.number_format($o['total']) : '—' ?>
        </td>
        <td>
          <span class="badge badge-<?= match($o['status']){
            'confirmed'=>'green','completed'=>'blue','cancelled'=>'red','in_progress'=>'purple',default=>'amber'
          } ?>">
            <?= ucfirst(str_replace('_',' ',$o['status'])) ?>
          </span>
        </td>
        <td>
          <button class="btn btn-sm btn-outline" onclick="showOrderModal(<?= htmlspecialchars(json_encode($o), ENT_QUOTES) ?>)">
            <i class="fa-solid fa-eye"></i>
          </button>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($orders)): ?>
      <tr><td colspan="7" style="text-align:center;color:#aaa;padding:32px">No bookings found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>

  <?php if ($total_pages > 1): ?>
  <div class="pagination">
    <?php for ($p=1;$p<=$total_pages;$p++): ?>
    <a href="?page=<?=$p?>&q=<?=urlencode($_GET['q']??'')?>&status=<?=urlencode($_GET['status']??'')?>"
       class="page-btn <?=$p===$current_page?'active':''?>"><?=$p?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ORDER DETAIL MODAL -->
<div id="orderModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;overflow-y:auto;padding:20px">
  <div style="background:#fff;border-radius:18px;max-width:600px;margin:40px auto;padding:32px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
      <h2 style="font-size:1.1rem;font-weight:800;color:#1a1a2e" id="modalRef">Booking</h2>
      <button onclick="document.getElementById('orderModal').style.display='none'"
              style="border:none;background:none;font-size:1.3rem;cursor:pointer;color:#888">✕</button>
    </div>
    <div id="modalBody"></div>
    <div style="margin-top:24px;display:flex;gap:10px;flex-wrap:wrap">
      <select id="statusSelect" class="form-select" style="flex:1;min-width:160px">
        <?php foreach (['pending','confirmed','in_progress','completed','cancelled'] as $st): ?>
        <option value="<?= $st ?>"><?= ucfirst(str_replace('_',' ',$st)) ?></option>
        <?php endforeach; ?>
      </select>
      <textarea id="adminNoteInput" class="form-textarea" style="flex:2;min-height:60px" placeholder="Admin note…"></textarea>
      <button class="btn btn-primary" onclick="updateOrderStatus()">
        <i class="fa-solid fa-check"></i> Update
      </button>
    </div>
  </div>
</div>

<?php
$csrf = Security::csrfToken();
$extra_admin_js = <<<JS
var currentOrderId = null;
function escHtml(value){
  return String(value == null ? '' : value).replace(/[&<>"']/g,function(ch){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch];
  });
}
function uploadUrl(path){
  path = String(path || '');
  return path.indexOf('/uploads/') === 0 ? window.ELLCY_BASE + path : '';
}
function applyFilters(){
  var q = document.getElementById('bkSearch').value;
  var s = document.getElementById('bkStatus').value;
  window.location.href = window.location.pathname + '?q=' + encodeURIComponent(q) + '&status=' + encodeURIComponent(s);
}
document.getElementById('bkSearch').addEventListener('keydown',function(e){if(e.key==='Enter')applyFilters();});

function showOrderModal(o){
  currentOrderId = o.id;
  document.getElementById('orderModal').style.display = 'block';
  document.getElementById('modalRef').textContent = 'Booking: ' + o.order_ref;
  document.getElementById('statusSelect').value = o.status;
  document.getElementById('adminNoteInput').value = o.admin_note || '';
  var items = [];
  try { items = JSON.parse(o.items_json || '[]'); } catch(e){}
  var html = '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;font-size:.87rem">';
  html += '<div><b>Name:</b> ' + escHtml(o.name) + '</div>';
  html += '<div><b>Phone:</b> ' + escHtml(o.phone) + '</div>';
  if(o.email) html += '<div><b>Email:</b> ' + escHtml(o.email) + '</div>';
  if(o.event_type) html += '<div><b>Event:</b> ' + escHtml(o.event_type) + '</div>';
  if(o.event_date) html += '<div><b>Date:</b> ' + escHtml(o.event_date) + '</div>';
  if(o.event_venue) html += '<div><b>Venue:</b> ' + escHtml(o.event_venue) + '</div>';
  if(o.guest_count) html += '<div><b>Guests:</b> ' + escHtml(o.guest_count) + '</div>';
  html += '</div>';
  var venueImages = [];
  try { venueImages = o.event_venue_images ? JSON.parse(o.event_venue_images) : []; } catch(e){}
  if(venueImages.length){
    html += '<div style="font-weight:700;margin-bottom:8px;font-size:.85rem">Event Location Photos</div>';
    html += '<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">';
    venueImages.forEach(function(src){
      src = uploadUrl(src); if(!src) return;
      html += '<a href="' + escHtml(src) + '" target="_blank" rel="noopener">' +
              '<img src="' + escHtml(src) + '" alt="Event location photo" ' +
              'style="width:84px;height:84px;object-fit:cover;border-radius:8px;border:1px solid #e0d5f0"/></a>';
    });
    html += '</div>';
  }
  var references = Array.isArray(o.reference_uploads) ? o.reference_uploads : [];
  if(references.length){
    html += '<div style="font-weight:700;margin-bottom:8px;font-size:.85rem">Saree / Colour References</div>';
    html += '<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">';
    references.forEach(function(ref){
      var src=uploadUrl(ref.path); if(!src) return;
      html += '<a href="'+escHtml(src)+'" target="_blank" rel="noopener" title="'+escHtml(ref.service_slug)+'">' +
              '<img src="'+escHtml(src)+'" alt="Jewellery reference" style="width:96px;height:96px;object-fit:cover;border-radius:8px;border:1px solid #e0d5f0"></a>';
    });
    html += '</div>';
  }
  if(items.length){
    html += '<div style="font-weight:700;margin-bottom:8px;font-size:.85rem">Ordered Services</div>';
    html += '<div style="border:1px solid #e0d5f0;border-radius:10px;overflow:hidden">';
    items.forEach(function(i){
      html += '<div style="display:flex;justify-content:space-between;padding:10px 14px;border-bottom:1px solid #f0eef8;font-size:.84rem">';
      html += '<span>' + (i.title||i.id) + (i.qty>1?' × '+i.qty:'') + '</span>';
      html += '<span style="font-weight:700">₹' + ((i.price||0)*(i.qty||1)).toLocaleString('en-IN') + '</span>';
      html += '</div>';
    });
    var tot = items.reduce(function(s,i){return s+(i.price||0)*(i.qty||1);},0);
    html += '<div style="display:flex;justify-content:space-between;padding:10px 14px;font-weight:800;background:#f9f5ff;font-size:.9rem">';
    html += '<span>Total</span><span style="color:#6a1b9a">₹'+tot.toLocaleString('en-IN')+'</span></div>';
    html += '</div>';
  }
  if(o.note) html += '<div style="margin-top:14px;font-size:.83rem;color:#666"><b>Note:</b> ' + escHtml(o.note) + '</div>';
  document.getElementById('modalBody').innerHTML = html;
}

function updateOrderStatus(){
  var status = document.getElementById('statusSelect').value;
  var note   = document.getElementById('adminNoteInput').value;
  fetch(window.ELLCY_BASE + '/admin/bookings/update/' + currentOrderId, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'status='+encodeURIComponent(status)+'&admin_note='+encodeURIComponent(note)+'&csrf_token={$csrf}'
  })
  .then(function(r){return r.json();})
  .then(function(d){
    if(d.success){ showNotif('Status updated!','success'); setTimeout(function(){location.reload();},1000); }
    else showNotif(d.message||'Error','error');
  });
}
JS;
require VIEWS_PATH . '/admin/layout_end.php';
?>
