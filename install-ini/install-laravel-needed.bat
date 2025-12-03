@echo off
title Installer Laravel Dependencies
color 0a

echo ==========================================
echo     INSTALLER PHP - COMPOSER - NODE.JS
echo ==========================================
echo.

:: CEK ADMIN
echo Mengecek hak administrator...
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Silakan jalankan file ini sebagai Administrator!
    pause
    exit /b
)

:: BUAT FOLDER TEMP
set TEMP_DIR=%cd%\installer_temp
if not exist %TEMP_DIR% mkdir %TEMP_DIR%

echo ------------------------------------------
echo 1. Mengunduh PHP (Portable)
echo ------------------------------------------
curl -L -o %TEMP_DIR%\php.zip https://windows.php.net/downloads/releases/php-8.2.12-nts-Win32-vs16-x64.zip
echo Ekstrak PHP...
powershell -command "Expand-Archive '%TEMP_DIR%\php.zip' '%cd%\php'"

echo Menambahkan PHP ke PATH...
setx PATH "%cd%\php;%PATH%"

echo Selesai meng-install PHP.
echo.

echo ------------------------------------------
echo 2. Mengunduh Composer
echo ------------------------------------------
curl -L -o %TEMP_DIR%\composer-setup.exe https://getcomposer.org/Composer-Setup.exe
start "" %TEMP_DIR%\composer-setup.exe

echo Jalankan installer Composer dan klik Next sampai selesai.
echo.

echo ------------------------------------------
echo 3. Mengunduh Node.js LTS
echo ------------------------------------------
curl -L -o %TEMP_DIR%\nodejs.msi https://nodejs.org/dist/latest-lts/win-x64/nodejs.msi
start "" %TEMP_DIR%\nodejs.msi

echo Setelah installer Node terbuka, lanjutkan proses instalasinya.
echo.

echo ------------------------------------------
echo 4. Menampilkan versi setelah instalasi
echo ------------------------------------------
echo Pastikan setelah instalasi selesai kamu buka CMD baru lalu cek:
echo.
echo php -v
echo composer -v
echo node -v
echo npm -v
echo.

echo Instalasi selesai. Tekan apa saja untuk keluar.
pause
exit
