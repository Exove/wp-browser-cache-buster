## General information

- Version: 0.3.1
- Description: Filename-based cache busting for WordPress scripts/styles. Based on a <a href="https://gist.github.com/ocean90/1966227#gistcomment-2030017">modified version of the original plugin</a>.
- Authors: <a href="https://dominikschilling.de/880/">Dominik Schilling</a>, <a href="https://github.com/KimchaC">KimchaC</a>, and <a href="https://www.exove.com">Exove</a>
- License: <a href="http://www.gnu.org/licenses/gpl-2.0.html">GPLv2 or later</a>

## Usage

Apache — add to your `.htaccess`:

```
 <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
 
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.+)\.(?:\d+)\.(min.js|min.css|js|css)($|\?.*$) $1.$3 [L]
  </IfModule>
```

Nginx — add to the `server` block:
 
```
location ~* ^(.+)\.(?:\d+)\.(min.js|min.css|js|css)($|\?.*$) {
  try_files $uri $1.$2;
}
```

## Changes

Doesn't touch scripts and styles from `/wp-includes` (since this plugin doesn't properly work with them).
