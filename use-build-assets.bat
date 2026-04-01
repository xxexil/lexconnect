@echo off
echo Ensuring built assets are used...
if exist public\hot (
    del public\hot
    echo Removed hot file - using built assets
) else (
    echo Hot file not found - already using built assets
)
echo Done!
pause