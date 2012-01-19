<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="headline">

		<xsl:element name="h{@priority}">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:apply-templates/>
			<a href="#{@id}" class="pilcrow">&#182;</a>
		</xsl:element>

	</xsl:template>

</xsl:stylesheet>