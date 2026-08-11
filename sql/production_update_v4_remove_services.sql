-- ============================================================
-- Ellcy.in — Production update v4
-- Removes "Invitation" and "Aarthi Plate" from the live site.
--
-- We HIDE rather than DELETE the category/service rows. This is
-- the safe production choice: if any past order or booking already
-- references these services (orders.items_json, request_calls,
-- etc.), deleting the rows would break that historical record or
-- fail on a foreign key. Hiding removes them everywhere on the
-- public site (they are filtered out of listings/admin dropdowns)
-- without touching order history.
--
-- Safe to re-run.
-- ============================================================

UPDATE service_categories
   SET hidden = 1
 WHERE slug IN ('invitation', 'aarthi-plate');

UPDATE services
   SET status = 'inactive'
 WHERE slug IN ('digital-wedding-invitation', 'traditional-aarthi-plate');
