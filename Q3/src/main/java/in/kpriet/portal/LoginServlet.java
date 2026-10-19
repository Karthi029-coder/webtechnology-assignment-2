package in.kpriet.portal;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import java.io.IOException;
import java.sql.*;
import org.mindrot.jbcrypt.BCrypt;
@WebServlet("/login")
public class LoginServlet extends HttpServlet {
 @Override protected void doGet(HttpServletRequest req,HttpServletResponse res)throws ServletException,IOException { req.getRequestDispatcher("/WEB-INF/views/login.jsp").forward(req,res); }
 @Override protected void doPost(HttpServletRequest req,HttpServletResponse res)throws ServletException,IOException {
  req.setCharacterEncoding("UTF-8"); String username=req.getParameter("username"), password=req.getParameter("password");
  if(username==null||password==null||username.isBlank()||password.isBlank()){req.setAttribute("error","Enter both username and password.");req.getRequestDispatcher("/WEB-INF/views/login.jsp").forward(req,res);return;}
  try(Connection c=Db.get();PreparedStatement p=c.prepareStatement("SELECT password_hash FROM users WHERE username=?")){
   p.setString(1,username.trim());try(ResultSet r=p.executeQuery()){
    if(r.next()&&BCrypt.checkpw(password,r.getString(1))){HttpSession old=req.getSession(false);if(old!=null)old.invalidate();HttpSession session=req.getSession(true);session.setAttribute("username",username.trim());session.setMaxInactiveInterval(20*60);res.sendRedirect(req.getContextPath()+"/students");}
    else{req.setAttribute("error","Invalid username or password.");req.getRequestDispatcher("/WEB-INF/views/login.jsp").forward(req,res);}
   }
  }catch(SQLException e){throw new ServletException("Database access failed. Check database.sql and Db settings.",e);}
 }
}
