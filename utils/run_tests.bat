@echo OFF
set PATH=%PATH%;%~pd0..\vendor\bin

cd ..
cmd /C phpunit --no-coverage tests\NFe\
cd %~pd0

pause
