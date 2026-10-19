
package in.kpriet.portal;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class Db {
    private static final String URL =
        "jdbc:mysql://localhost:3306/java_student_portal?useUnicode=true&characterEncoding=UTF-8&serverTimezone=Asia/Kolkata";
    private static final String USER = "root";
    private static final String PASSWORD = "";

    public static Connection get() throws SQLException {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            throw new SQLException(
                "MySQL JDBC driver is missing from the application.", e);
        }

        return DriverManager.getConnection(URL, USER, PASSWORD);
    }
}
    