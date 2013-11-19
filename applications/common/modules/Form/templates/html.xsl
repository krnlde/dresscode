<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="form">
		<form action="{@action}" method="{@method}">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<xsl:if test="@multipart = 1">
				<xsl:attribute name="enctype">multipart/form-data</xsl:attribute>
				<xsl:attribute name="method">post</xsl:attribute>
			</xsl:if>
			<fieldset>
				<xsl:apply-templates/>
			</fieldset>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">
					<i class="glyphicon glyphicon-send"><xsl:text> </xsl:text></i>
					<xsl:text> </xsl:text>
					<xsl:value-of select="php:function('\Dresscode\Translator::translate', 'Form.submit')"/>
				</button>
				<button type="reset" class="btn btn-default">
					<i class="glyphicon glyphicon-refresh"><xsl:text> </xsl:text></i>
					<xsl:text> </xsl:text>
					<xsl:value-of select="php:function('\Dresscode\Translator::translate', 'Form.reset')"/>
				</button>
			</div>
				<xsl:if test=".//input[@required = 1]">
					<span class="help-block">
						<span class="required">* </span>
						<xsl:value-of select="php:function('\Dresscode\Translator::translate', 'Form.RequiredInfo')"/>
					</span>
				</xsl:if>
		</form>
	</xsl:template>

</xsl:stylesheet>