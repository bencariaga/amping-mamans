@echo off
echo ========================================
echo  Clearing Laravel Caches
echo ========================================
echo.

php artisan config:clear
echo [OK] Config cache cleared

php artisan cache:clear
echo [OK] Application cache cleared

php artisan view:clear
echo [OK] View cache cleared

php artisan route:clear
echo [OK] Route cache cleared

echo.
echo ========================================
echo  All caches cleared successfully!
echo ========================================
echo.
pause
