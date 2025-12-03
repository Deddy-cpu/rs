@echo off
setlocal enabledelayedexpansion
cls
title Generate BAT Runner Laravel + Vue

echo ================================
echo   Generate BAT Runner Script
echo ================================
echo Cara pasang: salin lokasi, contoh:
echo C:\project\laravel\rs
echo Pastikan setup database sudah selesai.
echo.

:input_path
set /p projectPath=Masukkan lokasi folder: 

if "!projectPath!"=="" (
    echo.
    echo [ERROR] Path kosong! Masukkan lagi.
    echo.
    goto input_path
)

set projectPath=!projectPath:"=!

if not exist "!projectPath!" (
    echo.
    echo [ERROR] Folder tidak ditemukan! Masukkan ulang.
    echo.
    goto input_path
)

echo.
echo --------------------------------
echo Folder ditemukan
echo --------------------------------
echo.

echo Menginstall BAT runner...
echo.

:: ==============================
:: Hapus file lama jika ada
:: ==============================
del start.bat >nul 2>&1
del check_version.bat >nul 2>&1

:: ==============================
:: BUAT FILE start.bat
:: ==============================

echo @echo off >> start.bat
echo title Menjalankan Laravel dan Vue >> start.bat
echo echo Menjalankan Laravel Server... >> start.bat
echo start cmd /k "cd /d "!projectPath!" ^&^& php artisan serve" >> start.bat
echo echo Menjalankan Vue (Vite)... >> start.bat
echo start cmd /k "cd /d "!projectPath!" ^&^& npm run dev" >> start.bat
echo pause >> start.bat

:: ==============================
:: BUAT FILE check_version.bat
:: ==============================

del check_version.bat >nul 2>&1

echo @echo off >> check_version.bat
echo title Cek Versi PHP - Composer - Node.js - NPM >> check_version.bat
echo color 0a >> check_version.bat

echo echo ========================================== >> check_version.bat
echo echo        CEK VERSI LINGKUNGAN DEV            >> check_version.bat
echo echo ========================================== >> check_version.bat
echo echo. >> check_version.bat

:: PHP
echo echo [PHP VERSION] >> check_version.bat
echo call php -v 2^>nul ^|^| echo PHP tidak ditemukan di PATH! >> check_version.bat
echo echo. >> check_version.bat

:: Composer
echo echo [COMPOSER VERSION] >> check_version.bat
echo call composer -V 2^>nul ^|^| echo Composer tidak ditemukan di PATH! >> check_version.bat
echo echo. >> check_version.bat

:: Node
echo echo [NODE VERSION] >> check_version.bat
echo call node -v 2^>nul ^|^| echo Node.js tidak ditemukan di PATH! >> check_version.bat
echo echo. >> check_version.bat

:: NPM
echo echo [NPM VERSION] >> check_version.bat
echo call npm -v 2^>nul ^|^| echo NPM tidak ditemukan di PATH! >> check_version.bat
echo echo. >> check_version.bat

echo echo ========================================== >> check_version.bat
echo echo                CEK SELESAI                 >> check_version.bat
echo echo ========================================== >> check_version.bat
echo echo. >> check_version.bat
echo pause >> check_version.bat


echo --------------------------------
echo check_version.bat telah dibuat!
echo --------------------------------
echo.
pause
exit
