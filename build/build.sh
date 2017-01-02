version="$(php build.php --ver)"
echo "Building version: ${version}"

php build.php
cd free
rm -rf pwebcontact/media/js/jquery.pwebcontact.min.js
mkdir -p pwebcontact/media/js
unzip -j -o "wp_pwebcontact_${version}_free.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js" -d "pwebcontact/media/js"
yuicompressor "pwebcontact/media/js/jquery.pwebcontact.min.js" -o "pwebcontact/media/js/jquery.pwebcontact.min.js"
zip -u "wp_pwebcontact_${version}_free.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js"
rm pwebcontact/media/js/jquery.pwebcontact.min.js
rmdir -p pwebcontact/media/js
cd ..

php build.php --pro
cd pro
mkdir -p pwebcontact/media/js
unzip -j -o "wp_pwebcontact_${version}_pro.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js" -d "pwebcontact/media/js"
yuicompressor "pwebcontact/media/js/jquery.pwebcontact.min.js" -o "pwebcontact/media/js/jquery.pwebcontact.min.js"
zip -u "wp_pwebcontact_${version}_pro.zip" "pwebcontact/media/js/jquery.pwebcontact.min.js"
if [ -d "/Users/lanah/" ]; then
  unzip -o -q "wp_pwebcontact_${version}_pro.zip" -d "/Users/lanah/PhpstormProjects/wordpress/wp-content/plugins/"
  else
  unzip -o -q "wp_pwebcontact_${version}_pro.zip" -d "/Users/DMR/Sites/wordpress/wp-content/plugins/"
fi


rm pwebcontact/media/js/jquery.pwebcontact.min.js
rmdir -p pwebcontact/media/js

echo "DONE"