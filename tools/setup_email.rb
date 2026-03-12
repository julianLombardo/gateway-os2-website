#!/usr/bin/env ruby
# GatewayOS2 Website — Email Configuration
# Sets up SMTP credentials for sending verification codes and password resets.

require 'json'
require 'io/console'

PROJECT_ROOT = File.expand_path('..', __dir__)
CONFIG_FILE = File.join(PROJECT_ROOT, 'data', 'mail_config.json')

puts ""
puts "  \e[1m\e[36mGatewayOS2 Email Setup\e[0m"
puts "  \e[90m──────────────────────────────\e[0m"
puts ""
puts "  This configures SMTP for sending verification"
puts "  codes and password reset emails."
puts ""
puts "  For Gmail, you need an App Password:"
puts "  1. Go to myaccount.google.com/apppasswords"
puts "  2. Generate a password for 'Mail'"
puts "  3. Use that 16-character password below"
puts ""
puts "  \e[90m──────────────────────────────\e[0m"
puts ""

# Load existing config
config = if File.exist?(CONFIG_FILE)
  JSON.parse(File.read(CONFIG_FILE))
else
  {}
end

print "  SMTP Host [#{config['smtp_host'] || 'smtp.gmail.com'}]: "
host = gets.strip
config['smtp_host'] = host.empty? ? (config['smtp_host'] || 'smtp.gmail.com') : host

print "  SMTP Port [#{config['smtp_port'] || 587}]: "
port = gets.strip
config['smtp_port'] = port.empty? ? (config['smtp_port'] || 587) : port.to_i

print "  Email address: "
email = gets.strip
config['smtp_user'] = email unless email.empty?
config['from_email'] = email unless email.empty?

print "  App Password: "
pass = STDIN.noecho(&:gets).strip
puts ""
config['smtp_pass'] = pass unless pass.empty?

print "  From Name [#{config['from_name'] || 'GatewayOS2'}]: "
name = gets.strip
config['from_name'] = name.empty? ? (config['from_name'] || 'GatewayOS2') : name

File.write(CONFIG_FILE, JSON.pretty_generate(config))

puts ""
puts "  \e[32m✓\e[0m Configuration saved to data/mail_config.json"
puts ""

# Test connection
print "  Send a test email? [y/N]: "
if gets.strip.downcase == 'y'
  print "  Send test to: "
  test_to = gets.strip
  unless test_to.empty?
    php = `which php 2>/dev/null`.strip
    php = "#{ENV['HOME']}/local/bin/php" if php.empty?
    mailer_path = File.join(PROJECT_ROOT, 'lib', 'services', 'MailService.php')
    result = `#{php} -r "
      require '#{mailer_path}';
      \\$mail = new MailService();
      \\$r = \\$mail->send('#{test_to}', 'GatewayOS2 Test', '<h2>It works!</h2><p>Email is configured correctly.</p>');
      echo \\$r === true ? 'SUCCESS' : \\$r;
    " 2>&1`
    if result.include?('SUCCESS')
      puts "  \e[32m✓\e[0m Test email sent to #{test_to}"
    else
      puts "  \e[31m✗\e[0m Failed: #{result.strip}"
    end
  end
end

puts ""
