# ELLCY — Complete PHP + MySQL Application
## One-Folder Deployment Guide for XAMPP

> **Changelog (production RewriteBase fix + CSP header)**
> - 🔴 **Found the "error page instead of home page after login" bug:**
>   `.htaccess` still had `RewriteBase /ellcy/` (the local-XAMPP-subfolder
>   setting) inside a zip named for production. Every PHP-handled route
>   works fine as long as it doesn't get internally rewritten (most pages
>   are served as real files and never touch this), but the post-login
>   redirect target is `/`, which Apache rewrites to `$1/index.html` using
>   `RewriteBase` to build the path — with the wrong base that resolved to
>   a URL that doesn't exist on the live server, so Apache's own error page
>   showed up instead of the homepage. Changed to `RewriteBase /` for the
>   production (root-install) deployment; see the updated comment in
>   `.htaccess` if this copy ever needs to run at `localhost/ellcy/` again.
> - 🟠 **Added a Content-Security-Policy header** (`app/helpers/Security.php`)
>   restricting scripts/styles/fonts to `'self'` plus the one CDN the site
>   actually uses (cdnjs), blocking `<object>`/`<embed>` entirely, and
>   locking `frame-ancestors`/`form-action`/`base-uri` to `'self'`. Written
>   to not break any of the site's existing inline `<script>` blocks.
> - The rest of this round's request list (fake-jewellery image-match
>   upload, Wedding → Food cleanup, Decoration 5-image upload, DJ
>   packages/pricing rewrite, Entertainment Activities three-service restoration,
>   admin single-thumbnail-in-description, Bouncer-UI-styled description
>   pages, and the `public/js` vs `js` duplicate-file cleanup) is a large,
>   multi-file, testable build — see the reply in chat for a status table
>   and what's needed to move forward on each one.

> **Changelog (Food Services rebuilt + single-media simplification + Enter Show
> Down cleanup + mobile header fix + full site audit)**
> - **Note on this upload:** the zip contained the original messy-named image
>   files (`Chenda Melam.jpeg`, `Car - Luxury.jpeg`, etc.) sitting back in
>   `uploads/services/`, overwriting last round's clean renamed versions —
>   that's very likely why images weren't showing. Built this round on top of
>   the last known-good copy instead of this upload so that work isn't lost.
>   Please deploy from the zip I hand back rather than re-zipping a folder
>   that's had the original photo zip extracted into it again.
> - **Food Services completely rebuilt** as a proper multi-level category
>   (the old 3 single "Breakfast/Lunch/Dinner Catering" pages are gone):
>   - Food → Breakfast / Lunch / Dinner (listing)
>   - Breakfast → 5/10/15/20 dish-count cards → description page
>   - Lunch → Veg / Non-Veg → 5/10/15/20 (veg) or 5/10/15 (non-veg) → description
>   - Dinner → Buffet-Style / Banana-Leaf-Style → Veg / Non-Veg →
>     5/10/20/25 dish-count → description
>   - Every description page uses the **Bouncer template** exactly as
>     requested: no package-pill selector, just a quantity stepper
>     ("Number of Plates", min 15 / default 15 / max 1000) × a per-plate rate.
>   - 39 pages generated in total; every one passed an HTML tag-balance check
>     and a JS syntax check, and all 793 internal links across them resolve
>     to real files.
> - **Media simplified to exactly one image or video per service** — the
>   public site (`js/media-gallery.js`) now shows only the admin's chosen
>   "Primary" item, not a multi-photo gallery. Added a star button in the
>   admin gallery grid to set which one is primary; a service's first
>   uploaded item becomes primary automatically so nothing shows blank.
> - **Enter Show Down:** removed the Morning/Evening/Both time-slot picker
>   entirely, replaced with a Bouncer-style quantity stepper (min 5 / default
>   5 / max 200) that multiplies against the price of whichever effect is
>   selected.
> - **Mobile header spacing fix:** on `category.html`, `services.html`,
>   `cart.html`, `booking.html`, `enquiry.html` and `success.html`, the
>   mobile header hides the logo and shows a Back button instead — correct
>   behaviour, but with nothing in the middle it looked like an emptied-out
>   header with the Back button and Cart stranded at opposite edges. Added a
>   centered page title (e.g. "Categories", "Your Cart") to fill that space;
>   Cart stays pinned top-right as already required.
> - **Full site audit, as requested:**
>   - Broken-link scan across all 198 HTML files / 3,234 internal links:
>     zero real broken links (5 regex false-positives from JS template
>     strings, not actual links).
>   - Viewport meta tag present on all 153 real pages.
>   - Every one of the 45 registered routes in `index.php` points to a
>     controller method that actually exists.


