#!/usr/bin/env ruby
# GatewayOS2 Website — Build System
# Concatenates CSS/JS, validates PHP, generates sitemap and asset manifest.

require 'fileutils'
require 'json'
require 'digest'

PROJECT_ROOT = File.expand_path('..', __dir__)
DOC_ROOT = File.join(PROJECT_ROOT, 'public')
SRC_DIR = File.join(PROJECT_ROOT, 'src')
DATA_DIR = File.join(PROJECT_ROOT, 'data')
SITE_URL = ENV['SITE_URL'] || 'https://gatewayos2.com'

puts "\e[1mGatewayOS2 Website Build\e[0m"
puts "========================\n\n"

manifest = {}

# --- Concatenate CSS ---
puts "\e[36mBuilding CSS...\e[0m"

# Main CSS: all files except 30-33 (admin)
main_css_files = Dir.glob(File.join(SRC_DIR, 'css', '*.css'))
                    .reject { |f| File.basename(f) =~ /^3\d-/ }
                    .sort
admin_css_files = Dir.glob(File.join(SRC_DIR, 'css', '3*.css')).sort

def concat_files(files)
  files.map { |f|
    content = File.read(f)
    "/* === #{File.basename(f)} === */\n#{content}"
  }.join("\n\n")
end

FileUtils.mkdir_p(File.join(DOC_ROOT, 'css'))

main_css = concat_files(main_css_files)
File.write(File.join(DOC_ROOT, 'css', 'style.css'), main_css)
manifest['style.css'] = Digest::MD5.hexdigest(main_css)[0..7]
puts "  \e[32m✓\e[0m public/css/style.css (#{main_css_files.length} files, #{(main_css.length / 1024.0).round(1)}KB)"

admin_css = concat_files(admin_css_files)
File.write(File.join(DOC_ROOT, 'css', 'admin.css'), admin_css)
manifest['admin.css'] = Digest::MD5.hexdigest(admin_css)[0..7]
puts "  \e[32m✓\e[0m public/css/admin.css (#{admin_css_files.length} files, #{(admin_css.length / 1024.0).round(1)}KB)"

# --- Concatenate JS ---
puts "\n\e[36mBuilding JavaScript...\e[0m"

FileUtils.mkdir_p(File.join(DOC_ROOT, 'js'))

main_js_files = Dir.glob(File.join(SRC_DIR, 'js', '*.js'))
                   .reject { |f| File.basename(f) =~ /^2\d-/ }
                   .sort
admin_js_files = Dir.glob(File.join(SRC_DIR, 'js', '2*.js')).sort

main_js = concat_files(main_js_files)
File.write(File.join(DOC_ROOT, 'js', 'main.js'), main_js)
manifest['main.js'] = Digest::MD5.hexdigest(main_js)[0..7]
puts "  \e[32m✓\e[0m public/js/main.js (#{main_js_files.length} files, #{(main_js.length / 1024.0).round(1)}KB)"

admin_js = concat_files(admin_js_files)
File.write(File.join(DOC_ROOT, 'js', 'admin.js'), admin_js)
manifest['admin.js'] = Digest::MD5.hexdigest(admin_js)[0..7]
puts "  \e[32m✓\e[0m public/js/admin.js (#{admin_js_files.length} files, #{(admin_js.length / 1024.0).round(1)}KB)"

# --- Write manifest ---
FileUtils.mkdir_p(File.join(DATA_DIR, 'cache'))
File.write(File.join(DATA_DIR, 'cache', 'manifest.json'), JSON.pretty_generate(manifest))
puts "\n  \e[32m✓\e[0m Asset manifest written (#{manifest.length} entries)"

# --- Validate PHP ---
puts "\n\e[36mValidating PHP files...\e[0m"
php_files = Dir.glob(File.join(PROJECT_ROOT, '**', '*.php'))
               .reject { |f| f.include?('/vendor/') }
               .sort
errors = 0
php_available = system('which php > /dev/null 2>&1')

if php_available
  php_files.each do |file|
    rel = file.sub(PROJECT_ROOT + '/', '')
    result = `php -l "#{file}" 2>&1`
    if result.include?('No syntax errors')
      puts "  \e[32m✓\e[0m #{rel}"
    else
      puts "  \e[31m✗\e[0m #{rel}"
      puts "    #{result.strip}"
      errors += 1
    end
  end
else
  puts "  \e[33m⚠\e[0m PHP not installed — skipping syntax validation (#{php_files.length} files)"
end

# --- Generate sitemap ---
puts "\n\e[36mGenerating sitemap.xml...\e[0m"
today = Time.now.strftime('%Y-%m-%d')

routes = ['/', '/features', '/apps', '/guide', '/code', '/about', '/download', '/blog', '/contact', '/search', '/demo']

urls = routes.map do |path|
  priority = path == '/' ? '1.0' : '0.8'
  { loc: "#{SITE_URL}#{path}", lastmod: today, priority: priority }
end

# Add blog posts
posts_file = File.join(DATA_DIR, 'blog', 'posts.json')
if File.exist?(posts_file)
  posts = JSON.parse(File.read(posts_file)) rescue []
  posts.each do |post|
    urls << { loc: "#{SITE_URL}/blog/#{post['id']}", lastmod: post['date'] || today, priority: '0.6' }
  end
end

sitemap = <<~XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
#{urls.map { |u|
  "  <url>\n    <loc>#{u[:loc]}</loc>\n    <lastmod>#{u[:lastmod]}</lastmod>\n    <priority>#{u[:priority]}</priority>\n  </url>"
}.join("\n")}
</urlset>
XML

File.write(File.join(DOC_ROOT, 'sitemap.xml'), sitemap)
puts "  \e[32m✓\e[0m Written to public/sitemap.xml (#{urls.length} URLs)"

# --- Summary ---
puts "\n\e[1mSummary\e[0m"
puts "  CSS files:   #{main_css_files.length + admin_css_files.length}"
puts "  JS files:    #{main_js_files.length + admin_js_files.length}"
puts "  PHP files:   #{php_files.length}"
puts "  Sitemap:     #{urls.length} URLs"
if errors > 0
  puts "  Errors:      \e[31m#{errors}\e[0m"
  exit 1
else
  puts "  Errors:      \e[32m0\e[0m"
  puts "\n\e[32mBuild complete!\e[0m"
end
