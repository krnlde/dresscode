<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="form">
		<form action="{@action}" method="{@method}">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:apply-templates/>
			<input type="submit"/>
		</form>
	</xsl:template>

</xsl:stylesheet>