> - Unzipped and mapped all 45 photos from `INT - Images` to their matching
>   services (by filename/folder — e.g. `Car Entry/Car - Luxury.jpeg` →
>   Luxury Cars, `Dancers/Dancers - Male.jpeg` → Male Team, etc.), saved as
>   clean-named `.jpg` files under `uploads/services/` (mirrored to
>   `public/uploads/services/` too).
> - Replaced the generic placeholder image (mostly a single reused
>   `stage.png`) with the correct real photo in: category grid tiles (all 3
>   `data.js` copies), the home-page circles, every listing/card renderer in
>   `js/services.js` (dancers by team type, luxury vs normal cars, real vs
>   artificial flowers, gold/silver/kundan jewellery, catering boys vs
>   welcome girls, aarti/seer plates), Flower Rangoli, and all three new Food
>   Services pages.
> - Enter Show Down got the most complete treatment: each of its 7 effects
>   (Pyro Show, Entry Pot Fog, Paper Blast, Rose Blast, Balloon Blast, Stage
>   Fog Setup, Gun Paper Blast) now shows its own matching real photo instead
>   of one generic image repeated 7 times — there wasn't previously a
>   per-effect image field at all, so this is a small feature addition, not
>   just a swap.
> - Gaps that couldn't be filled (no matching photo was provided, left as
>   the existing placeholder): individual car brands/models (BMW, Audi,
>   Mercedes, Rolls-Royce, and specific models within each — only one
>   generic "Luxury" and one "Normal" car photo were provided), Bridal &
>   Groom Make Up, Music Performers sub-types beyond the 4 provided, and
>   anything under Birthday/College/Temple (only Wedding services were
>   covered by this photo set).


> - **Note on this upload:** the zip you sent this time was missing everything
>   from the previous round (the Categories admin page, Food Services pages,
>   etc.) — it looks like an older copy got re-uploaded. I built on top of my
>   last delivered version instead so nothing is lost; make sure this is the
>   zip you deploy from.
> - **Admin → Users was also a dead link**, same issue as Categories last
>   time — the sidebar link existed but nothing was behind it. Built
>   `User::getAll/countAll/getStats/setStatus()` plus a full list page with
>   search, status filter, and Activate/Deactivate/Ban actions.
> - **Phone login — the real blocker:** `app/helpers/Sms.php` doesn't send a
>   real text message yet (no SMS gateway is configured) — it only writes the
>   code to the PHP error log, which isn't visible to someone testing on
>   their own phone. **Added a dev-mode fallback:** while `APP_ENV` isn't
>   `'production'`, the 6-digit code is now shown directly on the login page
>   after "Send OTP" and auto-fills the verify field, so you can actually test
>   the whole flow without a real SMS account. To go live for real users,
>   sign up with an SMS gateway (MSG91, Twilio, Fast2SMS all work for Indian
>   numbers) and fill in the real API call in `Sms::sendOtp()` — the file has
>   a commented example showing the shape.
> - **Admin service media (image/video add, replace, delete) was already
>   fully built** from earlier work — verified it end-to-end and hardened it
>   with the same try/catch pattern as the phone-login fix, so if
>   `sql/media_gallery_migration.sql` hasn't been run yet, you get a clear
>   message pointing at that instead of a silent failure.
> - **Responsive-design audit, as requested before making changes:** checked
>   all 104 real service pages — every one has a viewport meta tag, and even
>   the ~80 pages still on the older single-price template (car-entry
>   variants, dancers, etc.) use the same responsive `sd-topbar`/`sd-dsk-hdr`
>   header structure as the newer multi-package pages. Responsiveness itself
>   is consistent site-wide; the DJ-style card-grid work that's still
>   outstanding is about page *content* (multiple priced packages), not
>   responsiveness.
> - Image attachment request from this session couldn't be actioned — no
>   image file came through with the message, only the zip and text. Please
>   re-attach and point at which service(s) it's for.


