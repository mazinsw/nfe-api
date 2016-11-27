@echo OFF

perl -0777 -i.original -pe "s/USE `NFeApi_classes`\$\$\n//igs" classes_base.sql
perl -0777 -i.original -pe "s/USE `NFeApi_classes`;\n//igs" classes_base.sql
perl -0777 -i.original -pe "s/END\$\$\n/END \$\$/igs" classes_base.sql
perl -0777 -i.original -pe "s/`NFeApi_classes`\.//igs" classes_base.sql
perl -0777 -i.original -pe "s/\nDELIMITER \$\$.*?DELIMITER ;\n\n//igs" classes_base.sql
perl -0777 -i.original -pe "s/' \/\* comment truncated \*\/ \/\*([^\*]+)\*\//$1'/igs" classes_base.sql
del /F/Q classes_base.sql.original
echo classes_base corrigido