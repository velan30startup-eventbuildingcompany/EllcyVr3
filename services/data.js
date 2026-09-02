// ============================================================
// data.js — ELLCY Patch 7.0 (Database-Driven Build)
// This file now loads the live service catalog from the MySQL
// database via GET /api/services and GET /api/categories.
// If the API is unreachable (DB not set up yet, offline dev,
// setup.php not run), it falls back to the bundled static
// catalog below so the site never breaks.
//
// IMPORTANT: this file exists in three physical locations
// (js/data.js, services/data.js, public/js/data.js) because it is
// referenced by many static HTML pages at different folder depths.
// Keep all three copies in sync — they differ ONLY in the
// __ELLCY_API_BASE__ relative path at the top.
// ============================================================
// ============================================================
// data.js — ELLCY Patch 6.0  (Production Build)
// Changes:
//  C1: Full 17 Wedding Services added with sub-services
//  C2: Chenda Melam performers section removed from data
//  C3: All new sub-services routing added
// ============================================================

const STATIC_SERVICES_DATA = {

  /* ── DECORATION: Light ─────────────────────────────────── */
  'light-decoration': [
    { id:50, title:'Light Set Up In Party Hall',
      description:'Professional indoor party hall lighting setup with RGB LED strips, fairy lights, spotlights, and ambient colour-changing fixtures. Transforms any hall into a stunning venue.',
      image:'../uploads/services/lighting.webp', event_types:['wedding'] },
    { id:51, title:'Light Set Up In Out Door',
      description:'High-impact outdoor lighting setup with weatherproof LED fixtures, string lights, uplighters, and powerful floodlights. Perfect for open-air events, lawns and rooftop celebrations.',
      image:'../uploads/services/lighting.webp', event_types:['wedding'] },
  ],

  /* ── DECORATION: Stage ─────────────────────────────────── */
  'stage-decoration': [
    { id:1, title:'Party Hall Decoration',
      description:'Breathtaking party hall stage setups crafted with premium backdrops, LED panels, floral arrangements and full mood lighting — designed to impress every guest.',
      image:'../uploads/services/stage.webp', event_types:['wedding'], descPage:true },
    { id:2, title:'Outdoor Decoration',
      description:'Grand outdoor stage setups built for open-air events with weather-resistant structures, floral archways, draping and atmospheric lighting installations.',
      image:'../uploads/services/stage.webp', event_types:['wedding'], descPage:true },
    { id:3, title:'Hotel Decoration',
      description:'Luxury hotel venue decoration packages including stage design, table centrepieces, floral walls, entrance arches and complete hall transformation.',
      image:'../uploads/services/stage.webp', event_types:['wedding'], descPage:true },
  ],

  /* ── PHOTOGRAPHY (single package) ─────────────────────── */
  /* Photography uses PHOTOGRAPHY_PACKAGE + PHOTOGRAPHY_FILTERS below */

  /* ── FOOD (sub-services: Dinner, Breakfast, Lunch) ──────── */
  'food': [
    { id:70, title:'Dinner Catering',
      description:'Full-course dinner catering service with multiple cuisines, live counters, and professional serving staff for your event.',
      base_price:450, image:'../uploads/services/catering.webp', event_types:['wedding'] },
    { id:71, title:'Breakfast Catering',
      description:'Fresh and wholesome breakfast spread with South Indian, North Indian and continental options. Ideal for morning ceremonies.',
      base_price:250, image:'../uploads/services/catering.webp', event_types:['wedding'] },
    { id:72, title:'Lunch Catering',
      description:'Elaborate lunch catering with traditional thali meals, buffet spreads and live cooking stations for afternoon events.',
      base_price:350, image:'../uploads/services/catering.webp', event_types:['wedding'] },
  ],

  /* ── DJ (7 packages) ────────────────────────────────────── */
  'dj': [
    { id:15, title:'DJ Starter Package',
      description:'Entry-level DJ setup with quality sound system, basic LED lighting and a curated playlist for small, intimate celebrations.',
      base_price:9999, image:'../uploads/services/dj.webp', event_types:['wedding'],
      rating:4.7, reviews:32, experienceYears:3, tag:'' },
    { id:16, title:'DJ Silver Package',
      description:'Mid-range DJ package with enhanced sound system, moving head lights and fog machine. Great for up to 200 guests.',
      base_price:14999, image:'../uploads/services/dj.webp', event_types:['wedding'],
      rating:4.8, reviews:58, experienceYears:4, tag:'Popular' },
    { id:17, title:'DJ Gold Package',
      description:'Premium DJ experience with professional-grade sound towers, LED wash lights, laser effects and a customised playlist.',
      base_price:17999, image:'../uploads/services/dj.webp', event_types:['wedding'],
      rating:4.9, reviews:123, experienceYears:5, tag:'Popular' },
    { id:18, title:'DJ Platinum Package',
      description:'High-impact DJ setup with dual sub-woofers, full LED stage rig, haze machines and live mixing. Perfect for 500 guests.',
      base_price:24999, image:'../uploads/services/dj.webp', event_types:['wedding'],
      rating:5.0, reviews:13, experienceYears:5, tag:'' },
    { id:19, title:'DJ Diamond Package',
      description:'Elite DJ performance with touring-grade line-array speakers, full moving-head truss system, confetti cannons and CO₂ jets.',
      base_price:34999, image:'../uploads/services/dj.webp', event_types:['wedding'],
      rating:4.9, reviews:41, experienceYears:6, tag:'' },
    { id:20, title:'DJ Ultra Package',
      description:'Luxury DJ event experience with concert-level sound, full-colour LED video wall backdrop and a professional MC.',
      base_price:47999, image:'../uploads/services/dj.webp', event_types:['wedding'],
      rating:5.0, reviews:7, experienceYears:7, tag:'' },
    { id:21, title:'DJ Grand Celebration Package',
      description:'The ultimate DJ package — full production sound & lighting, pyrotechnic sparks, mirror ball, cold fire jets and a sound engineer.',
      base_price:59999, image:'../uploads/services/dj.webp', event_types:['wedding'],
      rating:5.0, reviews:4, experienceYears:8, tag:'' },
  ],

  /* ── MUSIC PERFORMERS (sub: Chenda Melam, Nadhaswaram & Thavil, BandSet, Melam Set) */

  /* ── CHENDA MELAM ────────────────────────────────────────── */
  'chenda-melam': [
    { id:60, title:'Chenda Melam – Standard',
      description:'Traditional Kerala Chenda Melam percussion ensemble with experienced artists performing authentic rhythmic beats for processions and auspicious ceremonies.',
      base_price:12000, image:'../uploads/services/chenda-melam.webp', event_types:['wedding'] },
    { id:61, title:'Chenda Melam – Grand Procession',
      description:'Large-scale Chenda Melam troupe with full brass and percussion ensemble ideal for wedding processions, temple festivals and grand cultural events.',
      base_price:22000, image:'../uploads/services/chenda-melam.webp', event_types:['wedding'] },
  ],

  /* ── NADHASWARAM & THAVIL ───────────────────────────────── */
  'nadhaswaram-thavil': [
    { id:80, title:'Nadhaswaram & Thavil – Classic',
      description:'Traditional Nadhaswaram and Thavil duo performance for auspicious ceremonies, wedding rituals and processions. Brings divine blessings and festive energy.',
      base_price:8000, image:'../uploads/services/nadhaswaram.webp', event_types:['wedding'] },
    { id:81, title:'Nadhaswaram & Thavil – Grand',
      description:'Full ensemble Nadhaswaram and Thavil group performance ideal for grand weddings, temple events and large-scale cultural celebrations.',
      base_price:15000, image:'../uploads/services/nadhaswaram.webp', event_types:['wedding'] },
  ],

  /* ── BAND SET ───────────────────────────────────────────── */
  'band-set': [
    { id:82, title:'Band Set – 6 Members',
      description:'Compact 6-member brass band for intimate wedding entries and smaller processions. Uniformed performers with a curated wedding classics repertoire.',
      base_price:11994, image:'../uploads/services/bandset.webp', event_types:['wedding'] },
    { id:83, title:'Band Set – 8 Members',
      description:'8-member ensemble delivering a fuller brass sound, ideal for mid-sized wedding processions and grand entry ceremonies.',
      base_price:15992, image:'../uploads/services/bandset.webp', event_types:['wedding'] },
    { id:84, title:'Band Set – 10 Members',
      description:'Impressive 10-member brass band for larger wedding ceremonies, baarats and grand entries with high energy and showmanship.',
      base_price:19990, image:'../uploads/services/bandset.webp', event_types:['wedding'] },
    { id:85, title:'Band Set – 12 Members',
      description:'Grand 12-member ensemble with uniformed performers and drum major. A commanding presence for large-scale wedding processions.',
      base_price:23988, image:'../uploads/services/bandset.webp', event_types:['wedding'] },
    { id:86, title:'Band Set – 15 Members',
      description:'Premium 15-member brass band with LED costumes and choreographed drum majors. Makes every procession a visual and musical spectacle.',
      base_price:29985, image:'../uploads/services/bandset.webp', event_types:['wedding'] },
    { id:87, title:'Band Set – 18 Members',
      description:'Elite 18-member ensemble delivering a wall of sound and dazzling performance. Perfect for extravagant weddings and grand baraat celebrations.',
      base_price:35982, image:'../uploads/services/bandset.webp', event_types:['wedding'] },
    { id:88, title:'Band Set – 20 Members',
      description:'Our flagship 20-member full brass band — the ultimate grand entry experience with full LED production, drum corps and maximum energy.',
      base_price:39980, image:'../uploads/services/bandset.webp', event_types:['wedding'] },
  ],

  /* ── MELAM SET ─────────────────────────────────────── */
  'melam-set': [
    { id:90, title:'Melam Set – 4 Members',
      description:'Compact 4-member melam procession set for intimate ceremonies, home poojas and smaller festive occasions.',
      base_price:7994, image:'../uploads/services/musical_band.webp', event_types:['wedding'] },
    { id:91, title:'Melam Set – 6 Members',
      description:'6-member traditional percussion ensemble, ideal for mid-sized processions, griha pravesams and auspicious family functions.',
      base_price:11994, image:'../uploads/services/musical_band.webp', event_types:['wedding'] },
    { id:92, title:'Melam Set – 8 Members',
      description:'8-member melam set delivering a fuller, more resonant sound for wedding processions and temple festival ceremonies.',
      base_price:15992, image:'../uploads/services/musical_band.webp', event_types:['wedding'] },
    { id:93, title:'Melam Set – 10 Members',
      description:'Grand 10-member ensemble ideal for larger wedding processions, temple festivals and elaborate ceremonial routes.',
      base_price:19990, image:'../uploads/services/musical_band.webp', event_types:['wedding'] },
    { id:94, title:'Melam Set – 12 Members',
      description:'12-member percussion ensemble creating a powerful, rhythmic atmosphere for grand weddings and major festival events.',
      base_price:23988, image:'../uploads/services/musical_band.webp', event_types:['wedding'] },
    { id:95, title:'Melam Set – 15 Members',
      description:'Premium 15-member melam set for large-scale processions and elaborate cultural celebrations with full devotional energy.',
      base_price:29985, image:'../uploads/services/musical_band.webp', event_types:['wedding'] },
    { id:96, title:'Melam Set – 18 Members',
      description:'Elite 18-member ensemble delivering an immersive wall of percussion for extravagant wedding processions and grand events.',
      base_price:35982, image:'../uploads/services/musical_band.webp', event_types:['wedding'] },
    { id:97, title:'Melam Set – 20 Members',
      description:'Our flagship 20-member grand procession ensemble — the ultimate traditional melam experience for the most prestigious ceremonies.',
      base_price:39980, image:'../uploads/services/musical_band.webp', event_types:['wedding'] },
  ],

    /* ── BOUNCERS (1 service) ───────────────────────────────── */
  'bouncers': [
    { id:40, title:'Event Security & Bouncers',
      description:'Professional event security and bouncers for crowd management, entry control and event safety.',
      base_price:1400, image:'../uploads/services/bouncer.webp', event_types:['wedding'] },
  ],

  /* ── ENTERTAINMENT ACTIVITIES ───────────────────────────── */
  'entertainment-activities': [
    { id:90, title:'360 Degree Camera',
      description:'Immersive 360° camera booth for your event — captures slow-motion videos of guests for instant sharing and lasting memories.',
      base_price:8000, image:'../uploads/services/photobooth.webp', event_types:['wedding'] },
    { id:91, title:'Photo Booth',
      description:'Fully branded photo booth with props, instant prints and digital sharing. A crowd favourite at weddings and parties.',
      base_price:6000, image:'../uploads/services/photobooth.webp', event_types:['wedding'] },
    { id:92, title:'Human Doll (Mascots)',
      description:'Life-size human doll and mascot characters for entertaining guests, photo opportunities and themed event experiences. Available in Cute, Giant, Cartoon and Couple styles.',
      base_price:2499, image:'../uploads/services/fun.webp', event_types:['wedding'] },
  ],

  /* ── SNACKS & STALLS ────────────────────────────────────── */
  'snacks-stalls': [
    { id:41, title:'Cotton Candy',
      description:'Classic fluffy cotton candy stall with multiple flavours and colours. A sweet treat loved by guests of all ages.',
      base_price:3000, image:'../uploads/services/snacks.webp', event_types:['wedding'] },
    { id:42, title:'Pop Corn',
      description:'Freshly popped flavoured popcorn stall with savoury and sweet varieties. Perfect for evening events and receptions.',
      base_price:2500, image:'../uploads/services/snacks.webp', event_types:['wedding'] },
    { id:43, title:'Chocolate Foundation',
      description:'Elegant chocolate fountain with dipping options — fruits, marshmallows and wafers. A showpiece treat for your event.',
      base_price:5000, image:'../uploads/services/snacks.webp', event_types:['wedding'] },
    { id:44, title:'Fruit Salad',
      description:'Fresh seasonal fruit salad station with cream and honey dressing options. Healthy and refreshing for all guests.',
      base_price:2000, image:'../uploads/services/snacks.webp', event_types:['wedding'] },
    { id:45, title:'Ice Cream',
      description:'Premium ice cream parlour stall with multiple flavours and toppings. Served in cups and cones for your guests.',
      base_price:3500, image:'../uploads/services/snacks.webp', event_types:['wedding'] },
    { id:46, title:'Mojito & Tea',
      description:'Live mojito and tea counter with fresh mint mojitos, lemon coolers and specialty teas to keep your guests refreshed.',
      base_price:4000, image:'../uploads/services/snacks.webp', event_types:['wedding'] },
  ],

  /* ── ENTER SHOW DOWN ────────────────────────────────────── */
  'enter-show-down': [
    { id:95, title:'Pyro Show',
      description:'Spectacular choreographed pyro burst with colourful aerial effects for grand entries and stage reveals.',
      base_price:299, image:'../uploads/services/stage.webp', event_types:['wedding'] },
    { id:96, title:'Entry Pot Fag',
      description:'Dramatic entry pot fog effect that creates a mystical low-lying fog for bride/groom entries and stage entrances.',
      base_price:459, image:'../uploads/services/stage.webp', event_types:['wedding'] },
    { id:97, title:'Paper Blast',
      description:'High-energy confetti paper cannon blast for entries, first dance and grand celebration moments.',
      base_price:299, image:'../uploads/services/stage.webp', event_types:['wedding'] },
    { id:98, title:'Rose Blast',
      description:'Romantic rose petal blast that showers the couple with fragrant petals during special moments.',
      base_price:299, image:'../uploads/services/stage.webp', event_types:['wedding'] },
    { id:99, title:'Balloon Blast',
      description:'Exciting balloon blast with hundreds of balloons released simultaneously for celebrations and photo moments.',
      base_price:599, image:'../uploads/services/stage.webp', event_types:['wedding'] },
    { id:100, title:'Stage Fog Setup',
      description:'Professional stage fog machine setup that creates dramatic atmospheric effects for performances and entries.',
      base_price:599, image:'../uploads/services/stage.webp', event_types:['wedding'] },
    { id:101, title:'Gun Paper Blast',
      description:'Handheld confetti gun blast for instant celebration effects — perfect for couple entry and first dance moments.',
      base_price:499, image:'../uploads/services/stage.webp', event_types:['wedding'] },
  ],

  /* ── CATERING BOYS / WELCOME GIRLS ─────────────────────── */
  'catering-boys': [
    { id:200, title:'Welcome Girls – Breakfast',
      description:'Graceful welcome girls greeting and welcoming your guests at breakfast. Fixed price booking.',
      base_price:1500, image:'../uploads/services/welcomegirls.webp', event_types:['wedding'] },
    { id:201, title:'Welcome Girls – Lunch',
      description:'Graceful welcome girls greeting and welcoming your guests at lunch. Fixed price booking.',
      base_price:1500, image:'../uploads/services/welcomegirls.webp', event_types:['wedding'] },
    { id:202, title:'Welcome Girls – Dinner',
      description:'Graceful welcome girls greeting and welcoming your guests at dinner. Fixed price booking.',
      base_price:1500, image:'../uploads/services/welcomegirls.webp', event_types:['wedding'] },
    { id:203, title:'Catering Boys – Breakfast',
      description:'Uniformed catering boys serving breakfast at your event. Fixed price booking.',
      base_price:750, image:'../uploads/services/cateringboys.webp', event_types:['wedding'] },
    { id:204, title:'Catering Boys – Lunch',
      description:'Uniformed catering boys serving lunch at your event. Fixed price booking.',
      base_price:750, image:'../uploads/services/cateringboys.webp', event_types:['wedding'] },
    { id:205, title:'Catering Boys – Dinner',
      description:'Uniformed catering boys serving dinner at your event. Fixed price booking.',
      base_price:750, image:'../uploads/services/cateringboys.webp', event_types:['wedding'] },
  ],

  /* ── DANCERS ─────────────────────────────────────────────── */
  'dancers': [
    { id:36, title:'Only Male Team',
      description:'High-energy all-male dance troupe performing Bollywood, folk and western styles to energise your event. Choose 4, 5, 7 or 9 members.',
      base_price:11196, image:'../uploads/services/dancers.webp', event_types:['wedding'] },
    { id:37, title:'Only Female Team',
      description:'Graceful all-female dance performance team with classical, semi-classical and contemporary repertoire. Choose 4, 5, 7 or 9 members.',
      base_price:15196, image:'../uploads/services/dancers.webp', event_types:['wedding'] },
    { id:38, title:'Co-ED Man & Women Team',
      description:'Dynamic mixed-gender dance troupe with choreographed group performances for weddings and grand events. Choose 4, 6, 8, 10 or 12 members.',
      base_price:12998, image:'../uploads/services/dancers.webp', event_types:['wedding'] },
  ],

  /* ── INVITATION (1 service) ─────────────────────────────── */
  'invitation': [
    { id:102, title:'Digital Wedding Invitation',
      description:'Beautifully designed digital wedding invitation with animations, music and personalised details. Shared instantly via WhatsApp and social media.',
      base_price:2000, image:'../uploads/services/stage.webp', event_types:['wedding'] },
  ],

  /* ── REAL FLOWERS (1 service) ───────────────────────────── */
  'real-flowers': [
    /* ── Reception sub-services ────────────── */
    { id:103, title:'Reception — Real Flowers',
      description:'Fresh real flower stage, entry arch and table arrangements for your reception — roses, jasmine and marigold sourced every morning.',
      base_price:5000, image:'../uploads/services/stage.webp', event_types:['wedding'],
      eventGroup:'reception', flowerType:'real' },
    { id:104, title:'Reception — Artificial Flowers',
      description:'Premium quality artificial flower stage and decor for reception — lifelike blooms that stay perfect all day without wilting.',
      base_price:6000, image:'../uploads/services/stage.webp', event_types:['wedding'],
      eventGroup:'reception', flowerType:'artificial' },
    /* ── Marriage sub-services ─────────────── */
    { id:113, title:'Marriage — Real Flowers',
      description:'Lush real flower mandapam, garlands and venue decoration for the wedding ceremony — traditional fragrant blooms for an authentic setup.',
      base_price:5000, image:'../uploads/services/stage.webp', event_types:['wedding'],
      eventGroup:'marriage', flowerType:'real' },
    { id:114, title:'Marriage — Artificial Flowers',
      description:'Long-lasting artificial flower mandapam and bridal-path decoration for the marriage ceremony — vibrant colours that photograph beautifully.',
      base_price:6000, image:'../uploads/services/stage.webp', event_types:['wedding'],
      eventGroup:'marriage', flowerType:'artificial' },
  ],

  /* ── FAKE JEWELLERY ─────────────────────────────────────── */
  'fake-jewellery': [
    { id:104, title:'Gold Style Jewellery',
      description:'Premium gold-finish fashion jewellery sets for brides and bridesmaids — necklaces, bangles, earrings and maang tikka.',
      base_price:3000, image:'../uploads/services/stage.webp', event_types:['wedding'] },
    { id:105, title:'Silver Style Jewellery',
      description:'Elegant silver-finish fashion jewellery sets for weddings and ceremonies — oxidised and contemporary designs available.',
      base_price:2500, image:'../uploads/services/stage.webp', event_types:['wedding'] },
    { id:106, title:'Kundan Style Jewellery',
      description:'Traditional Kundan jewellery sets with intricate stonework — perfect for bridal and ethnic ceremony looks.',
      base_price:3500, image:'../uploads/services/stage.webp', event_types:['wedding'] },
  ],

  /* ── CAR ENTRY ──────────────────────────────────────────── */
  'car-entry': [
    { id:107, title:'Normal Cars Entry',
      description:'Stylish decorated normal car entry for bride and groom with floral decorations and ribbon arrangements.',
      base_price:5000, image:'../uploads/services/stage.webp', event_types:['wedding'] },
    { id:108, title:'Luxury Cars Entry',
      description:'Premium luxury car entry package with high-end vehicles decorated for your grand wedding arrival.',
      base_price:15000, image:'../uploads/services/stage.webp', event_types:['wedding'] },
  ],

  /* ── AARTHI PLATE (1 service) ───────────────────────────── */
  'aarthi-plate': [
    { id:109, title:'Traditional Aarthi Plate',
      description:'Beautifully decorated traditional aarthi plate with diyas, flowers and accessories for wedding and religious ceremonies.',
      base_price:1500, image:'../uploads/services/stage.webp', event_types:['wedding'] },
  ],

  /* ── PHOTOGRAPHY: 2 individually-routed packages ────────── */
  'photography': [
    { id:701, title:'Photography — Photo Package',
      description:'Professional photo-only coverage for your full-day event — dedicated photographer, edited gallery delivered digitally.',
      base_price:25000, image:'../uploads/services/photo.webp', event_types:['wedding'], descPage:true },
    { id:702, title:'Photography — Photo + Video',
      description:'Complete photo and cinematic video coverage — professional photographer plus a videography team with edited highlight reel.',
      base_price:30000, image:'../uploads/services/photo.webp', event_types:['wedding'], descPage:true },
  ],

  /* ── BRIDAL & GROOM MAKEUP ──────────────────────────────── */
  'bridal-groom-styling': [
    { id:8,  title:'Bridal Makeup & Styling',
      description:'Complete bridal makeup with HD and airbrush techniques, hair styling, saree draping and jewellery coordination for your big day.',
      base_price:12000, image:'../uploads/services/bridal.webp', event_types:['wedding'] },
    { id:9,  title:'Mehanti Bridal',
      description:'Full bridal Mehndi with intricate traditional patterns from renowned artists. Includes detailed design on both hands and feet.',
      base_price:8000, image:'../uploads/services/mehandi.webp', event_types:['wedding'] },
  ],

};

