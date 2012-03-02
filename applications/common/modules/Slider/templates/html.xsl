<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="slider">
		<div>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:for-each select="*">
				<div class="slide">
					<xsl:apply-templates select="."/>
				</div>
			</xsl:for-each>
		</div>
	</xsl:template>

</xsl:stylesheet>