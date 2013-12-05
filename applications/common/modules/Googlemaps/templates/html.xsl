<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<!-- <xsl:import href="../../Tabs/templates/html.xsl"/> -->

	<xsl:template match="googlemaps">
		<div class="googlemaps">
			<xsl:copy-of select="@id" />
			<xsl:if test="@class">
				<xsl:attribute name="class">
					<xsl:text>googlemaps </xsl:text>
					<xsl:value-of select="@class"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:text> </xsl:text>
		</div>

	</xsl:template>

</xsl:stylesheet>