@echo OFF

rm -f utils\IBPT\*.*

svn checkout https://github.com/frones/ACBr/trunk/Exemplos/ACBrTCP/ACBrIBPTax/tabela utils\IBPT

php -f utils\update_IBPT.php "utils\IBPT" "src\NFe\Database\data\IBPT"
