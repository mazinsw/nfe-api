@echo OFF
set PATH=%PATH%;%~pd0..\vendor\bin

cd ..
cmd /C phpmd api text cleancode,codesize,controversial,design,unusedcode > utils\tmp\analisys.txt
cd %~pd0

pause
