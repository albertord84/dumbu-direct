#!/bin/sh

grep insta_id $1 | grep -v resultset | sed s/\<field\ name=\"insta_id\"\>//g | sed s/\<\\/field\>//g | awk '{print $1}'

