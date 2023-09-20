# power-inventory
system for inventory

###### PM2 with schedule_runner.js
pm2 start path/to/schedule_runner.js --name="APP NAME" --watch -- --script "path/to/php path/to/phpfile.php"  --cron "* * * * *"