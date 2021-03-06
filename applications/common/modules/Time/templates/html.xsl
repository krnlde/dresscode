<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="time">
		<time>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:copy-of select="@datetime"/>
			<xsl:apply-templates/>
		</time>
	</xsl:template>

</xsl:stylesheet>