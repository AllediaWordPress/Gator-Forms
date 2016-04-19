php build.php
cd free
unzip -j -o "wp_pwebcontact_2.1.7_free.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js" -d "pwebcontact/media/js"
yui-compressor "pwebcontact/media/js/jquery.pwebcontact.min.js" -o "pwebcontact/media/js/jquery.pwebcontact.min.js"
zip -u "wp_pwebcontact_2.1.7_free.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js"

cd ..

php build.php --pro
cd pro
unzip -j -o "wp_pwebcontact_2.1.7_pro.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js" -d "pwebcontact/media/js"
yui-compressor "pwebcontact/media/js/jquery.pwebcontact.min.js" -o "pwebcontact/media/js/jquery.pwebcontact.min.js"
zip -u "wp_pwebcontact_2.1.7_pro.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js"
