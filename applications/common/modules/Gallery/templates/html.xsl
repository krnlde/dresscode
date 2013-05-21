<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<!-- <xsl:import href="../../Tabs/templates/html.xsl"/> -->

	<xsl:template match="gallery">
		<!-- render image -->
		<!-- XSLT 2.0:    <xsl:for-each-group select="gallery" group-by="(position() - 1) idiv 6"> -->
		<xsl:if test="count(thumbnail) &gt; @perTab">
			<ul class="nav nav-pills">
				<xsl:call-template name="pills">
					<xsl:with-param name="id" select="@id"/>
					<xsl:with-param name="count" select="count(thumbnail)"/>
					<xsl:with-param name="perTab" select="@perTab"/>
					<xsl:with-param name="foldAfter" select="@foldAfter"/>
				</xsl:call-template>
			</ul>
		</xsl:if>
		<div class="tab-content">
			<xsl:copy-of select="@id"/>
			<xsl:call-template name="tab-content">
				<xsl:with-param name="id" select="@id"/>
				<xsl:with-param name="perTab" select="@perTab"/>
				<xsl:with-param name="thumbnails" select="thumbnail"/>
			</xsl:call-template>
		</div>
	</xsl:template>

	<xsl:template name="pills">
		<xsl:param name="id"/>
		<xsl:param name="count"/>
		<xsl:param name="perTab"/>
		<xsl:param name="foldAfter"/>
		<xsl:param name="index" select="1"/>
		<xsl:param name="active" select="1"/>
		<xsl:if test="$index = 1">
			<li>
				<a href="#" class="first">
					<xsl:text disable-output-escaping="yes">&amp;laquo;</xsl:text>
				</a>
			</li>
			<li>
				<a href="#" class="previous">
					<xsl:text disable-output-escaping="yes">&amp;lt;</xsl:text>
				</a>
			</li>
		</xsl:if>
		<li>
			<xsl:if test="$index = $active">
				<xsl:attribute name="class">active</xsl:attribute>
			</xsl:if>
			<a data-toggle="pill">
				<xsl:attribute name="href">#tabs-<xsl:value-of select="$id" />-<xsl:value-of select="$index"/></xsl:attribute>
				<xsl:value-of select="$index"/>
			</a>
		</li>
		<xsl:if test="($perTab * $index) &lt; $count">
			<xsl:call-template name="pills">
				<xsl:with-param name="id" select="$id"/>
				<xsl:with-param name="count" select="$count"/>
				<xsl:with-param name="perTab" select="$perTab"/>
				<xsl:with-param name="index" select="$index + 1"/>
			</xsl:call-template>
		</xsl:if>
		<xsl:if test="($perTab * $index) &gt;= ($count)">
			<li>
				<a href="#" class="next">
					<xsl:text disable-output-escaping="yes">&amp;gt;</xsl:text>
				</a>
			</li>
			<li>
				<a href="#" class="last">
					<xsl:text disable-output-escaping="yes">&amp;raquo;</xsl:text>
				</a>
			</li>
		</xsl:if>
	</xsl:template>

	<xsl:template name="tab-content">
		<xsl:param name="id"/>
		<xsl:param name="perTab"/>
		<xsl:param name="thumbnails"/>
		<xsl:param name="index" select="1"/>
		<xsl:param name="active" select="1"/>
		<div class="tab-pane" id="tabs-{$id}-{$index}">
			<xsl:if test="$index = $active">
				<xsl:attribute name="class">tab-pane active</xsl:attribute>
			</xsl:if>
			<div class="row">
				<xsl:apply-templates select="$thumbnails[position() &lt;= $perTab]" mode="inner"/>
			</div>
		</div>
		<xsl:if test="count($thumbnails) - $perTab &gt; 0">
			<xsl:call-template name="tab-content">
				<xsl:with-param name="id" select="$id"/>
				<xsl:with-param name="perTab" select="$perTab"/>
				<xsl:with-param name="thumbnails" select="$thumbnails[position() &gt; $perTab]"/>
				<xsl:with-param name="index" select="$index + 1"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>