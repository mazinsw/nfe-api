@echo OFF
set PATH=%PATH%;%~pd0..\vendor\bin

cd ..
cmd /C phpcpd api
cmd /C phpcpd tests\NFe
cd %~pd0

pause
