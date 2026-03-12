#!/usr/bin/env ruby
# GatewayOS2 — Seed admin user and sample data

require 'json'
require 'securerandom'
require 'fileutils'

PROJECT_ROOT = File.expand_path('..', __dir__)
DATA_DIR = File.join(PROJECT_ROOT, 'data')

puts "\e[1mGatewayOS2 — Seed Data\e[0m\n\n"

# Create admin user
users_file = File.join(DATA_DIR, 'users.json')
users = File.exist?(users_file) ? JSON.parse(File.read(users_file)) : []

admin_exists = users.any? { |u| u['role'] == 'admin' }
unless admin_exists
  print "  Admin username [admin]: "
  username = gets.strip
  username = 'admin' if username.empty?

  print "  Admin email: "
  email = gets.strip

  print "  Admin password: "
  password = gets.strip

  # Hash password using PHP
  hash = `php -r "echo password_hash('#{password.gsub("'", "\\\\'")}', PASSWORD_BCRYPT, ['cost' => 12]);"`.strip

  admin = {
    'id' => SecureRandom.hex(16),
    'username' => username,
    'email' => email,
    'display_name' => username.capitalize,
    'password_hash' => hash,
    'created_at' => Time.now.strftime('%Y-%m-%dT%H:%M:%S%z'),
    'email_verified' => true,
    'role' => 'admin'
  }

  users << admin
  File.write(users_file, JSON.pretty_generate(users))
  puts "  \e[32m✓\e[0m Admin user '#{username}' created"
else
  puts "  \e[33m⊘\e[0m Admin user already exists"
end

# Ensure role field on existing users
modified = false
users.each do |u|
  unless u.key?('role')
    u['role'] = 'user'
    modified = true
  end
end
if modified
  File.write(users_file, JSON.pretty_generate(users))
  puts "  \e[32m✓\e[0m Added 'role' field to existing users"
end

# Initialize data directories
['cache', 'messages'].each do |dir|
  path = File.join(DATA_DIR, dir)
  FileUtils.mkdir_p(path)
end

puts "\n\e[32mSeed complete!\e[0m"
