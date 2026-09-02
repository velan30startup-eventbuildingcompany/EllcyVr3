-- ============================================================
-- ELLCY — Service Catalog Migration
-- Generated from the legacy static js/data.js catalog.
-- Run AFTER ellcy_schema.sql (adds missing sub-categories, then
-- inserts every real service + price as actual DB rows).
-- ============================================================
USE ellcy_db;

-- ── New categories not already in the base schema ──────────
INSERT INTO service_categories (parent_id,name,slug,description,image,sort_order)
SELECT id, 'Light Decoration', 'light-decoration', 'Indoor & outdoor professional lighting setups for your event venue.', '/uploads/services/stage.webp', 0
FROM service_categories WHERE slug='decoration'
AND NOT EXISTS (SELECT 1 FROM service_categories WHERE slug='light-decoration');
INSERT INTO service_categories (parent_id,name,slug,description,image,sort_order)
SELECT id, 'Stage Decoration', 'stage-decoration', 'Elegant stage setups, backdrops, floral arrangements and full hall transformations.', '/uploads/services/stage.webp', 0
FROM service_categories WHERE slug='decoration'
AND NOT EXISTS (SELECT 1 FROM service_categories WHERE slug='stage-decoration');
INSERT INTO service_categories (parent_id,name,slug,description,image,sort_order)
SELECT id, 'Chenda Melam', 'chenda-melam', 'Traditional Kerala Chenda Melam percussion ensemble for processions and celebrations.', '/uploads/services/stage.webp', 0
FROM service_categories WHERE slug='music-performers'
AND NOT EXISTS (SELECT 1 FROM service_categories WHERE slug='chenda-melam');
INSERT INTO service_categories (parent_id,name,slug,description,image,sort_order)
SELECT id, 'Nadhaswaram & Thavil', 'nadhaswaram-thavil', 'Classical Nadhaswaram and Thavil duo for wedding rituals and auspicious ceremonies.', '/uploads/services/stage.webp', 0
FROM service_categories WHERE slug='music-performers'
AND NOT EXISTS (SELECT 1 FROM service_categories WHERE slug='nadhaswaram-thavil');
INSERT INTO service_categories (parent_id,name,slug,description,image,sort_order)
SELECT id, 'Band Set', 'band-set', 'Professional brass band for baraat, processions and grand entry ceremonies.', '/uploads/services/stage.webp', 0
FROM service_categories WHERE slug='music-performers'
AND NOT EXISTS (SELECT 1 FROM service_categories WHERE slug='band-set');
INSERT INTO service_categories (parent_id,name,slug,description,image,sort_order)
SELECT id, 'Melam Set', 'melam-set', 'Traditional melam set for poojas, home events and large temple celebrations.', '/uploads/services/stage.webp', 0
FROM service_categories WHERE slug='music-performers'
AND NOT EXISTS (SELECT 1 FROM service_categories WHERE slug='melam-set');
INSERT IGNORE INTO service_categories (parent_id,name,slug,description,image,sort_order)
VALUES (NULL, 'Real Flowers', 'real-flowers', 'Fresh and artificial flower decoration for weddings.', '/uploads/services/stage.webp', 50);

