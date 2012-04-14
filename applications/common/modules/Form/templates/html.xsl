<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="form">
		<form method="{@method}">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<fieldset>
				<xsl:apply-templates/>
			</fieldset>
			<div class="form-actions">
				<button type="submit" class="btn">
					<i class="icon-ok"><xsl:text> </xsl:text></i>
					<xsl:value-of select="php:function('\Mocovi\Translator::translate', 'button.submit')"/>
				</button>
				<button type="reset" class="btn">
					<i class="icon-refresh"><xsl:text> </xsl:text></i>
					<xsl:value-of select="php:function('\Mocovi\Translator::translate', 'button.reset')"/>
				</button>
			</div>
		</form>
	</xsl:template>

</xsl:stylesheet>