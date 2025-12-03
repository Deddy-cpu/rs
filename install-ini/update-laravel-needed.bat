@echo off
title Auto Update PHP, Composer, Node.js
color 0b

echo ==========================================
echo           AUTO UPDATE SYSTEM
echo     PHP (Portable) - Composer - Node.js
echo ==========================================
echo.

:: Folder PHP kamu
set PHP_DIR=%cd%\php

:: Folder temp downloader
set TEMP_DIR=%cd%\update_temp
if not exist %TEMP_DIR% mkdir %TEMP_DIR%

:: CEK ADMIN RIGHTS
echo Mengecek hak administrator...
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Jalankan file ini sebagai Administrator!
    pause
    exit /b
)
echo Hak administrator OK.
echo.

:: ===============================
:: 1. UPDATE PHP PORTABLE
:: ===============================
echo ------------------------------------------
echo 1. Mengecek update PHP...
echo ------------------------------------------

set PHP_URL=https://windows.php.net/downloads/releases/latest/php-8.2-nts-Win32-vs16-x64-latest.zip
echo Mengunduh versi terbaru PHP...
curl -L -o %TEMP_DIR%\php_latest.zip %PHP_URL%

echo Membersihkan PHP lama...
rmdir /s /q %PHP_DIR%

echo Meng-ekstrak PHP terbaru...
powershell -command "Expand-Archive '%TEMP_DIR%\php_latest.zip' '%PHP_DIR%'"

echo PHP berhasil diperbarui.
echo.

:: ===============================
:: 2. UPDATE COMPOSER
:: ===============================
echo ------------------------------------------
echo 2. Mengecek update Composer...
echo ------------------------------------------

echo Menjalankan self-update...
composer self-update

echo Composer berhasil diperbarui.
echo.

:: ===============================
:: 3. UPDATE NODE.JS
:: ===============================
echo ------------------------------------------
echo 3. Mengecek update Node.js...
echo ------------------------------------------
echo Mengunduh installer Node.js terbaru (LTS)...

curl -L -o %TEMP_DIR%\node_latest.msi https://nodejs.org/dist/latest-lts/win-x64/nodejs.msi

echo Menjalankan installer Node.js...
start "" %TEMP_DIR%\node_latest.msi

echo Setelah installer terbuka, klik Next sampai selesai.
echo.

:: ===============================
:: 4. CEK VERSI
:: ===============================
echo ------------------------------------------
echo CEK HASIL UPDATE
echo ------------------------------------------

echo PHP version:
php -v
echo.

echo Composer version:
composer -V
echo.

echo Node version:
node -v
echo.

echo Update selesai.
pause
exit
