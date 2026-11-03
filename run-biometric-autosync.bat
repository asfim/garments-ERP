@echo off
title TBG ERP - ZKTeco Biometric Real-time Auto-Sync Daemon
color 0b
echo ==============================================================
echo   TBG ERP - ZKTeco Biometric Real-time Background Auto-Sync
echo   Turkey Bangla Garments Ltd.
echo ==============================================================
echo.
echo Starting automated sync daemon...
echo Checking active ZKTeco biometric devices every 30 seconds.
echo Keep this window open/minimized for 24/7 real-time attendance sync.
echo.

php artisan biometric:sync --loop --interval=30

pause
