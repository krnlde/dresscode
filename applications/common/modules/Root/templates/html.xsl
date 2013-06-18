<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="root">
		<xsl:variable name="externalStylesheets" select="php:function('\Dresscode\Application::getExternalStylesheets')"/>
		<xsl:variable name="externalJavascripts" select="php:function('\Dresscode\Application::getExternalJavascripts')"/>
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

				<xsl:for-each select="$externalStylesheets/*/stylesheet">
					<xsl:if test="string-length(@href) &gt; 0">
						<link rel="stylesheet" type="text/css" href="{@href}" media="all">
							<xsl:copy-of select="@media"/>
						</link>
					</xsl:if>
				</xsl:for-each>

				<link rel="stylesheet" type="text/css" href="{php:function('\Dresscode\Application::dumpStylesheets')}" media="all"/>

				<!-- HTML5shiv enables HTML5 elements in old browsers, like IE < 9 -->
				<!-- obsolete since jQuery 1.7 -->
				<xsl:comment><![CDATA[[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]]]></xsl:comment>
			</head>
			<body>
				<div class="container">
					<xsl:apply-templates/>
				</div>
				<!--
					yahoo says: Put javascripts at the bottom.
					So I put javascripts at the bottom.
					source: http://developer.yahoo.com/performance/rules.html#js_bottom
				-->
				<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"><xsl:text> </xsl:text></script>
				<script type="text/javascript">
					<xsl:text disable-output-escaping="yes">window.jQuery || document.write('&lt;script type="text/javascript" src="</xsl:text>
					<xsl:value-of select="$assets"/>
					<xsl:text disable-output-escaping="yes">/js/jquery.min.js"&gt;&lt;\/script&gt;');</xsl:text>
				</script>

				<xsl:for-each select="$externalJavascripts/*/javascript">
					<xsl:if test="string-length(@href) &gt; 0">
						<script type="text/javascript" src="{@href}"><xsl:text> </xsl:text></script>
					</xsl:if>
				</xsl:for-each>

				<script type="text/javascript" src="{php:function('\Dresscode\Application::dumpJavascripts')}"><xsl:text> </xsl:text></script>
			</body>
		</html>
	</xsl:template>

</xsl:stylesheet>