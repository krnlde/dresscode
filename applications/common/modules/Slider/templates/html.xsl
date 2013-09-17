<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="slider">
		<div class="carousel slide">
			<xsl:copy-of select="@id"/>
			<xsl:if test="@class">
				<xsl:attribute name="class">
					<xsl:text>carousel slide </xsl:text>
					<xsl:value-of select="@class"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="@transitionSpeed">
				<xsl:attribute name="data-interval">
					<xsl:value-of select="@transitionSpeed"/>
				</xsl:attribute>
			</xsl:if>
			<ol class="carousel-indicators">
				<xsl:for-each select="*">
					<li data-target="#{../@id}" data-slide-to="{position() - 1}">
						<xsl:if test="position() = 1">
							<xsl:attribute name="class">active</xsl:attribute>
						</xsl:if>
					</li>
				</xsl:for-each>
			</ol>
			<div class="carousel-inner">
				<xsl:for-each select="*">
					<div class="item">
						<xsl:if test="position() = 1">
							<xsl:attribute name="class">item active</xsl:attribute>
						</xsl:if>
						<xsl:apply-templates select="."/>
					</div>
				</xsl:for-each>
			</div>
			<!-- Controls -->
			<!-- <a class="left carousel-control" href="#{@id}" data-slide="prev">
				<span class="icon-prev"></span>
			</a>
			<a class="right carousel-control" href="#{@id}" data-slide="next">
				<span class="icon-next"></span>
			</a> -->
		</div>
	</xsl:template>

</xsl:stylesheet>