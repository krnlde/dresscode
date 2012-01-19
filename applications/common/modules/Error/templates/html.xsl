<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="error">
		<div class="error">
			<h1>Error</h1>
			<xsl:apply-templates/>
		</div>
	</xsl:template>

	<!-- Soft Warning -->
	<xsl:template match="*">
		<div class="warning">
			<h1>XSLT Warning</h1>
			<p>
				<xsl:text>There is no template for module </xsl:text>
				<q>
					<xsl:value-of select="name()"/>
				</q>
				<xsl:text>.</xsl:text>
			</p>
			<p>
				<xsl:value-of select="."/>
			</p>
		</div>
	</xsl:template>


</xsl:stylesheet>