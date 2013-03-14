 /*
creating the WSDL 
 java -cp ./axis.jar:./commons-logging-1.0.4.jar:./commons-discovery-0.2.jar:./saaj.jar:./wsdl4j-1.5.1.jar:./jaxrpc.jar  org.apache.axis.wsdl.WSDL2Java http://cipcnet/moodle/wspp/wsdl_pp.php

 
JavaDOC :

javadoc -private -d ~/public_html/moodlews/java/javadoc fr.insa_lyon.cipcnet.moodle.wspp.wsdl *.java 

 Compilation :  
 javac -cp ./axis.jar:./jaxrpc.jar:. Test1.java
 
 Execution : 
 java -cp ./axis.jar:./commons-logging-1.0.4.jar:./commons-discovery-0.2.jar:saaj.jar::wsdl4j-1.5.1.jar:./jaxrpc.jar:.  Test1

*/

// adjust the import to your Moodle wsdl created by WSDL2Java !!!
import localhost.c2i.V1_4.plate_forme.ws.wsdl.*;

import org.apache.axis.AxisFault;
// GRR WSDL2Java traduced xsd:integer to java.math.BigInteger class ...
import java.math.BigInteger;

public class Testc2iv14_bilan  {


	public static void main (String[] args) {
	
		C2IWSLocator service= new C2IWSLocator();
		try {
	
			C2IWSPortType port=service.getC2IWSPort();
			
			System.out.println ("login in");
			LoginReturn lr=port.login ("xxxx","secret");
			System.out.println ("LR.client:"+lr.getClient());
			System.out.println ("LR.key:"+lr.getSessionkey());
			
		
			System.out.println ("get_bilans_examen");
			BilanRecord[] rr=port.get_bilans_examen(lr.getClient(),
						lr.getSessionkey(),"65.20");
			
			System.out.println(rr.length+" résultats");
			
			for (int i=0; i< rr.length;i++)
				System.out.println (
					rr[i].getNumetudiant()+";"+rr[i].getScore()
					
				);	

			System.out.println ("logout and bye ...");
			System.out.println (port.logout(lr.getClient(),lr.getSessionkey()));
	
		} 
		catch (AxisFault af) {
			System.out.println ("axis fault "+af);
		}
	
		catch (Exception e) {
			System.out.println ("exception "+e);
		}
	
	}

}
