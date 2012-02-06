<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="root">
		<!-- HTML5 Doctype -->
		<xsl:text disable-output-escaping="yes">&lt;!DOCTYPE html&gt;&#xA;</xsl:text><!-- &#xA; == Line Break -->
		<html lang="{@language}">
			<head>
				<meta charset="utf-8" />
				<title><xsl:value-of select="@title" /></title>

				<meta name="viewport" content="width=device-width, initial-scale=1" />
				<meta name="author" content="{@author}" />
				<meta name="date" content="{php:function('date', 'c')}"/>

				<link rel="canonical" href="{@canonical}"/>
				<link rel="stylesheet" type="text/css" href="{php:function('\Mocovi\Application::dumpStylesheets')}"/>
				<link rel="stylesheet" type="text/css" href="http://twitter.github.com/bootstrap/assets/css/bootstrap-responsive.css"/>

				<!-- HTML5shiv enables HTML5 elements in old browsers, like IE < 9 -->
				<xsl:comment><![CDATA[[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]]]></xsl:comment>
			</head>
			<body>
				<div id="container">
					<xsl:apply-templates/>
				</div>
				<!--
					yahoo says: Put javascripts at the bottom.
					So I put javascripts at the bottom.
					source: http://developer.yahoo.com/performance/rules.html#js_bottom
				-->
				<script type="text/javascript" src="{php:function('\Mocovi\Application::dumpJavascripts')}"><xsl:text> </xsl:text></script>
			</body>
		</html>
	</xsl:template>

</xsl:stylesheet>