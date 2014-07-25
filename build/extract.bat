cls
@echo off

set CURRENTPATH=%~dp0

echo Perfect Contact Form for WordPress
echo Author^: Piotr Mocko
echo.
echo Extractor v1.0.0
echo.
echo Extract ZIP archive with FREE version...
"C:\Program Files\7-Zip\7z.exe" x %CURRENTPATH%wp_pwebcontact_1.0.5_free.zip -y -oC:\www\wordpress-dev1\wp-content\plugins
echo.
echo Extract ZIP archive with PRO version...
"C:\Program Files\7-Zip\7z.exe" x %CURRENTPATH%wp_pwebcontact_1.0.5_pro.zip -y -oC:\www\wordpress-dev2\wp-content\plugins
echo.

