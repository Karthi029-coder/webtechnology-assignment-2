package in.kpriet.portal;
import jakarta.servlet.ServletException;import jakarta.servlet.annotation.WebServlet;import jakarta.servlet.http.*;
import java.io.*;import java.nio.file.*;import java.sql.*;
@WebServlet("/file")
public class FileServlet extends HttpServlet {
 @Override protected void doGet(HttpServletRequest req,HttpServletResponse res)throws ServletException,IOException {
  int id;try{id=Integer.parseInt(req.getParameter("id"));}catch(Exception e){res.sendError(400,"Invalid file request.");return;}
  String relative=null, type=null;
  try(Connection c=Db.get();PreparedStatement p=c.prepareStatement("SELECT file_path,file_type FROM students WHERE id=?")){p.setInt(1,id);try(ResultSet r=p.executeQuery()){if(r.next()){relative=r.getString(1);type=r.getString(2);}}}
  catch(SQLException e){throw new ServletException("Unable to locate file record.",e);}
  if(relative==null||relative.isBlank()){res.sendError(404,"No file is associated with this student.");return;}
  Path root=Paths.get(System.getenv().getOrDefault("STUDENT_UPLOAD_DIR",System.getProperty("user.home")+File.separator+"student-portal-uploads")).toAbsolutePath().normalize();
  Path file=root.resolve(relative).normalize();
  if(!file.startsWith(root)||!Files.isRegularFile(file)){res.sendError(404,"File not found.");return;}
  String detected=Files.probeContentType(file);
  if(!"image/jpeg".equals(type)&&!"image/png".equals(type)&&!"application/pdf".equals(type)){res.sendError(415,"Unsupported file type.");return;}
  res.setHeader("X-Content-Type-Options","nosniff");res.setContentType(type);res.setContentLengthLong(Files.size(file));
  if("application/pdf".equals(type))res.setHeader("Content-Disposition","inline; filename=\"student-document.pdf\"");
  try(InputStream in=Files.newInputStream(file);OutputStream out=res.getOutputStream()){in.transferTo(out);}
 }
}
