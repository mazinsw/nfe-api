#!/bin/sh


FILE_LIST=$(find ./ -name "*.crt")
for file in $FILE_LIST
do
    name=$(echo $file | cut -d / -f 2)
    echo -n "$name -> " && keytool -printcert -file $file | grep until
done
