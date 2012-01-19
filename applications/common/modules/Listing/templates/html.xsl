<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="listing">
		<xsl:variable name="type">
			<xsl:choose>
				<xsl:when test="@type='ordered'">
					<xsl:text>ol</xsl:text>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>ul</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:element name="{$type}">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:for-each select="*">
				<li>
					<xsl:apply-templates select="."/>
				</li>
			</xsl:for-each>
		</xsl:element>
	</xsl:template>

</xsl:stylesheet>