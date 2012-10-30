<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:atom="http://www.w3.org/2005/Atom">

<!-- sollte wieder method="xml" werden !-->
	<xsl:output
		method="xml"
		media-type="text/xml"
		encoding="UTF-8"
		indent="yes"
	/>
	 <!--
	 	Elements sind die Elemente für die das gilt
		<xsl:strip-space elements="*"/>
		or
	 	<xsl:preserve-space elements="*"/>
	 -->

	<xsl:variable name="domain" select="'krnl.de'"/>

	<xsl:template match="root">
		<xsl:variable name="url">
			<xsl:text>http://</xsl:text><xsl:value-of select="$domain"/><xsl:text>/sitemap.rss</xsl:text>
		</xsl:variable>
		<rss version="2.0">
			<channel>
				<atom:link href="{$url}" rel="self" type="application/rss+xml" />
				<title><xsl:value-of select="$domain"/> # <xsl:value-of select="@title"/></title>
				<description><xsl:value-of select="//paragraph[1]"/></description>
				<link><xsl:value-of select="$url"/></link>
				<lastBuildDate><xsl:value-of select="//@modified[1]"/></lastBuildDate>
				<!--lastBuildDate><xsl:value-of select="//element[1]/@lastModified"/></lastBuildDate-->
				<generator>
					<xsl:text>dresscode </xsl:text>
					<xsl:value-of select="system-property('xsl:vendor')" />
					<xsl:value-of select="system-property('xsl:version')" />
				</generator>
				<!--
				<image>
					<url>http://www.golem.de/_img/golemlogo_70.png</url>
					<title>Golem.de Logo</title>
					<link>http://www.golem.de/</link>
					<description>Golem.de News Feed</description>
				</image>
				-->
				<language><xsl:value-of select="@language"/></language>
				<xsl:for-each select="//sitemap/element">
					<xsl:sort select="@order" order="descending"/>
						<xsl:call-template name="element"/>
				</xsl:for-each>
			</channel>
		</rss>
	</xsl:template>

	<xsl:template name="element">
		<item>
			<title>
				<xsl:value-of select="@alias"/>
			</title>
			<link>
				<xsl:text>http://</xsl:text><xsl:value-of select="$domain"/><xsl:text></xsl:text><xsl:value-of select="@path"/>
			</link>
			<description>
				<xsl:value-of select="description"/>
			</description>
			<pubDate>
				<xsl:value-of select="@modified"/>
			</pubDate>
			<guid>
				<xsl:text>http://</xsl:text><xsl:value-of select="$domain"/><xsl:text></xsl:text><xsl:value-of select="@path"/>
			</guid>
			<!--
			<author>
				<xsl:value-of select="@author"/>
			</author>
			-->
		</item>
	</xsl:template>

</xsl:stylesheet>