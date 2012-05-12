<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="form">
		<form action="" method="{@method}">
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
					<i class="icon-ok"><xsl:text> </xsl:text></i>
					<xsl:value-of select="php:function('\Mocovi\Translator::translate', 'button.submit')"/>
				</button>
				<button type="reset" class="btn">
					<i class="icon-refresh"><xsl:text> </xsl:text></i>
					<xsl:value-of select="php:function('\Mocovi\Translator::translate', 'button.reset')"/>
				</button>
			</div>
				<xsl:if test=".//input[@required = 1]">
					<div><span class="required">*</span> <xsl:value-of select="php:function('\Mocovi\Translator::translate', 'FormRequiredInfo')"/></div>
				</xsl:if>
		</form>
	</xsl:template>

</xsl:stylesheet>