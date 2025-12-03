@echo off
setlocal enabledelayedexpansion
cls
title Generate BAT Runner Laravel + Vue

echo ================================
echo   Generate BAT Runner Script
echo ================================
echo cara pasang, salin lokasi contoh ( C:project/laravel/rs )
echo  pastikan setup database selesai 
echo.

:input_path
set /p projectPath=Masukkan lokasi folder project Laravel: 

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

:: Hapus file lama jika ada
del start.bat >nul 2>&1

:: Buat file langkah-per-langkah (zero block danger)
echo @echo off >> start.bat
echo title Menjalankan Laravel dan Vue >> start.bat
echo echo Menjalankan Laravel Server... >> start.bat
echo start cmd /k "cd /d "!projectPath!" ^&^& php artisan serve" >> start.bat
echo echo Menjalankan Vue (Vite)... >> start.bat
echo start cmd /k "cd /d "!projectPath!" ^&^& npm run dev" >> start.bat
echo pause >> start.bat

echo --------------------------------
echo BAT telah di-install!
echo File: start.bat
echo --------------------------------
echo.
pause
exit
