<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="toc">
		<nav>
			<ul class="well nav nav-list">
				<xsl:copy-of select="@id"/>
				<xsl:if test="@class">
					<xsl:attribute name="class">
						<xsl:text>well nav nav-list </xsl:text>
						<xsl:value-of select="@class"/>
					</xsl:attribute>
				</xsl:if>
				<li class="nav-header">Contents</li>
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