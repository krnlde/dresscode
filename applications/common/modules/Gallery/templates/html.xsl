<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="gallery">
		<!-- render image -->
		<ul class="thumbnails">
			<xsl:copy-of select="@id"/>
			<xsl:if test="@class">
				<xsl:attribute name="class">
					<xsl:text>thumbnails </xsl:text>
					<xsl:value-of select="@class"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates select="thumbnail" mode="inner"/>
		</ul>
	</xsl:template>

</xsl:stylesheet>