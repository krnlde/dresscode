<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="link">
		<xsl:if test="@cipher">
			<xsl:comment>googleoff: index</xsl:comment>
		</xsl:if>
		<a href="{@url}">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:if test="@cipher">
				<xsl:attribute name="data-cipher">
					<xsl:value-of select="@cipher"/>
				</xsl:attribute>
			</xsl:if>
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
		<xsl:if test="@cipher">
			<xsl:comment>googleon: index</xsl:comment>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>