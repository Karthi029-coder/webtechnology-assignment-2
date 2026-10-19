package in.kpriet.portal;
import jakarta.servlet.ServletException;import jakarta.servlet.annotation.WebServlet;import jakarta.servlet.http.*;
import java.io.IOException;import java.sql.*;import java.util.*;
@WebServlet("/students")
public class StudentServlet extends HttpServlet {
 @Override protected void doGet(HttpServletRequest req,HttpServletResponse res)throws ServletException,IOException {
  List<Map<String,Object>> students=new ArrayList<>();
  try(Connection c=Db.get();PreparedStatement p=c.prepareStatement("SELECT id,name,roll_number,email,course,file_path,file_type FROM students ORDER BY name");ResultSet r=p.executeQuery()){
   while(r.next()){Map<String,Object> s=new HashMap<>();s.put("id",r.getInt("id"));s.put("name",r.getString("name"));s.put("rollNumber",r.getString("roll_number"));s.put("email",r.getString("email"));s.put("course",r.getString("course"));s.put("filePath",r.getString("file_path"));s.put("fileType",r.getString("file_type"));students.add(s);}
  }catch(SQLException e){throw new ServletException("Unable to load student records.",e);}
  req.setAttribute("students",students);req.getRequestDispatcher("/WEB-INF/views/students.jsp").forward(req,res);
 }
}