// (ALL_SERVICES is computed below, after live data loads)

/* ── Label map ──────────────────────────────────────────────── */
const LABEL_MAP = {
  'decoration':               'Decoration',
  'stage-decoration':         'Stage Decoration',
  'light-decoration':         'Light Decoration',
  'photography':              'Photography',
  'food':                     'Food',
  'dj':                       'DJ',
  'music-performers':         'Music Performers',
  'musical-band':             'Musical Band',
  'chenda-melam':             'Chenda Melam',
  'nadhaswaram-thavil':       'Nadhaswaram & Thavil',
  'nadhaswaram-reception':    'Nadhaswaram & Thavil — Reception',
  'nadhaswaram-marriage':     'Nadhaswaram & Thavil — Marriage',
  'band-set':                 'Band Set',
  'melam-set':                'Melam Set',
  'bouncers':                 "Bouncer's",
  'entertainment-activities': 'Entertainment Activities',
  'human-doll':               'Human Doll (Mascots)',
  '360-camera':               '360° Degree Camera',
  'photo-booth':              'Photo Booth',
  'snacks-stalls':            "Snacks & Stalls",
  'enter-show-down':          'Enter Show Down',
  'catering-boys':            'Catering Boys / Welcome Girls',
  'dancers':                  'Dancers',
  'invitation':               'Invitation',
  'real-flowers':             'Real Flowers',
  'real-flowers-reception':   'Real Flowers — Reception',
  'real-flowers-marriage':    'Real Flowers — Marriage',
  'fake-jewellery':           'Fake Jewellery',
  'car-entry':                'Car Entry',
  'aarthi-plate':             'Aarthi Plate',
  'bridal-groom-styling':     'Bridal & Groom Make Up',
  'mehandi':                  'Mehndi',
  'cake-decoration':          'Cake & Decoration',
  'wedding':                  'Wedding Events',
  'birthday':                 'Birthday Events',
  'college':                  'College Events',
  'temple':                   'Temple Events',
};

