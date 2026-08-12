<?php
$page_title = 'Vendor Sign Up | ELLCY';
$meta_description = 'Join ELLCY as a verified event-services vendor in Chennai.';
$robots = 'index, follow';
$settings = [];
$extra_css = ['header2.css'];
$skip_data_js = true;
require VIEWS_PATH . '/layouts/header.php';
?>
<style>
.vendor-page{min-height:72vh;background:#f7f2fb;padding:56px 20px}.vendor-wrap{max-width:1120px;margin:auto;display:grid;grid-template-columns:.9fr 1.1fr;gap:38px;align-items:start}.vendor-copy{padding:22px 4px}.vendor-kicker{color:#6a1b9a;font-size:.78rem;font-weight:900;letter-spacing:.1em;text-transform:uppercase}.vendor-copy h1{margin:10px 0 14px;font-size:clamp(2rem,4vw,3.35rem);line-height:1.05;color:#171327}.vendor-copy>p{color:#665d6d;line-height:1.7}.vendor-benefits{display:grid;gap:14px;margin-top:26px}.vendor-benefits div{display:flex;gap:13px}.vendor-benefits i{color:#6a1b9a;margin-top:4px}.vendor-benefits strong{display:block;color:#211728}.vendor-benefits span{color:#786e7e;font-size:.9rem}.vendor-card{padding:30px;border:1px solid #eadff1;border-radius:20px;background:#fff;box-shadow:0 18px 50px rgba(55,25,75,.1)}.vendor-card h2{margin:0 0 5px}.vendor-card>p{margin:0 0 22px;color:#756b7c}.vendor-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}.vendor-field{display:grid;gap:7px}.vendor-field.full{grid-column:1/-1}.vendor-field label{font-size:.8rem;font-weight:800;color:#33263c}.vendor-field input,.vendor-field select,.vendor-field textarea{width:100%;padding:12px 13px;border:1px solid #dcd1e4;border-radius:10px;background:#fff;color:#1e1524;font:inherit}.vendor-field textarea{min-height:90px;resize:vertical}.vendor-submit{width:100%;margin-top:18px;padding:14px;border:0;border-radius:11px;background:#6a1b9a;color:#fff;font:inherit;font-weight:900;cursor:pointer}.vendor-alert{margin-bottom:18px;padding:12px 14px;border-radius:10px;font-weight:700}.vendor-alert.ok{background:#e8f8ef;color:#17653a}.vendor-alert.err{background:#fff0f1;color:#9c2334}.vendor-privacy{margin:12px 0 0!important;font-size:.74rem}.honeypot{position:absolute!important;left:-9999px!important}@media(max-width:780px){.vendor-page{padding:30px 16px}.vendor-wrap{grid-template-columns:1fr;gap:18px}.vendor-card{padding:22px}.vendor-grid{grid-template-columns:1fr}.vendor-field.full{grid-column:auto}}
</style>
<header class="header new-header" role="banner">
  <span class="hdr-mobile-title">Vendor Sign Up</span>
  <a class="logo" href="<?= APP_URL ?>/" aria-label="ELLCY Home">ELLCY</a>
  <a href="<?= APP_URL ?>/cart" class="cart-header-btn hdr-cart-right" aria-label="View cart">
    <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i><span class="cart-btn-label">Cart</span><span class="cart-badge" style="display:none">0</span>
  </a>
</header>
<main class="vendor-page"><div class="vendor-wrap">
  <section class="vendor-copy"><span class="vendor-kicker">ELLCY for vendors</span><h1>Turn your event expertise into a growing business.</h1><p>Join a curated marketplace built for trusted event professionals. Create visibility, receive qualified enquiries and grow with a platform focused on Chennai celebrations.</p><div class="vendor-benefits"><div><i class="fa-solid fa-users"></i><p><strong>Reach ready-to-book customers</strong><span>Show your services to people actively planning events.</span></p></div><div><i class="fa-solid fa-shield-halved"></i><p><strong>Build a trusted profile</strong><span>Verification and clear service information improve customer confidence.</span></p></div><div><i class="fa-solid fa-chart-line"></i><p><strong>Grow with one partner</strong><span>Manage leads and expand your catalogue as your business grows.</span></p></div></div></section>
  <section class="vendor-card"><h2>Become an ELLCY vendor</h2><p>Tell us about your business. Verification is completed before a profile is published.</p>
    <?php if ($success): ?><div class="vendor-alert ok" role="status"><?= Security::e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="vendor-alert err" role="alert"><?= Security::e($error) ?></div><?php endif; ?>
    <form method="post" action="<?= APP_URL ?>/vendor-signup" novalidate><?= Security::csrfField() ?><input class="honeypot" name="company_url" tabindex="-1" autocomplete="off" aria-hidden="true">
      <div class="vendor-grid">
        <?php foreach ([['business_name','Business name'],['contact_name','Contact person'],['email','Business email'],['phone','Phone number'],['city','City'],['website','Website (optional)']] as [$name,$label]): ?><div class="vendor-field"><label for="v_<?= $name ?>"><?= $label ?></label><input id="v_<?= $name ?>" name="<?= $name ?>" value="<?= Security::e($values[$name]) ?>" <?= in_array($name,['business_name','contact_name','email','phone','city'],true)?'required':'' ?> <?= $name==='email'?'type="email"':($name==='phone'?'type="tel"':($name==='website'?'type="url"':'type="text"')) ?>></div><?php endforeach; ?>
        <div class="vendor-field full"><label for="v_category">Primary service category</label><select id="v_category" name="service_category" required><option value="">Choose a category</option><?php foreach ($categories as $category): ?><option <?= $values['service_category']===$category?'selected':'' ?>><?= Security::e($category) ?></option><?php endforeach; ?></select></div>
        <div class="vendor-field full"><label for="v_note">Tell us about your services</label><textarea id="v_note" name="note" maxlength="800"><?= Security::e($values['note']) ?></textarea></div>
      </div><button class="vendor-submit" type="submit">Submit vendor application</button><p class="vendor-privacy">Your details are used only to review and contact you about this application.</p>
    </form>
  </section>
</div></main>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>
