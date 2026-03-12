#!/usr/bin/env ruby
# GatewayOS2 — Migration: flat PHP → MVC architecture
# This script adds the 'role' field to users.json and ensures all data directories exist.

require 'json'
require 'fileutils'

PROJECT_ROOT = File.expand_path('..', __dir__)
DATA_DIR = File.join(PROJECT_ROOT, 'data')

puts "\e[1mGatewayOS2 — Migration\e[0m\n\n"

# 1. Add role field to users
users_file = File.join(DATA_DIR, 'users.json')
if File.exist?(users_file)
  users = JSON.parse(File.read(users_file))
  modified = false
  users.each do |u|
    unless u.key?('role')
      u['role'] = 'user'
      modified = true
    end
  end
  if modified
    File.write(users_file, JSON.pretty_generate(users))
    puts "  \e[32m✓\e[0m Added 'role' field to #{users.length} users"
  else
    puts "  \e[33m⊘\e[0m Users already have roles"
  end
end

# 2. Ensure directories
dirs = ['cache', 'messages', 'blog']
dirs.each do |dir|
  path = File.join(DATA_DIR, dir)
  FileUtils.mkdir_p(path)
  puts "  \e[32m✓\e[0m data/#{dir}/"
end

# 3. Initialize empty data files if missing
defaults = {
  'analytics.json' => '{"total_views":0,"daily":{}}',
  'tokens.json' => '[]',
  'reset_codes.json' => '[]',
  'cache/manifest.json' => '{}',
  'cache/github.json' => '{"data":null,"fetched_at":0}',
}

defaults.each do |file, content|
  path = File.join(DATA_DIR, file)
  unless File.exist?(path)
    File.write(path, content)
    puts "  \e[32m✓\e[0m data/#{file}"
  end
end

puts "\n\e[32mMigration complete!\e[0m"
