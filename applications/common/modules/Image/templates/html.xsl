<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="image">
		<!-- render image -->
			<img src="{@source}" alt="{@description}">
        <xsl:if test="string-length(@description) &gt; 0">
          <xsl:attribute name="title">
            <xsl:value-of select="@description"/>
          </xsl:attribute>
        </xsl:if>
				<xsl:copy-of select="@id"/>
				<xsl:copy-of select="@class"/>
			</img>
		<!--<img src="/image.php?source={@source}&amp;orientation={@orientation}&amp;crop={@crop}" alt="{@description}" title="{@description}" />-->
	</xsl:template>

</xsl:stylesheet>