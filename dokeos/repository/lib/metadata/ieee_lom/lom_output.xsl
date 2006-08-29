<?xml version="1.0" encoding="ISO-8859-1"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
    <xsl:for-each select="/lom/general/title/string">
      <strong>
      	<xsl:value-of select="."/>
      	<xsl:if test="./@language">
	      	(<xsl:value-of select="./@language"/>)
      	</xsl:if>
      </strong><br />
    </xsl:for-each>
    <xsl:if test="/lom/technical/format">
     File Format: <xsl:value-of select="/lom/technical/format"/>
    </xsl:if>
</xsl:template>
</xsl:stylesheet>