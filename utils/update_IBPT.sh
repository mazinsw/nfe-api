#!/bin/sh

BASEDIR=`pwd`/$(dirname $0)

if [ -d "$BASEDIR/ACBr" ]; then
    cwd=`pwd`
    cd "$BASEDIR/ACBr" && git pull
    cd $cwd
else
    git clone https://github.com/frones/ACBr "$BASEDIR/ACBr"
    ln -sf "$BASEDIR/ACBr/Exemplos/ACBrTCP/ACBrIBPTax/tabela" "$BASEDIR/IBPT"
fi

php -f "$BASEDIR/update_IBPT.php" "$BASEDIR/IBPT" "$BASEDIR/../src/NFe/Database/data/IBPT"
