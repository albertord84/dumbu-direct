#!/bin/sh

p=`pwd`
d=`dirname $0`
cdir=$p/$d

cd $cdir/../
rm -r vendor/mgp25/instagram-php/sessions/*

exit 0

