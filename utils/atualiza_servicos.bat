@echo OFF

mkdir -p "../storage/generated/api/NFe/Database/data"
php -f atualiza_servicos.php "../storage/generated/api/NFe/Database/data"
