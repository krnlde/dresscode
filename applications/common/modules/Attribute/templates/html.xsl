<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="attribute">
		<xsl:attribute name="{@name}">
			<xsl:value-of select="."/>
		</xsl:attribute>
	</xsl:template>

</xsl:stylesheet>