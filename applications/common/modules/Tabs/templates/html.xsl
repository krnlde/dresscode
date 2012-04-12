<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="tabs">
		<xsl:variable name="id" select="@id"/>
		<xsl:variable name="maximum" select="@maximum"/>
		<xsl:variable name="transition" select="@transition"/>
		<div>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<ul class="nav nav-tabs">
				<xsl:for-each select="*">
					<xsl:if test="position() &lt;= $maximum">
						<li>
							<xsl:if test="position() = 1">
								<xsl:attribute name="class">active</xsl:attribute>
							</xsl:if>
							<a href="#tab{position()}" data-toggle="tab"><xsl:value-of select="position()"/></a>
						</li>
					</xsl:if>
				</xsl:for-each>
				<xsl:if test="count(*) &gt; $maximum">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">...<b class="caret"><xsl:text> </xsl:text></b></a>
						<ul class="dropdown-menu">
							<xsl:for-each select="*[position() &gt; $maximum]">
								<li>
									<a href="#tab{position()+$maximum}" data-toggle="tab"><xsl:value-of select="position()+$maximum"/></a>
								</li>
							</xsl:for-each>
						</ul>
					</li>
				</xsl:if>
			</ul>
			<div class="tab-content">
				<xsl:for-each select="*">
					<div id="tab{position()}">
						<xsl:attribute name="class">
							<xsl:text>tab-pane</xsl:text>
							<xsl:if test="$transition">
								<xsl:text> </xsl:text>
								<xsl:value-of select="$transition"/>
							</xsl:if>
							<xsl:if test="position() = 1">
								<xsl:text> active in</xsl:text>
							</xsl:if>
						</xsl:attribute>
						<xsl:apply-templates select="."/>
					</div>
				</xsl:for-each>
			</div>
		</div>
	</xsl:template>

</xsl:stylesheet>