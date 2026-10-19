package in.kpriet.portal;
import jakarta.servlet.*;import jakarta.servlet.annotation.WebFilter;import jakarta.servlet.http.*;
import java.io.IOException;
@WebFilter(urlPatterns={"/students","/file","/logout"})
public class AuthFilter implements Filter {
 @Override public void doFilter(ServletRequest request,ServletResponse response,FilterChain chain)throws IOException,ServletException {
  HttpServletRequest req=(HttpServletRequest)request;HttpServletResponse res=(HttpServletResponse)response;HttpSession session=req.getSession(false);
  if(session==null||session.getAttribute("username")==null){res.sendRedirect(req.getContextPath()+"/login");return;}chain.doFilter(request,response);
 }
}
