<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:output
		method="xml"
		media-type="text/xml"
		encoding="UTF-8"
		indent="yes"
	/>

	<xsl:template match="/root">
		<urlset>
			<xsl:comment>
				<xsl:value-of select="count(//sitemap[1]//element)"/>
				<xsl:text> elements</xsl:text>
			</xsl:comment>
			<xsl:for-each select="//sitemap[1]//element">
				<url>
					<loc>
						<xsl:text>http://</xsl:text>
						<xsl:value-of select="php:function('\Dresscode\Application::getName')"/>
						<xsl:value-of select="@path"/>
					</loc>
					<xsl:if test="@modified">
						<lastmod><xsl:value-of select="@modified"/></lastmod>
					</xsl:if>

					<!--<changefreq>monthly</changefreq>-->

					<xsl:if test="@priority">
						<priority><xsl:value-of select="@priority"/></priority>
					</xsl:if>
				</url>
			</xsl:for-each>
		</urlset>
	</xsl:template>

</xsl:stylesheet>