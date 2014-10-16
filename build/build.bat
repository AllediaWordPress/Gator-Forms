cls
@echo off

set CURRENTPATH=%~dp0

echo Perfect Contact Form for WordPress
echo Author^: Piotr Mocko
echo.
echo Complier v2.0.0
echo.
echo Build ZIP archive with FREE version...
php "%CURRENTPATH%build.php"
echo.
echo Build ZIP archive with PRO version...
php "%CURRENTPATH%build.php" --pro
echo.
call "%CURRENTPATH%extract.bat
