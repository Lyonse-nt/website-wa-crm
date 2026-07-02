#!/bin/bash
# Railway Environment Variables Setup Script
# Run this with: railway variables --set

echo "Setting Railway environment variables..."

railway variables set DB_CONNECTION=mysql
railway variables set 'DB_HOST=${{MySQL.MYSQLHOST}}'
railway variables set 'DB_PORT=${{MySQL.MYSQLPORT}}'
railway variables set 'DB_DATABASE=${{MySQL.MYSQLDATABASE}}'
railway variables set 'DB_USERNAME=${{MySQL.MYSQLUSER}}'
railway variables set 'DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}'
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set APP_URL=https://website-wa-crm-production.up.railway.app

echo "Done! Railway will automatically redeploy with new variables."
