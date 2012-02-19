<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php"
	>

	<xsl:template match="thumbnail">
		<!-- render image -->
		<a href="{@source}" rel="lightbox[{@group}]" title="{@description}">
			<img src="{php:function('\Mocovi\Application::basePath')}/image.php?source={@source}&amp;size={@size}" alt="{@description}">
				<xsl:copy-of select="@id"/>
				<xsl:copy-of select="@class"/>
			</img>
		</a>
		<!--<img src="/image.php?source={@source}&amp;orientation={@orientation}&amp;crop={@crop}" alt="{@description}" title="{@description}" />-->
	</xsl:template>

</xsl:stylesheet>