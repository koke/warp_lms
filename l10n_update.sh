#!/bin/sh

LANGUAGES="es_ES"
TEMPLATE="po/warp_lms.pot"

xgettext --keyword=__ --keyword=_e --default-domain=warp_lms --language=php *.php --output=$TEMPLATE

msgfmt --statistics $TEMPLATE
for lang in $LANGUAGES; do
    msgmerge -o po/.tmp$lang.po po/$lang.po $TEMPLATE
    mv po/.tmp$lang.po po/$lang.po
    msgfmt --statistics -o warp_lms-$lang.mo po/$lang.po
done