<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="mediaplayer">
		<video id="{@id}">
			<source src="{@source}" type="video/mp4" />
		</video>
	</xsl:template>

</xsl:stylesheet>