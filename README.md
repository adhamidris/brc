# BRC Developments WordPress Build

This repo contains the custom WordPress code for the BRC Developments site.

## Structure

- `brc-theme/` - custom classic/hybrid WordPress theme.
- `brc-core/` - companion plugin for real-estate content types, fields, and schema helpers.
- `inspirations/` - visual references supplied for design direction.

## Local Install

### Docker

This repo includes a local WordPress stack.

```bash
docker-compose up -d
```

If you use newer Docker Compose syntax, this is equivalent:

```bash
docker compose up -d
```

Then open:

- WordPress: <http://localhost:8080>
- phpMyAdmin: <http://localhost:8081>

On the first WordPress screen, create the admin user. After login:

1. Go to **Appearance > Themes** and activate **BRC Developments**.
2. Go to **Plugins** and activate **BRC Core**.
3. Go to **Settings > Permalinks** and save once to refresh routes.

The local Docker setup mounts:

- `brc-theme/` to `wp-content/themes/brc-theme/`
- `brc-core/` to `wp-content/plugins/brc-core/`

So local file edits update the WordPress install directly.

To stop the local site:

```bash
docker-compose down
```

To fully reset WordPress and the database:

```bash
docker-compose down -v
```

To inspect PHP errors:

```bash
docker-compose exec wordpress tail -f /var/www/html/wp-content/debug.log
```

To lint theme/plugin PHP through the WordPress container:

```bash
docker-compose exec wordpress bash -lc 'find wp-content/themes/brc-theme wp-content/plugins/brc-core -name "*.php" -print0 | xargs -0 -n1 php -l'
```

To create local sample pages, projects, locations, units, and blog posts:

```bash
docker-compose exec wordpress php /var/www/html/scripts/seed-local-content.php
```

### Ubuntu Laptop Setup

If you want to move this project to another machine and run it locally there, clone the repo and start the Docker stack.

Install prerequisites:

```bash
sudo apt update
sudo apt install -y git docker.io docker-compose-plugin
sudo usermod -aG docker $USER
```

Log out and back in once so Docker group membership takes effect.

Clone and start the project:

```bash
git clone https://github.com/adhamidris/brc.git
cd brc
docker compose up -d
```

Then open:

- WordPress: <http://localhost:8080>
- phpMyAdmin: <http://localhost:8081>

On the first WordPress screen:

1. Complete the installer.
2. Activate **BRC Developments** in **Appearance > Themes**.
3. Activate **BRC Core** in **Plugins**.
4. Go to **Settings > Permalinks** and save once.
5. Seed demo content if needed:

```bash
docker compose exec wordpress php /var/www/html/scripts/seed-local-content.php
```

If you need the Ubuntu machine to match the exact current local content state rather than a fresh seeded install, export and import the database separately. Git carries the custom code and setup files, not the WordPress database or uploaded media.

### Plugin Choices

Install these after the base theme/plugin are active:

1. One SEO plugin: Yoast SEO or Rank Math.
2. One multilingual plugin: WPML or Polylang Pro.
3. One form plugin: Fluent Forms Pro or Gravity Forms.

## Architecture Notes

The theme owns presentation: templates, CSS, JavaScript, `theme.json`, and editor styles.

The plugin owns business content: Projects, Locations, Units, taxonomies, listing meta fields, and schema helpers. Keeping this in a plugin prevents content from disappearing if the theme is replaced later.
