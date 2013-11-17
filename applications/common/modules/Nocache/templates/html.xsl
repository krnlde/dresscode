<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="nocache">
		<xsl:comment>googleoff: index</xsl:comment>
		<xsl:apply-templates/>
		<xsl:comment>googleon: index</xsl:comment>
	</xsl:template>

</xsl:stylesheet>