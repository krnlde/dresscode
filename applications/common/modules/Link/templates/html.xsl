<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="link">
		<a href="{@url}">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:attribute name="title">
				<xsl:choose>
					<xsl:when test="@description">
						<xsl:value-of select="@description"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@url"/>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:apply-templates/>
		</a>
	</xsl:template>

</xsl:stylesheet>