/* ── Photography: single base package + filter modifiers ───── */
const STATIC_PHOTOGRAPHY_BASE_PRICE = 80000;
const STATIC_PHOTOGRAPHY_PACKAGE = {
  id:    100,
  title: 'ELLCY Photography Package',
  description: 'Our complete wedding photography package captures every precious moment of your celebration — from the first look to the last dance. Includes full-day coverage by a professional photographer, 300+ edited high-resolution photos, a private online gallery, and a premium printed album delivered within 30 days.',
  image: '../uploads/services/photo.webp',
};
const STATIC_PHOTOGRAPHY_FILTERS = [
  { key:'wedding',     label:'Wedding',     addPrice:0 },
  { key:'prewedding',  label:'Pre-Wedding', addPrice:80000 },
  { key:'postwedding', label:'Post-Wedding',addPrice:80000 },
  { key:'engagement',  label:'Engagement',  addPrice:0 },
];

/* ── Decoration sub-types ───────────────────────────────────── */
const DECORATION_SUBTYPES = [
  { name:'Light Decoration', slug:'light-decoration', img:'../uploads/services/lighting.webp',
    desc:'Indoor & outdoor professional lighting setups for your event venue.' },
  { name:'Stage Decoration', slug:'stage-decoration', img:'../uploads/services/stage.webp',
    desc:'Elegant stage setups, backdrops, floral arrangements and full hall transformations.' },
];

