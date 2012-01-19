<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="menu" name="menu">
		<xsl:param name="elements" select="element"/>
		<xsl:param name="inner" select="false()"/>
		<xsl:variable name="list">
			<ul>
				<xsl:for-each select="$elements">
					<li>
						<xsl:if test="@active">
							<xsl:attribute name="class">
								<xsl:text>active</xsl:text>
							</xsl:attribute>
						</xsl:if>
						<a href="{$basepath}{@path}" title="{$basepath}{@path}">
							<xsl:value-of select="@alias"/>
						</a>
						<xsl:if test="menu">
							<xsl:call-template name="menu">
								<xsl:with-param name="elements" select="menu/element"/>
								<xsl:with-param name="inner" select="true()"/>
							</xsl:call-template>
						</xsl:if>
					</li>
				</xsl:for-each>
			</ul>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="$inner">
				<xsl:copy-of select="$list"/>
			</xsl:when>
			<xsl:otherwise>
				<nav>
					<xsl:copy-of select="@id"/>
					<xsl:copy-of select="@class"/>
					<xsl:copy-of select="$list"/>
				</nav>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>