<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="gallery">
		<!-- render image -->
		<div>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:apply-templates/>
		</div>
		<!--<img src="/image.php?source={@source}&amp;orientation={@orientation}&amp;crop={@crop}" alt="{@description}" title="{@description}" />-->
	</xsl:template>

</xsl:stylesheet>