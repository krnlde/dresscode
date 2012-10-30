<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="sitemap" name="sitemap">
		<xsl:param name="elements" select="element"/>
		<xsl:param name="inner" select="false()"/>
		<xsl:variable name="list">
			<ul>
				<xsl:for-each select="$elements">
					<xsl:variable name="path">
						<xsl:if test="not(starts-with(@path, 'http'))">
							<xsl:value-of select="$basepath"/>
						</xsl:if>
						<xsl:value-of select="@path"/>
					</xsl:variable>
					<li>
						<xsl:if test="@active">
							<xsl:attribute name="class">
								<xsl:text>active</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<a href="{$path}" title="{$path}">
							<xsl:value-of select="@alias"/>
						</a>
						<xsl:if test="sitemap">
							<xsl:call-template name="sitemap">
								<xsl:with-param name="elements" select="sitemap/element"/>
								<xsl:with-param name="inner" select="true()"/>
							</xsl:call-template>
						</xsl:if>
					</li>
				</xsl:for-each>
			</ul>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="$inner">
				<xsl:copy-of select="$list"/>
			</xsl:when>
			<xsl:otherwise>
				<nav>
					<xsl:copy-of select="@id"/>
					<xsl:copy-of select="@class"/>
					<xsl:copy-of select="$list"/>
				</nav>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>