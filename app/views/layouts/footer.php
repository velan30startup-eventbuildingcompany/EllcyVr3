<footer class="site-footer" role="contentinfo">
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="footer-logo">ELLCY</div>
      <p class="footer-text">Creating unforgettable moments — weddings, birthdays, college events and temple celebrations across Chennai.</p>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="<?= APP_URL ?>/">Home</a></li>
        <li><a href="<?= APP_URL ?>/services">Event Services</a></li>
        <li><a href="<?= APP_URL ?>/booking">Book Now</a></li>
        <li><a href="<?= APP_URL ?>/cart">My Cart</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Contact</h4>
      <p class="footer-contact-item"><?= Security::e($settings['contact_phone'] ?? '+91 123-456-789') ?></p>
      <p class="footer-contact-item"><?= Security::e($settings['contact_email'] ?? 'info@ellcy.in') ?></p>
      <p class="footer-contact-item"><?= Security::e($settings['contact_address'] ?? 'Chennai, Tamil Nadu') ?></p>
    </div>
    <div class="footer-col">
      <h4>Book Your Event</h4>
      <p class="footer-quote-text">Add services to cart and confirm your booking in minutes.</p>
      <a class="footer-enquiry-btn" href="<?= APP_URL ?>/booking">
        <i class="fa-solid fa-calendar-check" aria-hidden="true"></i> Book Now
      </a>
    </div>
  </div>
  <div class="footer-divider"></div>
  <div class="footer-bottom">
    <p><span id="year"></span> &copy; ELLCY &mdash; All Rights Reserved.</p>
    <div class="footer-social">
      <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
      <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
      <a href="#" aria-label="Twitter/X"><i class="fab fa-x-twitter"></i></a>
    </div>
  </div>
</footer>

<script>document.getElementById('year').textContent = new Date().getFullYear();</script>
<script src="<?= PUBLIC_URL ?>/js/data.js"></script>
  <script src="<?= PUBLIC_URL ?>/js/auth.js?v=20260804.6"></script>
<script src="<?= PUBLIC_URL ?>/js/cart.js"></script>
<?php if (!empty($extra_js)): ?>
  <?php foreach ((array)$extra_js as $js): ?>
  <script src="<?= PUBLIC_URL ?>/js/<?= Security::e($js) ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($inline_js)): ?>
<script><?= $inline_js ?></script>
<?php endif; ?>
</body>
</html>
