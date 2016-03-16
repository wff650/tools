#!/usr/bin/python
# -*- coding: utf-8 -*-

import MySQLdb

def getColumns(conn,table):
    
    if conn:
        cur = conn.cursor()
        cur.execute("SHOW COLUMNS FROM %s" % table)
        return (v[0] for v in cur.fetchall())
        
    return tuple()

def getIndexs(conn,table):
    
    if conn:
        cur = conn.cursor()
        cur.execute("SHOW KEYS FROM %s" % table)
        return (v[2] for v in cur.fetchall())
        
    return tuple()
    

conf = [{'host':"127.0.0.1",'user':'root','passwd':'123','db':'test','port':3306},{'host':'127.0.0.1','user':'root','passwd':'123','db':'test1','port':3306}]

try:
    dbConf = conf[0]
    dbSource = MySQLdb.connect(host=dbConf['host'],user=dbConf['user'],passwd=dbConf['passwd'],db=dbConf['db'])
    
    curSource = dbSource.cursor()
except MySQLdb.Error,e:
    print "Mysql Error %s %d: %s " % (dbConf['host'],e.args[0], e.args[1])
    
try:
    dbConf = conf[1]
    dbDestination = MySQLdb.connect(host=dbConf['host'],user=dbConf['user'],passwd=dbConf['passwd'],db=dbConf['db'])
    curDestination = dbDestination.cursor()
except MySQLdb.Error,e:
    print "Mysql Error %s %d: %s " % (dbConf['host'],e.args[0], e.args[1])
    
    
    
curSource.execute("SHOW TABLES");
dbTableSource = curSource.fetchall()

curDestination.execute("SHOW TABLES");
dbTableDestination = curDestination.fetchall()

diffTables = set(dbTableSource) ^ set(dbTableDestination)

logFile = open("develop_test.log","w")
logMsg = u'''
两个表的对照记录如下：


'''

diffTables = list(diffTables)
if len(diffTables):
    logMsg += u"\n对称差集:\n";
    logMsg += ",".join([ "%s" % v for v in diffTables])
    logMsg += "\n"

diffTables = list(set(dbTableSource) - set(dbTableDestination))
sql="\n"
if len(diffTables):
    logMsg += u"\n在%s:%s中，但不在%s:%s中的表:\n" % (conf[0]['host'],conf[0]['db'],conf[1]['host'],conf[1]['db'])
    logMsg += ",".join([ "%s" % v for v in diffTables])
    logMsg += "\n"
    for table in diffTables:
        curSource.execute("SHOW CREATE TABLE %s" % table)
        res = curSource.fetchone()
        if res:
            sql += res[1]
            sql += "\n\n\n"
    
diffTables = list(set(dbTableDestination) - set(dbTableSource))
if len(diffTables):
    logMsg += u"\n在%s:%s中，但不在%s:%s中的表:\n" % (conf[1]['host'],conf[1]['db'],conf[0]['host'],conf[0]['db'])
    logMsg += ",".join([ "%s" % v for v in diffTables])
    logMsg += "\n"
    

intersectTables = list(set(dbTableSource) & set(dbTableDestination))

if len(intersectTables):
    for table in intersectTables:
        sourceAttr = getColumns(dbSource,table)
        destinationAttr = getColumns(dbDestination,table)
        
        diffAttr = list(set(sourceAttr) ^ set(destinationAttr))
        
        if len(diffAttr):
            logMsg += u"\n表%s的不同字段:\n" % table
            logMsg += ",".join([ "%s" % v for v in diffAttr])
            logMsg += "\n"
            
        sourceIndex = getIndexs(dbSource,table)
        destinationIndex = getIndexs(dbDestination,table)
        
        diffIndex = list(set(sourceIndex) ^ set(destinationIndex))
        
        if len(diffIndex):
            logMsg += u"\n表%s的不同索引:\n" % table
            logMsg += ",".join([ "%s" % v for v in diffIndex])
            logMsg += "\n"
        
if len(sql):
    logMsg += sql
    logMsg += "\n"
logFile.write(logMsg.encode("utf-8"))
logFile.close();
