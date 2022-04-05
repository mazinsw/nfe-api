#!/bin/sh

BASEDIR=$(dirname $0)

rm -f $BASEDIR/IBPT/*.*

svn checkout https://github.com/frones/ACBr/trunk/Exemplos/ACBrTCP/ACBrIBPTax/tabela "$BASEDIR/IBPT"

php -f "$BASEDIR/update_IBPT.php" "$BASEDIR/IBPT" "$BASEDIR/../src/NFe/Database/data/IBPT"
