@echo OFF
set PATH=%PATH%;%~pd0..\vendor\bin

cd ..
cmd /C phpcbf --no-patch --standard=psr2 api\NFe\ tests\
cd %~pd0

pause