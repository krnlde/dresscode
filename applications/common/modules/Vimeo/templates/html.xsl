<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="vimeo">
		<object type="application/x-shockwave-flash" class="vimeo">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:attribute name="data">
				<xsl:text>http://vimeo.com/moogaloop.swf?clip_id=</xsl:text>
				<xsl:value-of select="@videoId"/>
				<xsl:text>&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1</xsl:text>
			</xsl:attribute>
			<param name="movie">
				<xsl:attribute name="value">
					<xsl:text>http://vimeo.com/moogaloop.swf?clip_id=</xsl:text>
					<xsl:value-of select="@videoId"/>
					<xsl:text>&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1</xsl:text>
				</xsl:attribute>
			</param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowScriptAccess" value="always"></param>
		</object>
	</xsl:template>

</xsl:stylesheet>