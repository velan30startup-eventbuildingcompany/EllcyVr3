// enquiry.js — Enquiry page
document.addEventListener('DOMContentLoaded', () => {
  const categorySelect = document.getElementById('eventCategory');
  const serviceSelect  = document.getElementById('services');
  const form           = document.getElementById('enquiryForm');
  const resultBox      = document.getElementById('enquiryResult');
  const submitBtn      = document.getElementById('submitEnquiry');

  // Populate services dropdown when event type changes
  categorySelect?.addEventListener('change', () => {
    const list = ENQUIRY_SERVICES[categorySelect.value] || [];
    serviceSelect.innerHTML = '<option value="">Choose a Service</option>';
    list.forEach(name => {
      const opt = document.createElement('option');
      opt.value = name; opt.textContent = name;
      serviceSelect.appendChild(opt);
    });
  });

  // Form submit (client-side simulation)
  form?.addEventListener('submit', e => {
    e.preventDefault();
    const name  = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const cat   = categorySelect.value;
    const svc   = serviceSelect.value;

    if (!name || !phone || !cat || !svc) {
      resultBox.textContent = 'Please fill in all required fields.';
      resultBox.style.color = 'red';
      resultBox.style.display = 'block';
      return;
    }
    if (!/^\+?\d[\d\s\-]{6,}$/.test(phone)) {
      resultBox.textContent = 'Please enter a valid phone number.';
      resultBox.style.color = 'red';
      resultBox.style.display = 'block';
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    setTimeout(() => {
      resultBox.textContent = '✅ Enquiry submitted successfully! Our team will contact you shortly.';
      resultBox.style.color = 'green';
      resultBox.style.display = 'block';
      form.reset();
      serviceSelect.innerHTML = '<option value="">Choose a Service</option>';
      submitBtn.disabled = false;
      submitBtn.textContent = 'Submit Enquiry';
    }, 900);
  });

  const yr = document.getElementById('year');
  if (yr) yr.textContent = new Date().getFullYear();
});
