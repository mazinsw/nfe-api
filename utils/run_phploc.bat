@echo OFF
set PATH=%PATH%;%~pd0..\vendor\bin

cd ..
cmd /C phploc src
cd %~pd0

pause
