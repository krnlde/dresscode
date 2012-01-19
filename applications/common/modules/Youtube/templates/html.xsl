<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="youtube">
		<object type="application/x-shockwave-flash" class="youtube">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:attribute name="data">
				<xsl:text>http://www.youtube.com/v/</xsl:text>
				<xsl:value-of select="@videoId"/>
				<xsl:text>&amp;color1=0xb1b1b1&amp;color2=0xcfcfcf&amp;feature=player_embedded&amp;fs=1</xsl:text>
			</xsl:attribute>
			<param name="movie">
				<xsl:attribute name="value">
					<xsl:text>http://www.youtube.com/v/</xsl:text>
					<xsl:value-of select="@videoId"/>
					<xsl:text>&amp;color1=0xb1b1b1&amp;color2=0xcfcfcf&amp;feature=player_embedded&amp;fs=1</xsl:text>
				</xsl:attribute>
			</param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowScriptAccess" value="always"></param>
		</object>
	</xsl:template>

</xsl:stylesheet>