-- ── Services (one row per package/variant from the old catalog) ──
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Light Set Up In Party Hall', 'light-set-up-in-party-hall', 'Professional indoor party hall lighting setup with RGB LED strips, fairy lights, spotlights, and ambient colour-changing fixtures. Transforms any hall into a stunning venue.', 'Professional indoor party hall lighting setup with RGB LED strips, fairy lights, spotlights, and ambient colour-changing fixtures. Transforms any hall into a stunning venue.', 0, 'per event', '/uploads/services/lighting.webp', 'light-decoration', 'active'
FROM service_categories WHERE slug='light-decoration'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='light-set-up-in-party-hall');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Light Set Up In Out Door', 'light-set-up-in-out-door', 'High-impact outdoor lighting setup with weatherproof LED fixtures, string lights, uplighters, and powerful floodlights. Perfect for open-air events, lawns and rooftop celebrations.', 'High-impact outdoor lighting setup with weatherproof LED fixtures, string lights, uplighters, and powerful floodlights. Perfect for open-air events, lawns and rooftop celebrations.', 0, 'per event', '/uploads/services/lighting.webp', 'light-decoration', 'active'
FROM service_categories WHERE slug='light-decoration'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='light-set-up-in-out-door');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Party Hall Decoration', 'party-hall-decoration', 'Breathtaking party hall stage setups crafted with premium backdrops, LED panels, floral arrangements and full mood lighting — designed to impress every guest.', 'Breathtaking party hall stage setups crafted with premium backdrops, LED panels, floral arrangements and full mood lighting — designed to impress every guest.', 0, 'per event', '/uploads/services/stage.webp', 'stage-decoration', 'active'
FROM service_categories WHERE slug='stage-decoration'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='party-hall-decoration');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Outdoor Decoration', 'outdoor-decoration', 'Grand outdoor stage setups built for open-air events with weather-resistant structures, floral archways, draping and atmospheric lighting installations.', 'Grand outdoor stage setups built for open-air events with weather-resistant structures, floral archways, draping and atmospheric lighting installations.', 0, 'per event', '/uploads/services/stage.webp', 'stage-decoration', 'active'
FROM service_categories WHERE slug='stage-decoration'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='outdoor-decoration');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Hotel Decoration', 'hotel-decoration', 'Luxury hotel venue decoration packages including stage design, table centrepieces, floral walls, entrance arches and complete hall transformation.', 'Luxury hotel venue decoration packages including stage design, table centrepieces, floral walls, entrance arches and complete hall transformation.', 0, 'per event', '/uploads/services/stage.webp', 'stage-decoration', 'active'
FROM service_categories WHERE slug='stage-decoration'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='hotel-decoration');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Dinner Catering', 'dinner-catering', 'Full-course dinner catering service with multiple cuisines, live counters, and professional serving staff for your event.', 'Full-course dinner catering service with multiple cuisines, live counters, and professional serving staff for your event.', 450, 'per event', '/uploads/services/catering.webp', 'food', 'active'
FROM service_categories WHERE slug='food'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='dinner-catering');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Breakfast Catering', 'breakfast-catering', 'Fresh and wholesome breakfast spread with South Indian, North Indian and continental options. Ideal for morning ceremonies.', 'Fresh and wholesome breakfast spread with South Indian, North Indian and continental options. Ideal for morning ceremonies.', 250, 'per event', '/uploads/services/catering.webp', 'food', 'active'
FROM service_categories WHERE slug='food'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='breakfast-catering');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Lunch Catering', 'lunch-catering', 'Elaborate lunch catering with traditional thali meals, buffet spreads and live cooking stations for afternoon events.', 'Elaborate lunch catering with traditional thali meals, buffet spreads and live cooking stations for afternoon events.', 350, 'per event', '/uploads/services/catering.webp', 'food', 'active'
FROM service_categories WHERE slug='food'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='lunch-catering');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'DJ Starter Package', 'dj-starter-package', 'Entry-level DJ setup with quality sound system, basic LED lighting and a curated playlist for small, intimate celebrations.', 'Entry-level DJ setup with quality sound system, basic LED lighting and a curated playlist for small, intimate celebrations.', 9999, 'per event', '/uploads/services/dj.webp', 'dj', 'active'
FROM service_categories WHERE slug='dj'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='dj-starter-package');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'DJ Silver Package', 'dj-silver-package', 'Mid-range DJ package with enhanced sound system, moving head lights and fog machine. Great for up to 200 guests.', 'Mid-range DJ package with enhanced sound system, moving head lights and fog machine. Great for up to 200 guests.', 14999, 'per event', '/uploads/services/dj.webp', 'dj', 'active'
FROM service_categories WHERE slug='dj'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='dj-silver-package');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'DJ Gold Package', 'dj-gold-package', 'Premium DJ experience with professional-grade sound towers, LED wash lights, laser effects and a customised playlist.', 'Premium DJ experience with professional-grade sound towers, LED wash lights, laser effects and a customised playlist.', 17999, 'per event', '/uploads/services/dj.webp', 'dj', 'active'
FROM service_categories WHERE slug='dj'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='dj-gold-package');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'DJ Platinum Package', 'dj-platinum-package', 'High-impact DJ setup with dual sub-woofers, full LED stage rig, haze machines and live mixing. Perfect for 500 guests.', 'High-impact DJ setup with dual sub-woofers, full LED stage rig, haze machines and live mixing. Perfect for 500 guests.', 24999, 'per event', '/uploads/services/dj.webp', 'dj', 'active'
FROM service_categories WHERE slug='dj'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='dj-platinum-package');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'DJ Diamond Package', 'dj-diamond-package', 'Elite DJ performance with touring-grade line-array speakers, full moving-head truss system, confetti cannons and CO₂ jets.', 'Elite DJ performance with touring-grade line-array speakers, full moving-head truss system, confetti cannons and CO₂ jets.', 34999, 'per event', '/uploads/services/dj.webp', 'dj', 'active'
FROM service_categories WHERE slug='dj'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='dj-diamond-package');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'DJ Ultra Package', 'dj-ultra-package', 'Luxury DJ event experience with concert-level sound, full-colour LED video wall backdrop and a professional MC.', 'Luxury DJ event experience with concert-level sound, full-colour LED video wall backdrop and a professional MC.', 47999, 'per event', '/uploads/services/dj.webp', 'dj', 'active'
FROM service_categories WHERE slug='dj'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='dj-ultra-package');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'DJ Grand Celebration Package', 'dj-grand-celebration-package', 'The ultimate DJ package — full production sound & lighting, pyrotechnic sparks, mirror ball, cold fire jets and a sound engineer.', 'The ultimate DJ package — full production sound & lighting, pyrotechnic sparks, mirror ball, cold fire jets and a sound engineer.', 59999, 'per event', '/uploads/services/dj.webp', 'dj', 'active'
FROM service_categories WHERE slug='dj'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='dj-grand-celebration-package');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Chenda Melam – Standard', 'chenda-melam-standard', 'Traditional Kerala Chenda Melam percussion ensemble with experienced artists performing authentic rhythmic beats for processions and auspicious ceremonies.', 'Traditional Kerala Chenda Melam percussion ensemble with experienced artists performing authentic rhythmic beats for processions and auspicious ceremonies.', 12000, 'per event', '/uploads/services/musical_band.webp', 'chenda-melam', 'active'
FROM service_categories WHERE slug='chenda-melam'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='chenda-melam-standard');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Chenda Melam – Grand Procession', 'chenda-melam-grand-procession', 'Large-scale Chenda Melam troupe with full brass and percussion ensemble ideal for wedding processions, temple festivals and grand cultural events.', 'Large-scale Chenda Melam troupe with full brass and percussion ensemble ideal for wedding processions, temple festivals and grand cultural events.', 22000, 'per event', '/uploads/services/musical_band.webp', 'chenda-melam', 'active'
FROM service_categories WHERE slug='chenda-melam'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='chenda-melam-grand-procession');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Nadhaswaram & Thavil – Classic', 'nadhaswaram-thavil-classic', 'Traditional Nadhaswaram and Thavil duo performance for auspicious ceremonies, wedding rituals and processions. Brings divine blessings and festive energy.', 'Traditional Nadhaswaram and Thavil duo performance for auspicious ceremonies, wedding rituals and processions. Brings divine blessings and festive energy.', 8000, 'per event', '/uploads/services/nadhaswaram.webp', 'nadhaswaram-thavil', 'active'
FROM service_categories WHERE slug='nadhaswaram-thavil'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='nadhaswaram-thavil-classic');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Nadhaswaram & Thavil – Grand', 'nadhaswaram-thavil-grand', 'Full ensemble Nadhaswaram and Thavil group performance ideal for grand weddings, temple events and large-scale cultural celebrations.', 'Full ensemble Nadhaswaram and Thavil group performance ideal for grand weddings, temple events and large-scale cultural celebrations.', 15000, 'per event', '/uploads/services/nadhaswaram.webp', 'nadhaswaram-thavil', 'active'
FROM service_categories WHERE slug='nadhaswaram-thavil'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='nadhaswaram-thavil-grand');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Band Set – 6 Members', 'band-set-6-members', 'Compact 6-member brass band for intimate wedding entries and smaller processions. Uniformed performers with a curated wedding classics repertoire.', 'Compact 6-member brass band for intimate wedding entries and smaller processions. Uniformed performers with a curated wedding classics repertoire.', 11994, 'per event', '/uploads/services/bandset.webp', 'band-set', 'active'
FROM service_categories WHERE slug='band-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='band-set-6-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Band Set – 8 Members', 'band-set-8-members', '8-member ensemble delivering a fuller brass sound, ideal for mid-sized wedding processions and grand entry ceremonies.', '8-member ensemble delivering a fuller brass sound, ideal for mid-sized wedding processions and grand entry ceremonies.', 15992, 'per event', '/uploads/services/bandset.webp', 'band-set', 'active'
FROM service_categories WHERE slug='band-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='band-set-8-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Band Set – 10 Members', 'band-set-10-members', 'Impressive 10-member brass band for larger wedding ceremonies, baarats and grand entries with high energy and showmanship.', 'Impressive 10-member brass band for larger wedding ceremonies, baarats and grand entries with high energy and showmanship.', 19990, 'per event', '/uploads/services/bandset.webp', 'band-set', 'active'
FROM service_categories WHERE slug='band-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='band-set-10-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Band Set – 12 Members', 'band-set-12-members', 'Grand 12-member ensemble with uniformed performers and drum major. A commanding presence for large-scale wedding processions.', 'Grand 12-member ensemble with uniformed performers and drum major. A commanding presence for large-scale wedding processions.', 23988, 'per event', '/uploads/services/bandset.webp', 'band-set', 'active'
FROM service_categories WHERE slug='band-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='band-set-12-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Band Set – 15 Members', 'band-set-15-members', 'Premium 15-member brass band with LED costumes and choreographed drum majors. Makes every procession a visual and musical spectacle.', 'Premium 15-member brass band with LED costumes and choreographed drum majors. Makes every procession a visual and musical spectacle.', 29985, 'per event', '/uploads/services/bandset.webp', 'band-set', 'active'
FROM service_categories WHERE slug='band-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='band-set-15-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Band Set – 18 Members', 'band-set-18-members', 'Elite 18-member ensemble delivering a wall of sound and dazzling performance. Perfect for extravagant weddings and grand baraat celebrations.', 'Elite 18-member ensemble delivering a wall of sound and dazzling performance. Perfect for extravagant weddings and grand baraat celebrations.', 35982, 'per event', '/uploads/services/bandset.webp', 'band-set', 'active'
FROM service_categories WHERE slug='band-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='band-set-18-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Band Set – 20 Members', 'band-set-20-members', 'Our flagship 20-member full brass band — the ultimate grand entry experience with full LED production, drum corps and maximum energy.', 'Our flagship 20-member full brass band — the ultimate grand entry experience with full LED production, drum corps and maximum energy.', 39980, 'per event', '/uploads/services/bandset.webp', 'band-set', 'active'
FROM service_categories WHERE slug='band-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='band-set-20-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Melam Set – 4 Members', 'melam-set-4-members', 'Compact 4-member melam procession set for intimate ceremonies, home poojas and smaller festive occasions.', 'Compact 4-member melam procession set for intimate ceremonies, home poojas and smaller festive occasions.', 7994, 'per event', '/uploads/services/musical_band.webp', 'melam-set', 'active'
FROM service_categories WHERE slug='melam-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='melam-set-4-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Melam Set – 6 Members', 'melam-set-6-members', '6-member traditional percussion ensemble, ideal for mid-sized processions, griha pravesams and auspicious family functions.', '6-member traditional percussion ensemble, ideal for mid-sized processions, griha pravesams and auspicious family functions.', 11994, 'per event', '/uploads/services/musical_band.webp', 'melam-set', 'active'
FROM service_categories WHERE slug='melam-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='melam-set-6-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Melam Set – 8 Members', 'melam-set-8-members', '8-member melam set delivering a fuller, more resonant sound for wedding processions and temple festival ceremonies.', '8-member melam set delivering a fuller, more resonant sound for wedding processions and temple festival ceremonies.', 15992, 'per event', '/uploads/services/musical_band.webp', 'melam-set', 'active'
FROM service_categories WHERE slug='melam-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='melam-set-8-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Melam Set – 10 Members', 'melam-set-10-members', 'Grand 10-member ensemble ideal for larger wedding processions, temple festivals and elaborate ceremonial routes.', 'Grand 10-member ensemble ideal for larger wedding processions, temple festivals and elaborate ceremonial routes.', 19990, 'per event', '/uploads/services/musical_band.webp', 'melam-set', 'active'
FROM service_categories WHERE slug='melam-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='melam-set-10-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Melam Set – 12 Members', 'melam-set-12-members', '12-member percussion ensemble creating a powerful, rhythmic atmosphere for grand weddings and major festival events.', '12-member percussion ensemble creating a powerful, rhythmic atmosphere for grand weddings and major festival events.', 23988, 'per event', '/uploads/services/musical_band.webp', 'melam-set', 'active'
FROM service_categories WHERE slug='melam-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='melam-set-12-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Melam Set – 15 Members', 'melam-set-15-members', 'Premium 15-member melam set for large-scale processions and elaborate cultural celebrations with full devotional energy.', 'Premium 15-member melam set for large-scale processions and elaborate cultural celebrations with full devotional energy.', 29985, 'per event', '/uploads/services/musical_band.webp', 'melam-set', 'active'
FROM service_categories WHERE slug='melam-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='melam-set-15-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Melam Set – 18 Members', 'melam-set-18-members', 'Elite 18-member ensemble delivering an immersive wall of percussion for extravagant wedding processions and grand events.', 'Elite 18-member ensemble delivering an immersive wall of percussion for extravagant wedding processions and grand events.', 35982, 'per event', '/uploads/services/musical_band.webp', 'melam-set', 'active'
FROM service_categories WHERE slug='melam-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='melam-set-18-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Melam Set – 20 Members', 'melam-set-20-members', 'Our flagship 20-member grand procession ensemble — the ultimate traditional melam experience for the most prestigious ceremonies.', 'Our flagship 20-member grand procession ensemble — the ultimate traditional melam experience for the most prestigious ceremonies.', 39980, 'per event', '/uploads/services/musical_band.webp', 'melam-set', 'active'
FROM service_categories WHERE slug='melam-set'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='melam-set-20-members');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Event Security & Bouncers', 'event-security-bouncers', 'Professional event security and bouncers for crowd management, entry control and event safety.', 'Professional event security and bouncers for crowd management, entry control and event safety.', 1400, 'per event', '/uploads/services/bouncer.webp', 'bouncers', 'active'
FROM service_categories WHERE slug='bouncers'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='event-security-bouncers');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, '360 Degree Camera', '360-degree-camera', 'Immersive 360° camera booth for your event — captures slow-motion videos of guests for instant sharing and lasting memories.', 'Immersive 360° camera booth for your event — captures slow-motion videos of guests for instant sharing and lasting memories.', 8000, 'per event', '/uploads/services/photobooth.webp', 'entertainment-activities', 'active'
FROM service_categories WHERE slug='entertainment-activities'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='360-degree-camera');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Photo Booth', 'photo-booth', 'Fully branded photo booth with props, instant prints and digital sharing. A crowd favourite at weddings and parties.', 'Fully branded photo booth with props, instant prints and digital sharing. A crowd favourite at weddings and parties.', 6000, 'per event', '/uploads/services/photobooth.webp', 'entertainment-activities', 'active'
FROM service_categories WHERE slug='entertainment-activities'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='photo-booth');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Human Doll (Mascots)', 'human-doll-mascots', 'Life-size human doll and mascot characters for entertaining guests, photo opportunities and themed event experiences. Available in Cute, Giant, Cartoon and Couple styles.', 'Life-size human doll and mascot characters for entertaining guests, photo opportunities and themed event experiences. Available in Cute, Giant, Cartoon and Couple styles.', 2499, 'per event', '/uploads/services/fun.webp', 'entertainment-activities', 'active'
FROM service_categories WHERE slug='entertainment-activities'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='human-doll-mascots');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Cotton Candy', 'cotton-candy', 'Classic fluffy cotton candy stall with multiple flavours and colours. A sweet treat loved by guests of all ages.', 'Classic fluffy cotton candy stall with multiple flavours and colours. A sweet treat loved by guests of all ages.', 3000, 'per event', '/uploads/services/snacks.webp', 'snacks-stalls', 'active'
FROM service_categories WHERE slug='snacks-stalls'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='cotton-candy');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Pop Corn', 'pop-corn', 'Freshly popped flavoured popcorn stall with savoury and sweet varieties. Perfect for evening events and receptions.', 'Freshly popped flavoured popcorn stall with savoury and sweet varieties. Perfect for evening events and receptions.', 2500, 'per event', '/uploads/services/snacks.webp', 'snacks-stalls', 'active'
FROM service_categories WHERE slug='snacks-stalls'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='pop-corn');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Chocolate Foundation', 'chocolate-foundation', 'Elegant chocolate fountain with dipping options — fruits, marshmallows and wafers. A showpiece treat for your event.', 'Elegant chocolate fountain with dipping options — fruits, marshmallows and wafers. A showpiece treat for your event.', 5000, 'per event', '/uploads/services/snacks.webp', 'snacks-stalls', 'active'
FROM service_categories WHERE slug='snacks-stalls'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='chocolate-foundation');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Fruit Salad', 'fruit-salad', 'Fresh seasonal fruit salad station with cream and honey dressing options. Healthy and refreshing for all guests.', 'Fresh seasonal fruit salad station with cream and honey dressing options. Healthy and refreshing for all guests.', 2000, 'per event', '/uploads/services/snacks.webp', 'snacks-stalls', 'active'
FROM service_categories WHERE slug='snacks-stalls'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='fruit-salad');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Ice Cream', 'ice-cream', 'Premium ice cream parlour stall with multiple flavours and toppings. Served in cups and cones for your guests.', 'Premium ice cream parlour stall with multiple flavours and toppings. Served in cups and cones for your guests.', 3500, 'per event', '/uploads/services/snacks.webp', 'snacks-stalls', 'active'
FROM service_categories WHERE slug='snacks-stalls'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='ice-cream');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Mojito & Tea', 'mojito-tea', 'Live mojito and tea counter with fresh mint mojitos, lemon coolers and specialty teas to keep your guests refreshed.', 'Live mojito and tea counter with fresh mint mojitos, lemon coolers and specialty teas to keep your guests refreshed.', 4000, 'per event', '/uploads/services/snacks.webp', 'snacks-stalls', 'active'
FROM service_categories WHERE slug='snacks-stalls'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='mojito-tea');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Pyro Show', 'pyro-show', 'Spectacular choreographed pyro burst with colourful aerial effects for grand entries and stage reveals.', 'Spectacular choreographed pyro burst with colourful aerial effects for grand entries and stage reveals.', 299, 'per event', '/uploads/services/stage.webp', 'enter-show-down', 'active'
FROM service_categories WHERE slug='enter-show-down'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='pyro-show');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Entry Pot Fag', 'entry-pot-fag', 'Dramatic entry pot fog effect that creates a mystical low-lying fog for bride/groom entries and stage entrances.', 'Dramatic entry pot fog effect that creates a mystical low-lying fog for bride/groom entries and stage entrances.', 459, 'per event', '/uploads/services/stage.webp', 'enter-show-down', 'active'
FROM service_categories WHERE slug='enter-show-down'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='entry-pot-fag');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Paper Blast', 'paper-blast', 'High-energy confetti paper cannon blast for entries, first dance and grand celebration moments.', 'High-energy confetti paper cannon blast for entries, first dance and grand celebration moments.', 299, 'per event', '/uploads/services/stage.webp', 'enter-show-down', 'active'
FROM service_categories WHERE slug='enter-show-down'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='paper-blast');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Rose Blast', 'rose-blast', 'Romantic rose petal blast that showers the couple with fragrant petals during special moments.', 'Romantic rose petal blast that showers the couple with fragrant petals during special moments.', 299, 'per event', '/uploads/services/stage.webp', 'enter-show-down', 'active'
FROM service_categories WHERE slug='enter-show-down'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='rose-blast');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Balloon Blast', 'balloon-blast', 'Exciting balloon blast with hundreds of balloons released simultaneously for celebrations and photo moments.', 'Exciting balloon blast with hundreds of balloons released simultaneously for celebrations and photo moments.', 599, 'per event', '/uploads/services/stage.webp', 'enter-show-down', 'active'
FROM service_categories WHERE slug='enter-show-down'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='balloon-blast');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Stage Fog Setup', 'stage-fog-setup', 'Professional stage fog machine setup that creates dramatic atmospheric effects for performances and entries.', 'Professional stage fog machine setup that creates dramatic atmospheric effects for performances and entries.', 599, 'per event', '/uploads/services/stage.webp', 'enter-show-down', 'active'
FROM service_categories WHERE slug='enter-show-down'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='stage-fog-setup');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Gun Paper Blast', 'gun-paper-blast', 'Handheld confetti gun blast for instant celebration effects — perfect for couple entry and first dance moments.', 'Handheld confetti gun blast for instant celebration effects — perfect for couple entry and first dance moments.', 499, 'per event', '/uploads/services/stage.webp', 'enter-show-down', 'active'
FROM service_categories WHERE slug='enter-show-down'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='gun-paper-blast');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Welcome Girls – Breakfast', 'welcome-girls-breakfast', 'Graceful welcome girls greeting and welcoming your guests at breakfast. Fixed price booking.', 'Graceful welcome girls greeting and welcoming your guests at breakfast. Fixed price booking.', 1500, 'per event', '/uploads/services/welcomegirls.webp', 'catering-boys', 'active'
FROM service_categories WHERE slug='catering-boys'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='welcome-girls-breakfast');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Welcome Girls – Lunch', 'welcome-girls-lunch', 'Graceful welcome girls greeting and welcoming your guests at lunch. Fixed price booking.', 'Graceful welcome girls greeting and welcoming your guests at lunch. Fixed price booking.', 1500, 'per event', '/uploads/services/welcomegirls.webp', 'catering-boys', 'active'
FROM service_categories WHERE slug='catering-boys'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='welcome-girls-lunch');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Welcome Girls – Dinner', 'welcome-girls-dinner', 'Graceful welcome girls greeting and welcoming your guests at dinner. Fixed price booking.', 'Graceful welcome girls greeting and welcoming your guests at dinner. Fixed price booking.', 1500, 'per event', '/uploads/services/welcomegirls.webp', 'catering-boys', 'active'
FROM service_categories WHERE slug='catering-boys'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='welcome-girls-dinner');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Catering Boys – Breakfast', 'catering-boys-breakfast', 'Uniformed catering boys serving breakfast at your event. Fixed price booking.', 'Uniformed catering boys serving breakfast at your event. Fixed price booking.', 750, 'per event', '/uploads/services/cateringboys.webp', 'catering-boys', 'active'
FROM service_categories WHERE slug='catering-boys'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='catering-boys-breakfast');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Catering Boys – Lunch', 'catering-boys-lunch', 'Uniformed catering boys serving lunch at your event. Fixed price booking.', 'Uniformed catering boys serving lunch at your event. Fixed price booking.', 750, 'per event', '/uploads/services/cateringboys.webp', 'catering-boys', 'active'
FROM service_categories WHERE slug='catering-boys'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='catering-boys-lunch');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Catering Boys – Dinner', 'catering-boys-dinner', 'Uniformed catering boys serving dinner at your event. Fixed price booking.', 'Uniformed catering boys serving dinner at your event. Fixed price booking.', 750, 'per event', '/uploads/services/cateringboys.webp', 'catering-boys', 'active'
FROM service_categories WHERE slug='catering-boys'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='catering-boys-dinner');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Only Male Team', 'only-male-team', 'High-energy all-male dance troupe performing Bollywood, folk and western styles to energise your event. Choose 4, 5, 7 or 9 members.', 'High-energy all-male dance troupe performing Bollywood, folk and western styles to energise your event. Choose 4, 5, 7 or 9 members.', 11196, 'per event', '/uploads/services/dancers.webp', 'dancers', 'active'
FROM service_categories WHERE slug='dancers'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='only-male-team');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Only Female Team', 'only-female-team', 'Graceful all-female dance performance team with classical, semi-classical and contemporary repertoire. Choose 4, 5, 7 or 9 members.', 'Graceful all-female dance performance team with classical, semi-classical and contemporary repertoire. Choose 4, 5, 7 or 9 members.', 15196, 'per event', '/uploads/services/dancers.webp', 'dancers', 'active'
FROM service_categories WHERE slug='dancers'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='only-female-team');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Co-ED Man & Women Team', 'co-ed-man-women-team', 'Dynamic mixed-gender dance troupe with choreographed group performances for weddings and grand events. Choose 4, 6, 8, 10 or 12 members.', 'Dynamic mixed-gender dance troupe with choreographed group performances for weddings and grand events. Choose 4, 6, 8, 10 or 12 members.', 12998, 'per event', '/uploads/services/dancers.webp', 'dancers', 'active'
FROM service_categories WHERE slug='dancers'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='co-ed-man-women-team');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Digital Wedding Invitation', 'digital-wedding-invitation', 'Beautifully designed digital wedding invitation with animations, music and personalised details. Shared instantly via WhatsApp and social media.', 'Beautifully designed digital wedding invitation with animations, music and personalised details. Shared instantly via WhatsApp and social media.', 2000, 'per event', '/uploads/services/stage.webp', 'invitation', 'active'
FROM service_categories WHERE slug='invitation'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='digital-wedding-invitation');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Reception — Real Flowers', 'reception-real-flowers', 'Fresh real flower stage, entry arch and table arrangements for your reception — roses, jasmine and marigold sourced every morning.', 'Fresh real flower stage, entry arch and table arrangements for your reception — roses, jasmine and marigold sourced every morning.', 5000, 'per event', '/uploads/services/stage.webp', 'real-flowers,reception,real', 'active'
FROM service_categories WHERE slug='real-flowers'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='reception-real-flowers');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Reception — Artificial Flowers', 'reception-artificial-flowers', 'Premium quality artificial flower stage and decor for reception — lifelike blooms that stay perfect all day without wilting.', 'Premium quality artificial flower stage and decor for reception — lifelike blooms that stay perfect all day without wilting.', 6000, 'per event', '/uploads/services/stage.webp', 'real-flowers,reception,artificial', 'active'
FROM service_categories WHERE slug='real-flowers'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='reception-artificial-flowers');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Marriage — Real Flowers', 'marriage-real-flowers', 'Lush real flower mandapam, garlands and venue decoration for the wedding ceremony — traditional fragrant blooms for an authentic setup.', 'Lush real flower mandapam, garlands and venue decoration for the wedding ceremony — traditional fragrant blooms for an authentic setup.', 5000, 'per event', '/uploads/services/stage.webp', 'real-flowers,marriage,real', 'active'
FROM service_categories WHERE slug='real-flowers'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='marriage-real-flowers');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Marriage — Artificial Flowers', 'marriage-artificial-flowers', 'Long-lasting artificial flower mandapam and bridal-path decoration for the marriage ceremony — vibrant colours that photograph beautifully.', 'Long-lasting artificial flower mandapam and bridal-path decoration for the marriage ceremony — vibrant colours that photograph beautifully.', 6000, 'per event', '/uploads/services/stage.webp', 'real-flowers,marriage,artificial', 'active'
FROM service_categories WHERE slug='real-flowers'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='marriage-artificial-flowers');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Gold Style Jewellery', 'gold-style-jewellery', 'Premium gold-finish fashion jewellery sets for brides and bridesmaids — necklaces, bangles, earrings and maang tikka.', 'Premium gold-finish fashion jewellery sets for brides and bridesmaids — necklaces, bangles, earrings and maang tikka.', 3000, 'per event', '/uploads/services/stage.webp', 'fake-jewellery', 'active'
FROM service_categories WHERE slug='fake-jewellery'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='gold-style-jewellery');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Silver Style Jewellery', 'silver-style-jewellery', 'Elegant silver-finish fashion jewellery sets for weddings and ceremonies — oxidised and contemporary designs available.', 'Elegant silver-finish fashion jewellery sets for weddings and ceremonies — oxidised and contemporary designs available.', 2500, 'per event', '/uploads/services/stage.webp', 'fake-jewellery', 'active'
FROM service_categories WHERE slug='fake-jewellery'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='silver-style-jewellery');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Kundan Style Jewellery', 'kundan-style-jewellery', 'Traditional Kundan jewellery sets with intricate stonework — perfect for bridal and ethnic ceremony looks.', 'Traditional Kundan jewellery sets with intricate stonework — perfect for bridal and ethnic ceremony looks.', 3500, 'per event', '/uploads/services/stage.webp', 'fake-jewellery', 'active'
FROM service_categories WHERE slug='fake-jewellery'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='kundan-style-jewellery');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Normal Cars Entry', 'normal-cars-entry', 'Stylish decorated normal car entry for bride and groom with floral decorations and ribbon arrangements.', 'Stylish decorated normal car entry for bride and groom with floral decorations and ribbon arrangements.', 5000, 'per event', '/uploads/services/stage.webp', 'car-entry', 'active'
FROM service_categories WHERE slug='car-entry'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='normal-cars-entry');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Luxury Cars Entry', 'luxury-cars-entry', 'Premium luxury car entry package with high-end vehicles decorated for your grand wedding arrival.', 'Premium luxury car entry package with high-end vehicles decorated for your grand wedding arrival.', 15000, 'per event', '/uploads/services/stage.webp', 'car-entry', 'active'
FROM service_categories WHERE slug='car-entry'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='luxury-cars-entry');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Traditional Aarthi Plate', 'traditional-aarthi-plate', 'Beautifully decorated traditional aarthi plate with diyas, flowers and accessories for wedding and religious ceremonies.', 'Beautifully decorated traditional aarthi plate with diyas, flowers and accessories for wedding and religious ceremonies.', 1500, 'per event', '/uploads/services/stage.webp', 'aarthi-plate', 'active'
FROM service_categories WHERE slug='aarthi-plate'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='traditional-aarthi-plate');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Bridal Makeup & Styling', 'bridal-makeup-styling', 'Complete bridal makeup with HD and airbrush techniques, hair styling, saree draping and jewellery coordination for your big day.', 'Complete bridal makeup with HD and airbrush techniques, hair styling, saree draping and jewellery coordination for your big day.', 12000, 'per event', '/uploads/services/bridal.webp', 'bridal-groom-styling', 'active'
FROM service_categories WHERE slug='bridal-groom-styling'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='bridal-makeup-styling');
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, tags, status)
SELECT id, 'Mehanti Bridal', 'mehanti-bridal', 'Full bridal Mehndi with intricate traditional patterns from renowned artists. Includes detailed design on both hands and feet.', 'Full bridal Mehndi with intricate traditional patterns from renowned artists. Includes detailed design on both hands and feet.', 8000, 'per event', '/uploads/services/mehandi.webp', 'bridal-groom-styling', 'active'
FROM service_categories WHERE slug='bridal-groom-styling'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='mehanti-bridal');