> - **Phone login "Network error" bug:** `sendPhoneOtp()`/`verifyPhoneOtp()` in
>   `AuthController.php` had zero error handling around their database calls.
>   If `sql/production_update_v7_phone_otp_login.sql` hasn't been run yet (the
>   `otp_logins`/`login_history` tables won't exist), PHP prints a raw error
>   into what the frontend expects to be JSON — `fetch().json()` then throws,
>   and the client shows a misleading "Network error" even though the request
>   reached the server fine. **Run that migration if you haven't.** Both
>   endpoints now also catch any unexpected error and return a clear JSON
>   message instead of leaking a raw PHP error.
> - **Admin → Categories was a dead link.** The sidebar had the link, but no
>   controller, model, or views existed behind it — clicking it 404'd. Built
>   `app/models/Category.php` plus list/create/edit/delete admin pages, fully
>   wired into `index.php`. Bonus fix found along the way: every admin
>   "Delete" button (including the pre-existing Services delete) has been
>   silently failing CSRF verification because the meta tag its JS reads the
>   token from didn't exist on the page — added it.
> - **Food Services rebuilt as Breakfast / Lunch / Dinner:**
>   - Breakfast — veg only, 5/10/15/20 dish-count tiers.
>   - Lunch — Veg (5/10/15/20) and Non-Veg (5/10/15) via the group-pill
>     selector already used elsewhere on the site.
>   - Dinner — four groups (Veg–Buffet, Veg–Banana Leaf, Non-Veg–Buffet,
>     Non-Veg–Banana Leaf), each with 5/10/15/20/25 dish-count tiers.
>   - The old flat "Food & Catering" single-price page is now a small hub
>     linking to the three new pages (old links still land somewhere useful
>     instead of a dead end).
>   - Along the way, fixed a real gap: the shared `.sd-group-pills` container
>     (used for any veg/non-veg-style toggle) never had the flex/gap layout
>     that `.sd-pkg-pills` has — added it in `css/service-desc.css`.
>   - Note: there's a separate, oddly-named `services/catering-boys/boys/`
>     and `services/catering-boys/girls/` structure already in the codebase
>     with its own breakfast/buffet/banana pages — that appears to be a
>     different feature (hiring catering **staff**, by gender) built earlier,
>     not touched here. Worth clarifying/renaming if it's actually meant to
>     be the same thing.
> - **DJ-style package-card design for all services — surveyed, not yet
>   done.** Of 104 real (non-redirect-stub) service pages, only ~22 already
>   use the modern multi-package card-grid template (the one in the DJ page
>   screenshot). The other ~80 — car-entry's 20 luxury/normal car models,
>   dancers' 19 team-size variants, snacks-stalls, flowers, jewellery, and a
>   handful of others — are still on the older one-page-per-variant pattern
>   (the same structure Flower Rangoli was in before it got merged into a
>   single page with a size selector). Converting all of them is a large,
>   multi-session job; flagging it here rather than guessing at scope.


