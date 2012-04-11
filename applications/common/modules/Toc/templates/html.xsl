<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="toc">
		<nav>
			<ul class="nav nav-list well">
				<xsl:copy-of select="@id"/>
				<xsl:if test="@class">
					<xsl:attribute name="class">
						<xsl:text>nav nav-list well </xsl:text>
						<xsl:value-of select="@class"/>
					</xsl:attribute>
				</xsl:if>
				<xsl:for-each select="element">
					<li>
						<a href="#{@id}">
							<xsl:apply-templates/>
						</a>
					</li>
				</xsl:for-each>
			</ul>
		</nav>
	</xsl:template>

</xsl:stylesheet>