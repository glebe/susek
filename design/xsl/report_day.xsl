<?xml version="1.0" encoding="ISO-8859-1"?>
<html xsl:version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
  
  <head>
  <title>Stats for a day</title>
  </head>
  <body style="font-family:Arial,helvetica,sans-serif;font-size:10pt;
        background-color:#EEEEEE">
      
    <div style="background-color:#666666;color:white;padding:4px">
       <div style="float:left;width:500px">URL</div>
       <div style="float:left;width:150px">total visits</div>
       <div style="float:left;width:150px">hosts by ip</div>
       <div style="float:left;width:150px">hosts by ff</div>
       <br clear="all"/>
    </div>
      
    <xsl:for-each select="stat/day">
      <div style="background-color:#dddddd;padding:4px">
        <div style="float:left;width:500px"><xsl:value-of select="url"/></div>
        <div style="float:left;width:150px"><xsl:value-of select="visits"/></div>
        <div style="float:left;width:150px"><xsl:value-of select="visits_ip"/></div>
        <div style="float:left;width:150px"><xsl:value-of select="visits_ff"/></div>
        <br clear="all" />
      </div>
    </xsl:for-each>
  </body>

</html>