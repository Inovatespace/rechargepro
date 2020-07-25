<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
  <html>
  <body>
  <h2>PhProtector v0.3.1 </h2>
    <table border="0" >
      <tr bgcolor="#B4C4D1">
        <th>Date</th>
        <th>IP</th>
        <th>Hostname</th>
        <th>Browser</th>
        <th>HTTP Request</th>
        <th>Score</th>
        <th>Referer</th>
      </tr>
      <xsl:for-each select="logs/log">
      <tr bgcolor="#EAEEF3">
        <td><xsl:value-of select="date"/></td>
        <td><xsl:value-of select="ip"/></td>
        <td><xsl:value-of select="hostname"/></td>
        <td><xsl:value-of select="browser"/></td>
        <td><xsl:value-of select="request"/></td>
        <xsl:if test="score &gt; 0.5">
        <td bgcolor="#FF6600"><xsl:value-of select="score"/></td>
        </xsl:if>
        <xsl:if test="score &lt;= 0.5">
        <td ><xsl:value-of select="score"/></td>
        </xsl:if>
        <td><xsl:value-of select="referer"/></td>
      </tr>
      </xsl:for-each>
    </table>
  </body>
  </html>
</xsl:template>
</xsl:stylesheet>
