<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="div">
		<div>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:apply-templates/>
		</div>
	</xsl:template>

</xsl:stylesheet>