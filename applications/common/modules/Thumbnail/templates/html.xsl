<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php"
	>

	<xsl:template match="thumbnail">
		<xsl:variable name="source">
			<xsl:choose>
				<xsl:when test="starts-with(@source, 'http')">
					<xsl:value-of select="@source"/>
				</xsl:when>
				<xsl:otherwise>
						<xsl:value-of select="php:function('\Mocovi\Application::basePath')"/>
						<xsl:text>/image.php?source=</xsl:text>
						<xsl:value-of select="@source"/>
						<xsl:text>&amp;size=</xsl:text>
						<xsl:value-of select="@size"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<!-- render image -->
		<a href="{@source}" rel="lightbox[{@group}]" title="{@description}">
			<img src="{$source}" alt="{@description}">
				<xsl:copy-of select="@id"/>
				<xsl:copy-of select="@class"/>
			</img>
		</a>
		<!--<img src="/image.php?source={@source}&amp;orientation={@orientation}&amp;crop={@crop}" alt="{@description}" title="{@description}" />-->
	</xsl:template>

</xsl:stylesheet>