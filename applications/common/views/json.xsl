<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output indent="no" omit-xml-declaration="yes" method="text" encoding="UTF-8" media-type="text/x-json"/>
	<xsl:strip-space elements="*" />

	<!-- JSON object must be enclosed in an array -->
	<xsl:template match="/">
		<xsl:text>{</xsl:text>
		<xsl:apply-templates />
		<xsl:text>}</xsl:text>
	</xsl:template>

	<xsl:template match="*">
		<xsl:text>"</xsl:text>
		<xsl:value-of select="name()" />
		<!-- http://www.dpawson.co.uk/xsl/sect2/xpath.html 3. xpath to any node -->
		<!-- add an index if siblings have the same name and are not grouped in a parent tag -->
		<xsl:if test="count(../node()[name(.) = name(current())]) > 1">
			<xsl:value-of select="count(preceding-sibling::*[name()=name(current())]) + 1"/>
		</xsl:if>
		<xsl:text>":{</xsl:text>

		<!-- only open or close object declaration when not dealing with an array -->
		<!--<xsl:if test="not(*[count(../*[name(../*)=name(.)]) = count(../*) and count(../*) > 1])">{</xsl:if>-->

		<!-- attributes -->
		<xsl:apply-templates select="@*" />
		<xsl:if test="@* and child::node()">,</xsl:if><!-- separate attributes from child nodes/text if necessary -->
		<xsl:apply-templates select="child::node()" />

		<xsl:if test="not(following-sibling::*)">}</xsl:if>

		<!-- close object (only when not dealing with an array) or separate object attributes -->
		<!--<xsl:if test="not(*[count(../*[name(../*)=name(.)]) = count(../*) and count(../*) > 1])">}</xsl:if>-->
		<xsl:if test="following-sibling::* or following-sibling::text()">},</xsl:if>
	</xsl:template>

	<!-- process text nodes -->
	<xsl:template match="text()">
		<xsl:text>"$text</xsl:text>
		<xsl:if test="count(../text()) > 1">
			<xsl:value-of select="count(preceding-sibling::text()) + 1"/>
		</xsl:if>
		<xsl:text>":</xsl:text>
		<xsl:call-template name="format-value">
			<xsl:with-param name="s" select="." />
		</xsl:call-template>
		<xsl:if test="following-sibling::*">,</xsl:if>
	</xsl:template>

	<!-- process attributes and respectives values -->
	<xsl:template match="@*">
		<xsl:text>"@</xsl:text>
		<xsl:value-of select="name()" />
		<xsl:text>":</xsl:text>

		<xsl:call-template name="format-value">
			<xsl:with-param name="s" select="." />
		</xsl:call-template>

		<xsl:if test="position() != last()">,</xsl:if>
	</xsl:template>

	<!-- arrays -->
	<xsl:template match="*[count(../*[name(../*)=name(.)]) = count(../*) and count(../*) > 1]">
		<xsl:if test="not(preceding-sibling::*)">
			<xsl:text>"</xsl:text>
			<xsl:value-of select="name(../../*)" />
			<xsl:text>":[</xsl:text>
		</xsl:if>
		<xsl:text>{"</xsl:text><xsl:value-of select="name()" /><xsl:text>":{</xsl:text>

		<xsl:apply-templates select="@*" />
		<xsl:if test="@* and child::node()">,</xsl:if><!-- separate attributes from child nodes/text if necessary -->
		<xsl:apply-templates select="child::node()" />

		<xsl:if test="following-sibling::*">}},</xsl:if>
		<xsl:if test="not(following-sibling::*)">}}]</xsl:if>
	</xsl:template>


	<!-- Auxiliary template to be called for attribute values and tag text -->
	<xsl:template name="format-value">
		<xsl:param name="s" />
		<xsl:choose>
			<xsl:when test="string(number($s)) = 'NaN'"><!-- string, escape and quote -->
				<xsl:text>"</xsl:text>
				<xsl:call-template name="escape-backslash">
					<xsl:with-param name="s" select="normalize-space($s)" />
				</xsl:call-template>
				<xsl:text>"</xsl:text>
			</xsl:when>
			<xsl:otherwise><!-- number, print as it is -->
				<xsl:value-of select="normalize-space($s)" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Auxiliary template to be called for escaping backslashes -->
	<xsl:template name="escape-backslash">
		<xsl:param name="s"/>
		<xsl:choose>
		 <xsl:when test='contains($s, "\")'>
			<xsl:value-of select="substring-before($s,'\')" />
			<xsl:text>\\</xsl:text>
			<xsl:call-template name="escape-backslash">
			 <xsl:with-param name="s" select="substring-after($s, '\')" />
			</xsl:call-template>
		 </xsl:when>
		 <xsl:otherwise>
			<xsl:call-template name="escape-quotes">
				<xsl:with-param name="s" select="$s" />
			</xsl:call-template>
		 </xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Auxiliary template to be called for escaping quotes -->
	<xsl:template name="escape-quotes">
		<xsl:param name="s"/>
		<xsl:choose>
		 <xsl:when test="contains($s, '&quot;')">
			<xsl:value-of select="substring-before($s,'&quot;')" />
			<xsl:text>\"</xsl:text>
			<xsl:call-template name="escape-quotes">
			 <xsl:with-param name="s" select="substring-after($s, '&quot;')" />
			</xsl:call-template>
		 </xsl:when>
		 <xsl:otherwise>
			<xsl:value-of select="$s" />
		 </xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>
