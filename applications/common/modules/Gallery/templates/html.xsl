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
			<xsl:for-each select="*">
				<li class="span3">
					<xsl:apply-templates select="."/>
				</li>
			</xsl:for-each>
		</ul>
		<!--<img src="/image.php?source={@source}&amp;orientation={@orientation}&amp;crop={@crop}" alt="{@description}" title="{@description}" />-->
	</xsl:template>

	<xsl:template match="thumbnail">
		<xsl:variable name="source">
			<xsl:choose>
				<xsl:when test="starts-with(@source, 'http')">
					<xsl:value-of select="@source"/>
				</xsl:when>
				<xsl:otherwise>
						<xsl:value-of select="php:function('\Dresscode\Application::basePath')"/>
						<xsl:text>/image.php?source=</xsl:text>
						<xsl:value-of select="@source"/>
						<xsl:text>&amp;size=</xsl:text>
						<xsl:value-of select="@size"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<!-- render image -->
		<a href="{@source}" rel="lightbox[{@group}]" title="{@description}" class="thumbnail">
			<img src="{$source}" alt="{@description}">
				<xsl:copy-of select="@id"/>
				<xsl:copy-of select="@class"/>
			</img>
		</a>
		<!--<img src="/image.php?source={@source}&amp;orientation={@orientation}&amp;crop={@crop}" alt="{@description}" title="{@description}" />-->
	</xsl:template>

</xsl:stylesheet>