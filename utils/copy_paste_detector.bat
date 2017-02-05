@echo OFF
set PATH=%PATH%;%~pd0..\vendor\bin

cd ..
cmd /C phpcpd api
cd %~pd0

pause
