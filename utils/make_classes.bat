@echo OFF

call fix_script
java -jar SQLtoClass.jar -p config.properties
if not %ERRORLEVEL% == 0 pause > NUL