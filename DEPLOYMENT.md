# Deployment (CareJobber)

## Fix "jQuery is not defined" and 404s for JS/CSS on production

If the site works locally but on the server you see:

- **Uncaught ReferenceError: jQuery is not defined**
- **404** for `/js/jquery.min.js`, `/js/script.js`, `/admin_assets/...`, etc.

the most common cause is that the **web server document root is not set to the `public` directory**.

### 1. Set document root to `public/`

Laravel serves all static assets (JS, CSS, images) from the `public` folder. The site URL must point at `public`, not at the project root.

- **Apache**: Point `DocumentRoot` to `/var/www/html/carejobber/public` (or your actual path).
- **Nginx**: Set `root` to the full path to `public`, e.g. `root /var/www/html/carejobber/public;`
- **Shared hosting**: If you cannot change document root, move or symlink the contents of `public/` to the web root and ensure `index.php` and `.htaccess` are in place (see [Laravel deployment docs](https://laravel.com/docs/deployment)).

After this, requests like `https://yobist.com/js/jquery.min.js` should serve `public/js/jquery.min.js` and return 200, not 404.

### 2. Production `.env`

On the server, set:

```env
APP_URL=https://yobist.com
```

If you use a CDN or different domain for assets, you can set:

```env
ASSET_URL=https://yobist.com
```

Run after deploy:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. XrayWrapper error (Firefox)

The message *"Not allowed to define cross-origin object as property on [Object] or [Array] XrayWrapper"* is a Firefox security warning, often triggered by browser extensions or third-party scripts. It does not usually break the site. If it persists, try in a clean Firefox profile or another browser to confirm it is not from your app code.