/* ── Music Performers sub-types ─────────────────────────────── */
const MUSICAL_BAND_SUBTYPES = [
  { name:'Chenda Melam',        slug:'chenda-melam',        img:'../uploads/services/music-chenda-melam.webp',
    desc:'Traditional Kerala Chenda Melam percussion ensemble for processions and celebrations.' },
  { name:'Nadhaswaram & Thavil',slug:'nadhaswaram-thavil',  img:'../uploads/services/music-nadhaswaram-thavil.webp',
    desc:'Classical Nadhaswaram and Thavil duo for wedding rituals and auspicious ceremonies.' },
  { name:'Band Set',            slug:'band-set',            img:'../uploads/services/music-band-set.webp',
    desc:'Professional brass band for baraat, processions and grand entry ceremonies.' },
  { name:'Melam Set',           slug:'melam-set',           img:'../uploads/services/music-melam-set.webp',
    desc:'Traditional melam set for poojas, home events and large temple celebrations.' },
];

/* ── Home circles ───────────────────────────────────────────── */
const HOME_CATEGORIES = [
  { id:'dj',                    name:'DJ',                              image:'uploads/services/dj.webp',           hidden:false },
  { id:'bridal-groom-styling',  name:'Bridal & Groom Make Up',         image:'uploads/services/bridal.webp',       hidden:false },
  { id:'decoration',            name:'Decoration',                  image:'uploads/services/decoration-stage-indoor.webp', hidden:false, slug:'decoration' },
  { id:'catering-boys',         name:'Catering Boys / Welcome Girls',   image:'uploads/services/catering-boys.webp', hidden:false },
  { id:'entertainment-activities', name:'Entertainment Activities',     image:'uploads/services/entertainment-mascots.webp', hidden:false },
  { id:'snacks-stalls',         name:"Snacks & Stalls",                 image:'uploads/services/snacks-popcorn.webp', hidden:false },
  { id:'photography',           name:'Photography',                     image:'uploads/services/photography.webp', hidden:true },
  { id:'music-performers',      name:'Music Performers',                image:'uploads/services/music-band-set.webp', hidden:true, slug:'musical-band' },
  { id:'bouncers',              name:"Bouncer's",                       image:'uploads/services/bouncers.webp', hidden:true },
  { id:'dancers',               name:'Dancers',                         image:'uploads/services/dancers-coed.webp', hidden:true },
  { id:'food',                  name:'Food',                            image:'uploads/services/catering.webp',         hidden:true },
];

