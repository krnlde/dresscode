<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:output
		method="xml"
		omit-xml-declaration="yes"
		media-type="text/html"
		encoding="UTF-8"
		indent="yes"
	/>


	<xsl:template match="text()">
		<!--
			this is not suitable when using for example: "foo <element>bar</element>".
			This will output "foobar" instead of "foo bar"
		-->
		<!-- <xsl:value-of select="normalize-space(.)"/> -->
		<!-- better, but slow approach - it also may produce "<x> text </x>" -->
		<!-- <xsl:value-of select="php:function('preg_replace', '/\s{2,}/', ' ', string(.))"/> -->
		<xsl:value-of select="."/>
	</xsl:template>




	<xsl:variable name="basepath" select="php:function('\Dresscode\Application::basePath')" />
	<xsl:variable name="apppath">
		<xsl:value-of select="$basepath" />
		<xsl:text>/applications</xsl:text>
	</xsl:variable>
	<xsl:variable name="common">
		<xsl:value-of select="$apppath" />
		<xsl:text>/common</xsl:text>
	</xsl:variable>
	<xsl:variable name="user">
		<xsl:value-of select="$apppath" />
		<xsl:text>/</xsl:text>
		<xsl:value-of select="php:function('\Dresscode\Application::getName')" />
	</xsl:variable>
	<xsl:variable name="assets">
		<xsl:value-of select="$common" />
		<xsl:text>/assets</xsl:text>
	</xsl:variable>
	<xsl:variable name="userAssets">
		<xsl:value-of select="$user" />
		<xsl:text>/assets</xsl:text>
	</xsl:variable>


	<xsl:template match="collection">
		<collection>
			<xsl:apply-templates/>
		</collection>
	</xsl:template>





	<!-- This will be filled dynamically -->





</xsl:stylesheet>