cls
@echo off

set CURRENTPATH=%~dp0

echo Perfect Contact Form for WordPress
echo Author^: Piotr Mocko
echo.
echo Extractor v2.0.0
echo.
echo Extract ZIP archive with FREE version...
"C:\Program Files\7-Zip\7z.exe" x %CURRENTPATH%free\wp_pwebcontact_2.0.17_free.zip -y -oD:\www\wordpress-dev1\wp-content\plugins
echo.
echo Extract ZIP archive with PRO version...
"C:\Program Files\7-Zip\7z.exe" x %CURRENTPATH%pro\wp_pwebcontact_2.0.17_pro.zip -y -oD:\www\wordpress-dev2\wp-content\plugins
echo.
pause

