#!/bin/sh

BASEDIR=`pwd`/$(dirname $0)

cd $BASEDIR
curl -o "ACBr.zip" https://codeload.github.com/ProjetoACBr/ACBr/zip/refs/heads/master
rm -rf "IBPT"
unzip "ACBr.zip" "ACBr-master/Exemplos/ACBrTCP/ACBrIBPTax/tabela/*"
mv "ACBr-master/Exemplos/ACBrTCP/ACBrIBPTax/tabela" "IBPT"
rm -rf "ACBr-master"
php -f "update_IBPT.php" "IBPT" "../src/NFe/Database/data/IBPT"
