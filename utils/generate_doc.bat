@echo OFF
set PATH=%PATH%;%~pd0..\vendor\bin

cd ..
cmd /C phpdoc --progressbar --sourcecode -d api\NFe -t docs\api
cd %~pd0
start %~pd0..\docs\api\index.html
