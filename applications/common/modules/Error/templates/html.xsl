<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="/error">
		<!-- HTML5 Doctype -->
		<xsl:text disable-output-escaping="yes">&lt;!DOCTYPE html&gt;&#xA;</xsl:text><!-- &#xA; == Line Break -->
		<html>

			<xsl:if test="@language">
				<xsl:attribute name="lang">
					<xsl:value-of select="@language"/>
				</xsl:attribute>
			</xsl:if>
			<head>
				<meta charset="utf-8" />
				<title><xsl:value-of select="@title" /> - <xsl:value-of select="@domain" /></title>

				<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
				<xsl:if test="string-length(@keywords) &gt; 0">
					<meta name="keywords" content="{@keywords}"/>
				</xsl:if>
				<meta name="description" content="{php:function('trim', string(//article[1]/*[not(name(.) = 'header')]//paragraph[1]))}"/>
				<xsl:if test="string-length(@author) &gt; 0">
					<meta name="author" content="{@author}"/>
				</xsl:if>
				<meta name="date" content="{php:function('date', 'c')}"/>

				<link rel="canonical" href="{@canonical}"/>
				<link rel="stylesheet" type="text/css" href="{php:function('\Mocovi\Application::dumpStylesheets')}" media="all"/>

				<!-- HTML5shiv enables HTML5 elements in old browsers, like IE < 9 -->
				<!-- obsolete since jQuery 1.7 -->
				<xsl:comment><![CDATA[[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]]]></xsl:comment>
			</head>
			<body>
				<div class="container">
					<xsl:call-template name="error" select="."/>
				</div>
				<!--
					yahoo says: Put javascripts at the bottom.
					So I put javascripts at the bottom.
					source: http://developer.yahoo.com/performance/rules.html#js_bottom
				-->
				<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"><xsl:text> </xsl:text></script>
				<script type="text/javascript">
					<xsl:text disable-output-escaping="yes">window.jQuery || document.write('&lt;script type="text/javascript" src="</xsl:text>
					<xsl:value-of select="$assets"/>
					<xsl:text disable-output-escaping="yes">/js/jquery.min.js"&gt;&lt;\/script&gt;');</xsl:text>
				</script>
				<script type="text/javascript" src="{php:function('\Mocovi\Application::dumpJavascripts')}"><xsl:text> </xsl:text></script>
			</body>
		</html>
	</xsl:template>


	<xsl:template match="/*//error" name="error">
		<div class="alert alert-block alert-error">
			<a class="close" data-dismiss="alert">×</a>
			<h4 class="alert-heading">Error</h4>
			<xsl:apply-templates/>
		</div>
	</xsl:template>

	<!-- Soft Warning -->
	<!-- <xsl:template match="*">
		<div class="warning">
			<h1>XSLT Warning</h1>
			<p>
				<xsl:text>There is no template for module </xsl:text>
				<q>
					<xsl:value-of select="name()"/>
				</q>
				<xsl:text>.</xsl:text>
			</p>
			<p>
				<xsl:value-of select="."/>
			</p>
		</div>
	</xsl:template> -->


</xsl:stylesheet>