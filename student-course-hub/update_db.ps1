$env:MYSQL_PWD = ""
mysql -u root student_course_hub -e "source database_update.sql"
