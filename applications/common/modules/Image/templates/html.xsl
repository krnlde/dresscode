<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="image">
		<xsl:copy-of select="@id"/>
		<xsl:copy-of select="@class"/>
		<!-- render image -->
		<img src="{@source}" alt="{@description}" title="{@description}" />
		<!--<img src="/image.php?source={@source}&amp;orientation={@orientation}&amp;crop={@crop}" alt="{@description}" title="{@description}" />-->
	</xsl:template>

</xsl:stylesheet>