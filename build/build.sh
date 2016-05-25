version="$(php build.php --ver)"
echo "Building version: ${version}"

php build.php
cd free
rm pwebcontact/media/js/jquery.pwebcontact.min.js
unzip -j -o "wp_pwebcontact_${version}_free.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js" -d "pwebcontact/media/js"
yui-compressor "pwebcontact/media/js/jquery.pwebcontact.min.js" -o "pwebcontact/media/js/jquery.pwebcontact.min.js"
zip -u "wp_pwebcontact_${version}_free.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js"
unzip -o -q "wp_pwebcontact_${version}_free.zip" -d "/home/piotr/www/wordpress-dev1/wp-content/plugins/"

cd ..

php build.php --pro
cd pro
rm pwebcontact/media/js/jquery.pwebcontact.min.js
unzip -j -o "wp_pwebcontact_${version}_pro.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js" -d "pwebcontact/media/js"
yui-compressor "pwebcontact/media/js/jquery.pwebcontact.min.js" -o "pwebcontact/media/js/jquery.pwebcontact.min.js"
zip -u "wp_pwebcontact_${version}_pro.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js"
unzip -o -q "wp_pwebcontact_${version}_pro.zip" -d "/home/piotr/www/wordpress-dev2/wp-content/plugins/"