/* ── Home event tiles ───────────────────────────────────────── */
const HOME_EVENTS = [
  { id:'wedding',  title:'Wedding Events',  desc:'Elegant wedding arrangements and full event management.',  image:'uploads/category/wedding/service1.webp',  comingSoon:false },
  { id:'birthday', title:'Birthday Events', desc:'Fun and colourful birthday party setups for all ages.',    image:'uploads/category/birthday/service2.webp', comingSoon:true  },
  { id:'college',  title:'College Events',  desc:'Full-stage setup and sound systems for college fests.',   image:'uploads/category/college/service3.webp',   comingSoon:true  },
  { id:'temple',   title:'Temple Events',   desc:'Traditional temple decorations and sound systems.',       image:'uploads/category/temple/service4.webp',    comingSoon:true  },
];

/* ── Wedding category page mappings — 17 services ──────────── */
const CATEGORY_MAPPINGS = {
  wedding: [
    { name:'Decoration',                slug:'decoration',               img:'../uploads/services/decoration-stage-indoor.webp' },
    { name:'Photography',                   slug:'photography',              img:'../uploads/services/photography.webp' },
    { name:'Breakfast',                     slug:'food-breakfast',           img:'../uploads/services/food-veg.webp' },
    { name:'Lunch',                         slug:'food-lunch',               img:'../uploads/services/food-veg.webp' },
    { name:'Dinner',                        slug:'food-dinner',              img:'../uploads/services/food-buffet-veg.webp' },
    { name:'DJ',                            slug:'dj',                       img:'../uploads/services/dj.webp' },
    { name:'Music Performers',              slug:'musical-band',             img:'../uploads/services/music-band-set.webp' },
    { name:"Bouncer's",                     slug:'bouncers',                 img:'../uploads/services/bouncers.webp' },
    { name:'Entertainment Activities',      slug:'entertainment-activities', img:'../uploads/services/entertainment-mascots.webp' },
    { name:"Snacks & Stalls",               slug:'snacks-stalls',            img:'../uploads/services/snacks-popcorn.webp' },
    { name:'Enter Show Down',               slug:'enter-show-down',          img:'../uploads/services/entershow-pyro-show.webp' },
    { name:'Catering Boys / Welcome Girls', slug:'catering-boys',           img:'../uploads/services/catering-boys.webp' },
    { name:'Dancers',                       slug:'dancers',                  img:'../uploads/services/dancers-coed.webp' },
    { name:'Real Flowers',                  slug:'real-flowers',             img:'../uploads/services/flowers-decoration-1.webp' },
    { name:'Fake Jewellery',                slug:'fake-jewellery',           img:'../uploads/services/jewellery-gold.webp' },
    { name:'Car Entry',                     slug:'car-entry',                img:'../uploads/services/car-entry-luxury.webp' },
    { name:'Bridal & Groom Make Up',        slug:'bridal-groom-styling',     img:'../uploads/services/bridal.webp' },
    { name:'Plates Decoration',             slug:'plates-decoration',        img:'../uploads/services/decoration-stage-hotel.webp' },
  ],
  birthday: [],
  college:  [],
  temple:   [],
};

