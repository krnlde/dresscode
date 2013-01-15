<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="breadcrumb">
		<div class="navbar">
			<nav class="navbar-inner">
				<a class="brand" href="#">Breadcrumb</a>
				<ul class="nav">
					<xsl:apply-templates select="element" mode="breadcrumb"/>
				</ul>
			</nav>
		</div>
	</xsl:template>

	<xsl:template match="element" mode="breadcrumb">
		<li class="dropdown">
			<xsl:choose>
				<xsl:when test="@active">
					<xsl:attribute name="class">dropdown active</xsl:attribute>
					<a role="button" class="dropdown-toggle" data-toggle="dropdown"><xsl:value-of select="."/> <b class="caret"><xsl:text> </xsl:text></b></a>
				</xsl:when>
				<xsl:otherwise>
					<a role="button" class="dropdown-toggle" data-toggle="dropdown"><xsl:value-of select="."/> <b class="caret"><xsl:text> </xsl:text></b></a>
				</xsl:otherwise>
			</xsl:choose>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="">Test <xsl:value-of select="."/></a>
					<a href="">blubb</a>
				</li>
			</ul>
		</li>
	</xsl:template>

</xsl:stylesheet>