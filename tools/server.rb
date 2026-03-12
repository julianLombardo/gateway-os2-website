#!/usr/bin/env ruby
# GatewayOS2 Website — Development Server

require 'webrick'

PORT = (ENV['PORT'] || 8080).to_i
PROJECT_ROOT = File.expand_path('..', __dir__)
DOC_ROOT = File.join(PROJECT_ROOT, 'public')

# Build first
puts "\e[36mRunning build...\e[0m\n"
system("ruby #{File.join(PROJECT_ROOT, 'tools', 'build.rb')}")
puts ""

# Check PHP — prefer local binary in bin/, then system
php_path = `which php-cgi 2>/dev/null`.strip
if php_path.empty?
  local_php = File.join(PROJECT_ROOT, 'bin', 'php')
  if File.executable?(local_php)
    php_path = local_php
  else
    php_path = `which php 2>/dev/null`.strip
  end
  if php_path.empty?
    puts "\e[31mError: PHP not found.\e[0m"
    exit 1
  end
  puts ""
  puts "  \e[1m\e[36mGatewayOS2 Website\e[0m"
  puts "  \e[90m──────────────────────────────\e[0m"
  puts "  Local:   \e[32mhttp://localhost:#{PORT}\e[0m"
  puts "  Mode:    \e[33mPHP built-in server\e[0m"
  puts "  Root:    \e[33m#{DOC_ROOT}\e[0m"
  puts "  \e[90m──────────────────────────────\e[0m"
  puts ""
  router = File.join(DOC_ROOT, 'router.php')
  exec(php_path, "-S", "localhost:#{PORT}", "-t", DOC_ROOT, router)
end

puts ""
puts "  \e[1m\e[36mGatewayOS2 Website\e[0m"
puts "  \e[90m──────────────────────────────\e[0m"
puts "  Local:   \e[32mhttp://localhost:#{PORT}\e[0m"
puts "  PHP-CGI: \e[33m#{php_path}\e[0m"
puts "  Root:    \e[33m#{DOC_ROOT}\e[0m"
puts "  \e[90m──────────────────────────────\e[0m"
puts ""

# Use PHP built-in server with router for simplicity
router = File.join(DOC_ROOT, 'router.php')
exec("php", "-S", "localhost:#{PORT}", "-t", DOC_ROOT, router)
