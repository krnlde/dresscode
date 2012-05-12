<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="todolist">
		<section>
			<xsl:copy-of select="@class"/>
			<ul>
				<xsl:copy-of select="@id"/>
				<xsl:if test="not(element)">
					<xsl:text> </xsl:text>
				</xsl:if>
				<xsl:apply-templates select="element"/>
			</ul>
		</section>
		<xsl:apply-templates select="*[not(name()='element')]"/>
	</xsl:template>

	<xsl:template match="element">
		<li>
			<xsl:apply-templates/>
		</li>
	</xsl:template>

</xsl:stylesheet>