package in.kpriet.portal;
import java.sql.*;import org.mindrot.jbcrypt.BCrypt;
/** Run once from your IDE or Maven classpath to create a demonstration login. */
public class AdminSeed {
 public static void main(String[] args)throws Exception{
  String username=args.length>0?args[0]:"demo";
  String password=args.length>1?args[1]:"ChangeMe123!";
  try(Connection c=Db.get();PreparedStatement p=c.prepareStatement("INSERT INTO users(username,password_hash) VALUES(?,?) ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash)")){
   p.setString(1,username);p.setString(2,BCrypt.hashpw(password,BCrypt.gensalt(12)));p.executeUpdate();
  }
  System.out.println("Demo user ready: "+username+" (change the demonstration password before use).");
 }
}
