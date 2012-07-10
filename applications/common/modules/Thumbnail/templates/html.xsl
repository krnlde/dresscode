<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

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
		<ul class="thumbnails">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:apply-templates select="." mode="inner"/>
		</ul>
		<!--<img src="/image.php?source={@source}&amp;orientation={@orientation}&amp;crop={@crop}" alt="{@description}" title="{@description}" />-->
	</xsl:template>

	<xsl:template match="thumbnail" mode="inner">
		<li class="span3">
			<xsl:choose>
				<xsl:when test="*">
					<div class="thumbnail">
						<img src="{@source}" alt="{@description}"/>
						<div class="caption">
							<xsl:apply-templates/>
							<xsl:if test="boolean(string(../@more))">
								<div class="more"><xsl:text> </xsl:text></div>
							</xsl:if>
						</div>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<a href="{@source}" rel="lightbox[{@group}]" title="{@description}" class="thumbnail">
						<img src="{@source}" alt="{@description}"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
		</li>
	</xsl:template>

</xsl:stylesheet>