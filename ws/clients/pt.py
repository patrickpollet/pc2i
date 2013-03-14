
#!/usr/bin/env python
#-*- coding:iso-8859-1  -*-

import SOAPpy

def show_methods(proxy):
	""" doc string """
        keys=proxy.methods.keys()
	keys.sort()
        for key in keys:
            method = proxy.methods[key]
            print "Method Name:", key.ljust(15)
            print
            inps = method.inparams
            for parm in range(len(inps)):
                details = inps[parm]
                print "   In #%d: %s  (%s)\n" % (parm, details.name, details.type)
            print
            outps = method.outparams
            for parm in range(len(outps)):
                details = outps[parm]
                print "   Out #%d: %s  (%s)\n" % (parm, details.name, details.type)
            print




proxy = SOAPpy.WSDL.Proxy('http://localhost/c2i/V1.4/plate-forme/ws/wsdl.php',encoding='iso-8859-1')
#print proxy.methods
show_methods(proxy)
print len(proxy.methods)
a,b=proxy.login("xxxxx","secret")

