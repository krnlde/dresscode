<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="toc">
		<ul>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:for-each select="element">
				<li>
					<a href="#{@id}">
						<xsl:apply-templates/>
					</a>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>

</xsl:stylesheet>