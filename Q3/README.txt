QUESTION 3 — JAVA SERVLET / JSP / JDBC STUDENT PORTAL
Required: JDK 17, Maven 3.8+, MySQL 8+, Apache Tomcat 10.1.
1. Import database.sql in phpMyAdmin or MySQL Workbench.
2. Configure database via environment variables STUDENT_DB_URL, STUDENT_DB_USER, STUDENT_DB_PASSWORD, or edit defaults in Db.java.
3. Build from this folder: mvn clean package
4. Copy target/student-portal.war into Tomcat 10.1 webapps/ and start Tomcat.
5. Create the demonstration login by running in a Java classpath containing target/classes, MySQL Connector/J and jbcrypt: java in.kpriet.portal.AdminSeed demo ChangeMe123! (or run AdminSeed from your IDE with Maven dependencies). Change the password after demo.
6. Insert student rows in the students table. For a profile image/document, set file_path to a filename relative to STUDENT_UPLOAD_DIR and file_type to image/jpeg, image/png or application/pdf. Default storage directory is a folder named student-portal-uploads under the OS user's home directory. Keep uploaded files outside the web root.
7. Open http://localhost:8080/student-portal/login
The dashboard includes client-side search and a live visible-student count. The file servlet resolves only files under the configured upload directory and streams only file types allowed by the database field. Ensure file_path contains only a relative filename, not an absolute path.
Limitations: this assignment portal focuses on login and dynamically displaying records; student CRUD/upload UI is not part of the stated Q3 requirements. Create a BCrypt hash for the demo account using AdminSeed, not a plaintext database password.