/* ── Enquiry services ───────────────────────────────────────── */
const ENQUIRY_SERVICES = {
  wedding:  ['Photography','Stage Decoration','Light Decoration','DJ','Bridal & Groom Make Up','Music Performers','Chenda Melam','Nadhaswaram & Thavil','Band Set','Melam Set','Food','Entertainment Activities','Snacks & Stalls','Enter Show Down','Catering Boys / Welcome Girls','Dancers','Invitation','Real Flowers','Fake Jewellery','Car Entry','Aarthi Plate','Plates Decoration',"Bouncer's"],
  birthday: ['Photography','Stage Decoration','DJ','Food','Musical Band','Light Decoration','Snacks & Stalls'],
  college:  ['Stage Decoration','DJ','Photography',"Bouncer's",'Snacks & Stalls'],
  temple:   ['Photography','Chenda Melam','DJ','Stage Decoration',"Bouncer's"],
  others:   ['Photography','Stage Decoration','Light Decoration','Music Performers'],
};

/* ============================================================
   LIVE DATA LOADER — fetches the real catalog from MySQL via
   the /api/services endpoint and replaces the static fallback
   data above. Runs synchronously so every script loaded after
   this one (services.js, category.js, script.js, booking.js,
   service-desc.js, service_details.js) can keep using
   SERVICES_DATA / PHOTOGRAPHY_PACKAGE / PHOTOGRAPHY_FILTERS /
   PHOTOGRAPHY_BASE_PRICE / ALL_SERVICES exactly as before, with
   zero changes to those files.
   ============================================================ */
