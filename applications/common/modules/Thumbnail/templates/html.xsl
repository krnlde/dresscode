<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="thumbnail">
		<!-- render image -->
		<div>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:apply-templates select="." mode="inner"/>
		</div>
	</xsl:template>

	<xsl:template match="thumbnail" mode="inner">
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
						<xsl:if test="@crop">
							<xsl:text>&amp;crop=</xsl:text><!-- always crop thumbnails -->
							<xsl:value-of select="@crop"/>
						</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<div><!-- default -->
			<xsl:if test="not(../../*[contains(@class, 'col')])"> <!-- this is bad; better produce a general rule -->
				<xsl:attribute name="class">
					<xsl:text>col col-lg-2</xsl:text>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="../@span">
				<xsl:attribute name="class">
					<xsl:text>col col-lg-</xsl:text>
					<xsl:value-of select="../@span"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="*"><!-- has sub contents -->
					<figure class="thumbnail">
						<img src="{$source}" alt="{@description}"/>
						<figcaption class="caption">
							<xsl:apply-templates/>
						</figcaption>
					</figure>
				</xsl:when>
				<xsl:otherwise>
					<a href="{@source}" rel="lightbox[{@group}]" title="{@description}" class="thumbnail">
						<img src="{$source}" alt="{@description}"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>

</xsl:stylesheet>