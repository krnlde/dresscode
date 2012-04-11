<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet
	version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="form">
		<form method="{@method}">
			<xsl:copy-of select="@id"/>
			<xsl:copy-of select="@class"/>
			<fieldset>
				<xsl:apply-templates/>
			</fieldset>
			<div class="form-actions">
				<input type="submit" class="btn btn-primary"/>
				<input type="reset" class="btn"/>
			</div>
		</form>
	</xsl:template>

</xsl:stylesheet>