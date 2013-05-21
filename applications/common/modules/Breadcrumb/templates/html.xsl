<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="breadcrumb">
		<nav>
			<ul class="breadcrumb">
				<xsl:apply-templates select="element" mode="breadcrumb"/>
			</ul>
		</nav>
	</xsl:template>

	<xsl:template match="element" mode="breadcrumb">
		<li>
			<xsl:choose>
				<xsl:when test="@active">
					<xsl:attribute name="class">active</xsl:attribute>
					<xsl:value-of select="."/>
				</xsl:when>
				<xsl:otherwise>
					<a href="{@path}"><xsl:value-of select="."/></a>
				</xsl:otherwise>
			</xsl:choose>
		</li>
	</xsl:template>

</xsl:stylesheet>