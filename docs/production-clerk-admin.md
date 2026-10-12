# ELLCY production authentication and admin media

## Clerk

Create one Clerk application and configure the following deployment variables.
Never place their values in Git or browser JavaScript except the publishable key.

```text
ELLCY_AUTH_PROVIDER=clerk
CLERK_PUBLISHABLE_KEY=pk_...
CLERK_JWT_KEY=-----BEGIN PUBLIC KEY-----\n...\n-----END PUBLIC KEY-----
ELLCY_CLERK_ADMIN_USER_IDS=user_...
ELLCY_CLERK_ADMIN_NAME=ELLCY Admin
ELLCY_CLERK_AUTHORIZED_PARTIES=https://ellcy.in,https://www.ellcy.in,https://ellcy-vr2.vercel.app
```

Setting `ELLCY_AUTH_PROVIDER=clerk` makes incomplete Clerk configuration fail
closed; the application will never fall back to the legacy password form in
production while the provider is enabled.

`ELLCY_CLERK_ADMIN_USER_IDS` is a comma-separated allow-list. A valid Clerk
account that is not in this list receives HTTP 403 and cannot use the panel.
The backend verifies RS256, `exp`, `nbf`, `iat`, `iss`, `azp`, `sid`, `sub` and
rejects pending sessions before any admin controller action executes.

After the variables are saved, redeploy and open `/admin/login`. The legacy
password form remains available only when Clerk is not configured, which keeps
local XAMPP development usable during migration.

## Persistent photos and videos

Vercel serverless files are not durable. Configure Cloudinary before enabling
production uploads:

```text
CLOUDINARY_CLOUD_NAME=...
CLOUDINARY_API_KEY=...
CLOUDINARY_API_SECRET=...
```

Images, thumbnails and uploaded videos are then stored in Cloudinary while the
MySQL `service_images` table stores only their HTTPS URLs and storage keys.
Deleting or replacing a gallery item also deletes the matching cloud asset.
YouTube/Vimeo links and external HTTPS image URLs remain supported.

## Database migrations

Run these after the existing schema, or allow `ProductionBootstrap` to apply
them on the first production admin request:

1. `sql/production_update_v11_clerk_cloud_media.sql`
2. `sql/production_update_v12_admin_catalog.sql`

The second migration creates admin records for the photography standards, BMW
models and every bridal/groom makeover tier used by current public routes.
