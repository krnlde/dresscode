<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="search">
		<div>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<h2>Search results:</h2>
			<xsl:apply-templates/>
		</div>
	</xsl:template>

</xsl:stylesheet>