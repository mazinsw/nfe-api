#!/bin/sh


FILE_LIST=$(find ./ -name "*.crt")
cat cacert_mozilla.pem > cacert.pem
for file in $FILE_LIST
do
    name=$(echo $file | cut -d / -f 2)
    echo "" >> cacert.pem
    echo "$name" >> cacert.pem
    echo $name | sed -r 's/./=/g' >> cacert.pem
    cat $file >> cacert.pem
done