(function () {
  var API_BASE = '../'; // resolved relative to this file's own folder

  function resolveUrl(rel) {
    try {
      var self = document.currentScript || (function () {
        var s = document.getElementsByTagName('script');
        return s[s.length - 1];
      })();
      return new URL(rel, self.src).toString();
    } catch (e) {
      return rel;
    }
  }

  function loadJSONSync(url) {
    try {
      var xhr = new XMLHttpRequest();
      xhr.open('GET', url, false); // synchronous by design — see README note
      xhr.send(null);
      if (xhr.status >= 200 && xhr.status < 300) {
        return JSON.parse(xhr.responseText);
      }
    } catch (e) { /* API/DB not available yet — silently fall back below */ }
    return null;
  }

  function parseTags(tagStr) {
    var tags = String(tagStr || '').split(',').map(function (t) { return t.trim(); }).filter(Boolean);
    var out = {};
    if (tags.indexOf('reception') > -1) out.eventGroup = 'reception';
    if (tags.indexOf('marriage') > -1) out.eventGroup = 'marriage';
    if (tags.indexOf('real') > -1) out.flowerType = 'real';
    if (tags.indexOf('artificial') > -1) out.flowerType = 'artificial';
    return out;
  }

  function toLegacyItem(row) {
    var extra = parseTags(row.tags);
    return Object.assign({
      id: row.id,
      title: row.title,
      description: row.description || row.short_description || '',
      base_price: parseFloat(row.price) || 0,
      image: row.image || 'uploads/services/stage.webp',
      event_types: ['wedding'],
      slug: row.slug
    }, extra);
  }

  function toLegacyPackage(row, pkg, index) {
    var staticItems = STATIC_SERVICES_DATA[row.category_slug] || [];
    var fallback = staticItems[index] || {};
    var packagePrice = parseFloat(pkg.price);
    return Object.assign({}, fallback, {
      id: pkg.id || fallback.id || row.id,
      title: pkg.label || fallback.title || row.title,
      description: pkg.description || fallback.description || row.description || row.short_description || '',
      base_price: isNaN(packagePrice) ? (fallback.base_price || parseFloat(row.price) || 0) : packagePrice,
      image: pkg.image || row.image || fallback.image || 'uploads/services/stage.webp',
      event_types: fallback.event_types || ['wedding'],
      slug: pkg.slug || row.slug,
      pkgKey: pkg.pkg_key || fallback.pkgKey || ('p' + (index + 1))
    });
  }

  function useStaticFallback() {
    window.SERVICES_DATA        = STATIC_SERVICES_DATA;
    window.PHOTOGRAPHY_PACKAGE  = STATIC_PHOTOGRAPHY_PACKAGE;
    window.PHOTOGRAPHY_FILTERS  = STATIC_PHOTOGRAPHY_FILTERS;
    window.PHOTOGRAPHY_BASE_PRICE = STATIC_PHOTOGRAPHY_BASE_PRICE;
  }

  var servicesResp = loadJSONSync(resolveUrl(API_BASE + 'api/services'));

  if (servicesResp && Array.isArray(servicesResp.services) && servicesResp.services.length) {
    var grouped = {};
    var photoService = null;
    servicesResp.services.forEach(function (row) {
      if (row.category_slug === 'photography') { photoService = row; return; }
      var key = row.category_slug;
      if (!grouped[key]) grouped[key] = [];
      // DJ is stored as one parent service with seven packages. The listing
      // still needs one card per package, so expand that nested API shape
      // without changing or removing any service/package records.
      if (key === 'dj' && Array.isArray(row.packages) && row.packages.length) {
        row.packages.forEach(function (pkg, index) {
          grouped[key].push(toLegacyPackage(row, pkg, index));
        });
        return;
      }
      grouped[key].push(toLegacyItem(row));
    });
    window.SERVICES_DATA = grouped;

    if (photoService) {
      window.PHOTOGRAPHY_PACKAGE = {
        id: photoService.id,
        title: photoService.title,
        description: photoService.description,
        image: photoService.image
      };
      window.PHOTOGRAPHY_BASE_PRICE = parseFloat(photoService.price) || STATIC_PHOTOGRAPHY_BASE_PRICE;
      if (Array.isArray(photoService.packages) && photoService.packages.length) {
        window.PHOTOGRAPHY_FILTERS = photoService.packages.map(function (p) {
          return { key: p.pkg_key, label: p.label, addPrice: (parseFloat(p.price) || 0) - window.PHOTOGRAPHY_BASE_PRICE };
        });
      } else {
        window.PHOTOGRAPHY_FILTERS = STATIC_PHOTOGRAPHY_FILTERS;
      }
    } else {
      window.PHOTOGRAPHY_PACKAGE    = STATIC_PHOTOGRAPHY_PACKAGE;
      window.PHOTOGRAPHY_FILTERS    = STATIC_PHOTOGRAPHY_FILTERS;
      window.PHOTOGRAPHY_BASE_PRICE = STATIC_PHOTOGRAPHY_BASE_PRICE;
    }
  } else {
    // API unreachable or DB not seeded yet (e.g. setup.php not run) —
    // use the bundled static catalog so the site keeps working.
    useStaticFallback();
  }

  window.ALL_SERVICES = Object.values(window.SERVICES_DATA).flat();
})();
