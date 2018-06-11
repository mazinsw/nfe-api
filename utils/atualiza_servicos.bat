@echo OFF

mkdir "../storage/generated/api/NFe/Database/data"
php -c "D:\Development\Interpreter\PHP-7.1\php.ini" -n atualiza_servicos.php "../storage/generated/api/NFe/Database/data"
