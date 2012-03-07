<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="input">
		<xsl:if test="@label">
			<label for="{@id}">
				<xsl:value-of select="@label"/>
			</label>
		</xsl:if>
		<input>
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/><!-- @todo see highlight-->
			<xsl:copy-of select="@type"/>
			<xsl:copy-of select="@name"/>
			<xsl:copy-of select="@value"/>
			<xsl:copy-of select="@placeholder"/>
			<xsl:copy-of select="@minlength"/>
			<xsl:copy-of select="@maxlength"/>
			<xsl:copy-of select="@pattern"/>
			<xsl:if test="@required = 1">
				<xsl:attribute name="required">required</xsl:attribute>
			</xsl:if>
			<xsl:if test="@readonly = 1">
				<xsl:attribute name="readonly">readonly</xsl:attribute>
			</xsl:if>
			<xsl:if test="@disabled = 1">
				<xsl:attribute name="disabled">disabled</xsl:attribute>
			</xsl:if>
			<xsl:if test="@highlight = 1">
				<xsl:attribute name="class">highlight</xsl:attribute><!-- @todo see class-->
				<xsl:attribute name="style">border: 2px solid red;</xsl:attribute>
			</xsl:if>
		</input>
		<br/><!-- temporary -->
	</xsl:template>

</xsl:stylesheet>