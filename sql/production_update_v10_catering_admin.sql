-- ELLCY production update v10
-- Adds independently editable Banana Leaf and Buffet prices for the
-- Breakfast, Lunch and Dinner Catering Boys admin records.
USE ellcy_db;

INSERT IGNORE INTO service_packages
  (service_id, pkg_key, slug, label, price, description, is_default, sort_order, status)
SELECT id, 'banana_leaf', 'catering-boys-breakfast-banana-leaf', 'Banana Leaf Style', 850,
  'Traditional banana leaf breakfast service with uniformed catering staff.', 1, 1, 'active'
FROM services WHERE slug='catering-boys-breakfast';
INSERT IGNORE INTO service_packages
  (service_id, pkg_key, slug, label, price, description, is_default, sort_order, status)
SELECT id, 'buffet', 'catering-boys-breakfast-buffet', 'Buffet Style', 750,
  'Professionally managed buffet breakfast service with uniformed catering staff.', 0, 2, 'active'
FROM services WHERE slug='catering-boys-breakfast';

INSERT IGNORE INTO service_packages
  (service_id, pkg_key, slug, label, price, description, is_default, sort_order, status)
SELECT id, 'banana_leaf', 'catering-boys-lunch-banana-leaf', 'Banana Leaf Style', 950,
  'Traditional banana leaf lunch service with uniformed catering staff.', 1, 1, 'active'
FROM services WHERE slug='catering-boys-lunch';
INSERT IGNORE INTO service_packages
  (service_id, pkg_key, slug, label, price, description, is_default, sort_order, status)
SELECT id, 'buffet', 'catering-boys-lunch-buffet', 'Buffet Style', 850,
  'Professionally managed buffet lunch service with uniformed catering staff.', 0, 2, 'active'
FROM services WHERE slug='catering-boys-lunch';

INSERT IGNORE INTO service_packages
  (service_id, pkg_key, slug, label, price, description, is_default, sort_order, status)
SELECT id, 'banana_leaf', 'catering-boys-dinner-banana-leaf', 'Banana Leaf Style', 1000,
  'Traditional banana leaf dinner service with uniformed catering staff.', 1, 1, 'active'
FROM services WHERE slug='catering-boys-dinner';
INSERT IGNORE INTO service_packages
  (service_id, pkg_key, slug, label, price, description, is_default, sort_order, status)
SELECT id, 'buffet', 'catering-boys-dinner-buffet', 'Buffet Style', 900,
  'Professionally managed buffet dinner service with uniformed catering staff.', 0, 2, 'active'
FROM services WHERE slug='catering-boys-dinner';
