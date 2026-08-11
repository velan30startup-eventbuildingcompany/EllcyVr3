  </div><!-- /admin-content -->
</div><!-- /admin-main -->

<!-- NOTIFICATION TOAST -->
<div class="notif" id="notif"></div>

<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('show');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('show');
}
function showNotif(msg, type='success'){
  var n = document.getElementById('notif');
  n.textContent = msg;
  n.className = 'notif show ' + type;
  setTimeout(function(){ n.className='notif'; }, 3500);
}
// Auto-show flash message from PHP
<?php if (!empty($flash_message)): ?>
setTimeout(function(){ showNotif(<?= json_encode($flash_message) ?>, <?= json_encode($flash_type ?? 'success') ?>); }, 200);
<?php endif; ?>
</script>
<?php if (!empty($extra_admin_js)): ?>
<script><?= $extra_admin_js ?></script>
<?php endif; ?>
</body>
</html>
