<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="code">
		<xsl:choose>
			<xsl:when test="contains('prettyprint', @class)">
				<code>
					<xsl:copy-of select="@id"/>
					<xsl:copy-of select="@class"/>
					<xsl:apply-templates/>
				</code>
			</xsl:when>
			<xsl:otherwise>
				<pre>
					<xsl:copy-of select="@id"/>
					<xsl:copy-of select="@class"/>
					<xsl:apply-templates/>
				</pre>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>