-- ── Photography (special case: base package + variant filters) ──
INSERT INTO services (category_id, title, slug, short_description, description, price, price_unit, image, status)
SELECT id, 'ELLCY Photography Package', 'ellcy-photography-package', 'Our complete wedding photography package captures every precious moment of your celebration — from the first look to the last dance. Includes full-day coverage by a professional ph', 'Our complete wedding photography package captures every precious moment of your celebration — from the first look to the last dance. Includes full-day coverage by a professional photographer, 300+ edited high-resolution photos, a private online gallery, and a premium printed album delivered within 30 days.', 80000, 'per event', '/uploads/services/photo.webp', 'active'
FROM service_categories WHERE slug='photography'
AND NOT EXISTS (SELECT 1 FROM services WHERE slug='ellcy-photography-package');

INSERT INTO service_packages (service_id, pkg_key, label, price, is_default, status)
SELECT id, 'wedding', 'Wedding', 80000, 1, 'active'
FROM services WHERE slug='ellcy-photography-package'
AND NOT EXISTS (SELECT 1 FROM service_packages WHERE service_id=(SELECT id FROM services WHERE slug='ellcy-photography-package') AND pkg_key='wedding');
INSERT INTO service_packages (service_id, pkg_key, label, price, is_default, status)
SELECT id, 'prewedding', 'Pre-Wedding', 160000, 0, 'active'
FROM services WHERE slug='ellcy-photography-package'
AND NOT EXISTS (SELECT 1 FROM service_packages WHERE service_id=(SELECT id FROM services WHERE slug='ellcy-photography-package') AND pkg_key='prewedding');
INSERT INTO service_packages (service_id, pkg_key, label, price, is_default, status)
SELECT id, 'postwedding', 'Post-Wedding', 160000, 0, 'active'
FROM services WHERE slug='ellcy-photography-package'
AND NOT EXISTS (SELECT 1 FROM service_packages WHERE service_id=(SELECT id FROM services WHERE slug='ellcy-photography-package') AND pkg_key='postwedding');
INSERT INTO service_packages (service_id, pkg_key, label, price, is_default, status)
SELECT id, 'engagement', 'Engagement', 80000, 1, 'active'
FROM services WHERE slug='ellcy-photography-package'
AND NOT EXISTS (SELECT 1 FROM service_packages WHERE service_id=(SELECT id FROM services WHERE slug='ellcy-photography-package') AND pkg_key='engagement');
