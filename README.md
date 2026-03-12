# GatewayOS2 Website

Product website for [GatewayOS2](https://github.com/julianLombardo/GatewayOS2), built with PHP and Ruby.

## Requirements

- **PHP** (with CLI/CGI support)
- **Ruby** (2.7+, no gems required — uses stdlib only)

## Quick Start

```bash
# Start the development server
ruby server.rb

# Visit http://localhost:8080
```

## Build & Validate

```bash
# Validate PHP syntax and generate sitemap.xml
ruby build.rb
```

## Structure

```
gateway-os2-website/
├── server.rb              # Ruby WEBrick dev server (serves PHP via CGI)
├── build.rb               # Ruby build script (validation + sitemap)
├── public/
│   ├── index.php          # Home page
│   ├── features.php       # Features page
│   ├── apps.php           # Applications page
│   ├── about.php          # About page
│   ├── api/
│   │   └── stats.php      # JSON API endpoint
│   ├── css/
│   │   └── style.css      # Styles (NeXTSTEP-inspired dark theme)
│   ├── js/
│   │   └── main.js        # Client-side JS (animations, mobile nav)
│   └── includes/
│       ├── header.php     # Shared header/nav
│       └── footer.php     # Shared footer
└── README.md
```

## Deployment

For production, serve the `public/` directory with any PHP-capable web server (Apache, Nginx + PHP-FPM, etc.).
