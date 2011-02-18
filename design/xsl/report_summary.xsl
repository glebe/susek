<?xml version="1.0" encoding="ISO-8859-1"?>
<html xsl:version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
  
  <head>
  <title>Daily stats</title>
  </head>
  <body style="font-family:Arial,helvetica,sans-serif;font-size:10pt;
        background-color:#EEEEEE">
      
    <div style="background-color:#666666;color:white;padding:4px">
       <div style="float:left;width:100px">Date</div>
       <div style="float:left;width:100px">visits</div>
       <div style="float:left;width:100px">blog</div>
       <div style="float:left;width:100px">guestblog</div>
       <div style="float:left;width:100px">img</div>
       <div style="float:left;width:100px">dev</div>
       <br clear="all"/>
    </div>
      
    <xsl:variable name="daylink">day.php?day=</xsl:variable>
  	
	<xsl:for-each select="stat/day">

    
     <div style="background-color:#dddddd;padding:4px">
        <div style="float:left;width:100px;color:red"><a href="{$daylink}{date}"><xsl:value-of select="date"/></a></div>
        <div style="float:left;width:100px;color:red"><xsl:value-of select="@href"/></div>
        <div style="float:left;width:100px"><xsl:value-of select="summary"/></div>
        <div style="float:left;width:100px"><xsl:value-of select="blog"/></div>
        <div style="float:left;width:100px"><xsl:value-of select="guestblog"/></div>
        <div style="float:left;width:100px"><xsl:value-of select="img"/></div>
        <div style="float:left;width:100px"><xsl:value-of select="dev"/></div>
        <br clear="all" />
      </div>
    </xsl:for-each>
  </body>

</html>