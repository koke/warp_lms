#!/bin/sh

if [ "x$1" == "x" ]; then
    echo "Usage: $0 version"
    exit 1
fi

VERSION=$1

perl -pe "s/Version: .*/Version: $VERSION/" -i warp_lms.php
perl -pe "s/^\\\$warp_lms_version = .*/\\\$warp_lms_version = '$VERSION';/" -i warp_lms.php

git add warp_lms.php
echo "Diff to commit"
git diff --cached
echo "Press return to accept or Ctrl-C to cancel"
read foo
git commit -m "Version bump to $VERSION"
git tag -s v$VERSION -m "Version $VERSION"
git push --mirror
git archive --format=tar --prefix=warp_lms/ v$VERSION | gzip -c9 > warp_lms-$VERSION.tar.gz
tar tzvf warp_lms-$VERSION.tar.gz
scp warp_lms-$VERSION.tar.gz moe.warp.es:public_html/wp-plugins/warp_lms/