> - 🔴 Removed `pages/admin.html` — a leftover, unauthenticated admin panel with a
>   hardcoded username/password visible in the page source. The real admin panel
>   lives at `/admin` and is fully server-authenticated (see below).
> - 🔴 The public site now actually reads services and prices from MySQL instead
>   of the static `js/data.js` file. New endpoints: `GET /api/services`,
>   `GET /api/services/:slug`, `GET /api/categories`. `js/data.js` (and its two
>   copies, `services/data.js` and `public/js/data.js`) now fetch from these
>   endpoints at page load and fall back to a bundled static catalog only if the
>   API/DB isn't reachable — so the site never breaks, but adding/editing a
>   service in `/admin` now shows up on the live site.
> - 🟠 Fixed a path-traversal weakness in the `/pages/*` route handler in
>   `index.php` (it now validates the resolved file stays inside `pages/`).
> - 🟠 Fixed a bug where uploaded service images were saved to `/uploads/services/`
>   but linked as `/public/uploads/services/`, breaking the image after upload.
> - 🟠 `setup.php` now refuses to run once an admin account already exists, so it
>   can't be used to silently reset your admin login if you forget to delete it.
> - New file: `sql/ellcy_seed_services.sql` — migrates the ~73 real services and
>   prices that used to live only in `js/data.js` into proper `services` /
>   `service_packages` / `service_categories` rows. The setup wizard imports it
>   automatically; see Step 4 below if you're importing manually via phpMyAdmin.
> - New file: `sql/00_create_database_and_user.sql` — creates a dedicated MySQL
>   user (`ellcy_user`) scoped only to `ellcy_db`, instead of connecting as
>   `root` with no password (XAMPP's default). `config/database.php` now uses
>   this account. **Run this script first**, before the schema/seed files.

> **Changelog (header fixes + booking login gate + OTP password reset)**
> - Fixed unreadable header text: the sign-in/account widget's text was
>   hardcoded dark grey/near-black (`css/cart.css`), invisible against the
>   purple header. Now white, matching the rest of the header.
> - Mobile header previously hid the account widget entirely behind the
>   hamburger drawer — only the hamburger + Cart were visible. Now a compact
>   avatar/sign-in icon stays visible next to Cart on mobile too (the full
>   menu is still one tap away via the hamburger).
> - **Real bug fix:** `pages/booking.html` fired the booking request but
>   never checked the response — it always showed "Booking Confirmed" and
>   cleared the cart even if the server rejected the booking (e.g. session
>   expired, so login is required). Now it actually reads the response: on
>   `requires_login` it shows a "sign in to book" modal and leaves the
>   cart/items untouched instead of faking success.
> - Added that same "sign in to book" modal (`EllcyAuth.showLoginRequiredModal`
>   in `js/auth.js`) to Buy Now too, replacing a silent redirect to `/login`.
> - New file: `sql/production_update_v6_otp_reset.sql` — Forgot Password now
>   emails a **6-digit code** (10-minute expiry, max 5 guesses) instead of a
>   clickable reset link, verified on-site on the same page where the new
>   password is set (`/forgot-password` → `/reset-password?email=...`).


> - Finished `production_update_v4_remove_services.sql`: "Invitation" and
>   "Aarthi Plate" were hidden in the database but were still showing as
>   clickable tiles on the public wedding category grid and in the
>   Request-a-Call dropdown (`js/data.js`, `public/js/data.js`,
>   `pages/request-for-call.html`, `app/views/pages/request-for-call.php`).
>   Removed from all four.
> - Renamed the **Decoration** category to **Event Location** everywhere it's
>   customer-facing (`js/data.js`, `services/data.js`, `public/js/data.js`,
>   `services/decoration/index.html`). The URL slug (`decoration`) is
>   unchanged on purpose, so no service/booking data had to move.
> - New file: `sql/production_update_v5_event_location.sql` — adds
>   `orders.event_venue_images` (JSON array of up to 4 photo paths). Customers
>   can now attach their **Mahal / venue name** (existing `event_venue` field)
>   plus **up to 4 event location photos** on the booking page; both show up
>   in the admin Bookings detail view. Uploaded photos are stored under
>   `uploads/venues/` with random filenames and real MIME-type validation
>   (same pattern as the existing stage/light-decoration enquiry uploads).
> - **Flower Rangoli** is now a single page (`services/flower-rangoli/index.html`)
>   with a size selector (3×3 / 4×4 / 5×5 / 6×6 ft) instead of 4 separate
>   pages. The old size URLs now redirect to the merged page
>   (`?pkg=p1..p4`) so old links/bookmarks still work.

---

## What's Inside This Folder

This is the **complete ELLCY project** — your entire website PLUS the PHP backend
in a single folder. No separate copying required.

```
ellcy/
├── index.php          ← PHP router (handles admin, booking, RFC)
├── index.html         ← Your homepage (served directly)
├── setup.php          ← First-run wizard (DELETE after use)
├── .htaccess          ← URL routing rules
│
├── css/               ← All your stylesheets
├── js/                ← All your JavaScript files
├── uploads/           ← All your service images
├── pages/             ← All your HTML pages
├── services/          ← All your service description pages
│
├── config/            ← PHP configuration
│   ├── app.php        ← App settings (auto-detects URL)
│   └── database.php   ← Database connection
│
├── app/               ← PHP application code
│   ├── controllers/   ← Request handlers
│   ├── models/        ← Database models
│   ├── views/         ← PHP templates (admin panel)
│   └── helpers/       ← Security & Router utilities
│
├── sql/
│   └── ellcy_schema.sql  ← Database tables + seed data
│
└── storage/logs/      ← PHP error logs
```

---

## XAMPP Setup (Step by Step)

### Step 1 — Start XAMPP
1. Open XAMPP Control Panel
2. Click **Start** next to **Apache**
3. Click **Start** next to **MySQL**
4. Both should turn green ✓

### Step 2 — Copy This Folder
Copy the entire `ellcy` folder into:
```
Windows: C:\xampp\htdocs\ellcy\
Mac:     /Applications/XAMPP/htdocs/ellcy/
Linux:   /opt/lampp/htdocs/ellcy/
```

### Step 3 — Create the Database
1. Open browser → go to: `http://localhost/phpmyadmin`
2. Click **New** in the left sidebar
3. Type database name: `ellcy_db`
4. Click **Create**

### Step 3b — Create the database + dedicated DB user
Open phpMyAdmin (`http://localhost/phpmyadmin`), log in as **root**, click the
**SQL** tab, and paste in the contents of `sql/00_create_database_and_user.sql`
(or run it via `mysql -u root -p < sql/00_create_database_and_user.sql` from a
terminal). This creates:

| | |
|---|---|
| Database | `ellcy_db` |
| DB user | `ellcy_user` |
| DB password | Set `ELLCY_DB_PASS` in the server environment |

These already match `config/database.php` — you don't need to edit anything
unless you want to change the password (if you do, change it in **both**
places: the SQL script and `config/database.php`).

### Step 4 — Run the Setup Wizard
Open your browser and go to:
```
http://localhost/ellcy/setup.php
```
Follow the 5 steps:
- Step 1: Welcome
- Step 2: Tests your database connection
- Step 3: Creates all tables + seed data automatically, **including the full
  service catalog** (`sql/ellcy_schema.sql` then `sql/ellcy_seed_services.sql`)
- Step 4: You set your admin email + password
- Step 5: Done!

**Importing manually via phpMyAdmin instead?** Run both files, in this order:
```
sql/ellcy_schema.sql
sql/ellcy_seed_services.sql
```
The second file adds the real services/prices that used to be hardcoded in
`js/data.js` — skipping it means the DB will have empty categories and the
admin panel will show no services to manage.

### Step 5 — Delete setup.php
After the wizard finishes, delete `setup.php` from your folder.
This prevents anyone from resetting your admin account.

### Step 6 — Open Your Website
| Page | URL |
|------|-----|
| Homepage | `http://localhost/ellcy/` |
| Services | `http://localhost/ellcy/services` |
| Cart | `http://localhost/ellcy/cart` |
| Booking | `http://localhost/ellcy/booking` |
| Request for Call | `http://localhost/ellcy/request-for-call` |
| **Admin Login** | `http://localhost/ellcy/admin` |

---

## Troubleshooting

### White page / 404 on all pages
Your `.htaccess` `RewriteBase` must match your folder name.

Open `.htaccess` and find this line:
```apache
RewriteBase /ellcy/
```
Change `/ellcy/` to match your folder name exactly.
If the folder is at `htdocs` root directly, use:
```apache
RewriteBase /
```

### Apache won't start (port conflict)
1. In XAMPP Control Panel → click **Config** next to Apache
2. Open `httpd.conf`
3. Find `Listen 80` → change to `Listen 8080`
4. Restart Apache
5. Use `http://localhost:8080/ellcy/` instead

### Enable mod_rewrite (if pages show raw PHP)
1. In XAMPP → Apache Config → `httpd.conf`
2. Find: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Remove the `#` at the start
4. Restart Apache

### Database connection error
- Make sure MySQL is running (green in XAMPP)
- Open `config/database.php`
- Check `DB_NAME` is `ellcy_db`
- XAMPP default: user=`root`, password=`` (empty)

### See actual error messages
Open `config/app.php` and change:
```php
define('APP_ENV', 'development');
```
Refresh the page — you'll see the real error.

---

## Admin Panel

Login at: `http://localhost/ellcy/admin`

| Feature | What it does |
|---------|-------------|
| Dashboard | Overview of bookings, requests, revenue |
| Services | Add / Edit / Delete services |
| Bookings | View and manage all booking submissions |
| Call Requests | View and manage all RFC form submissions |
| Settings | Update site name, contact details |

---

## How the frontend loads live data

`js/data.js` (and its two copies) uses a **synchronous** XHR request to
`/api/services` the moment it's loaded, so that every other script on the page
(`services.js`, `category.js`, `script.js`, `booking.js`, `service-desc.js`,
`service_details.js`) can keep reading `SERVICES_DATA`, `ALL_SERVICES`,
`PHOTOGRAPHY_PACKAGE`, etc. exactly as before, with no changes needed to those
files. The tradeoff is a small, one-time blocking delay while that request
completes (typically a few milliseconds on localhost) before the rest of the
page's scripts run. If you'd rather have a fully non-blocking, async version
later (e.g. once you're ready to also rewrite the consumer scripts to await a
promise), that's a reasonable follow-up — ask and it can be converted.

If the API call fails for any reason (MySQL not running, `setup.php` not run
yet, offline development), `data.js` automatically falls back to a bundled
static copy of the catalog so the site still renders normally.

## How It Works

- **Your existing website** (HTML, CSS, JS, images) is served **exactly as before**
- **PHP only handles** 4 routes: `/admin`, `/booking`, `/request-for-call`, `/search`
- **Booking forms** now save to MySQL instead of just localStorage
- **Request for Call** forms now save to MySQL
- **Admin panel** lets you view and manage all submissions
