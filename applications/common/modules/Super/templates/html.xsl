<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="super">
		<sup>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:apply-templates/>
		</sup>
	</xsl:template>

</xsl:stylesheet>