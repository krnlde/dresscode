<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	extension-element-prefixes="php">

	<xsl:template match="input">
		<xsl:choose>
			<xsl:when test="@type='hidden'">
				<input>
					<xsl:copy-of select="@id"/>
					<xsl:copy-of select="@class"/><!-- @todo see highlight-->
					<xsl:copy-of select="@type"/>
					<xsl:copy-of select="@name"/>
					<xsl:copy-of select="@value"/>
					<xsl:copy-of select="@minlength"/>
					<xsl:copy-of select="@maxlength"/>
					<xsl:copy-of select="@pattern"/>
					<xsl:if test="@required = 1">
						<xsl:attribute name="required">required</xsl:attribute>
					</xsl:if>
					<xsl:if test="@readonly = 1">
						<xsl:attribute name="readonly">readonly</xsl:attribute>
					</xsl:if>
					<xsl:if test="@disabled = 1">
						<xsl:attribute name="disabled">disabled</xsl:attribute>
					</xsl:if>
				</input>
			</xsl:when>
			<xsl:otherwise>
				<div class="form-group">
					<xsl:if test="@highlight = 1">
						<xsl:attribute name="class">control-group error</xsl:attribute>
					</xsl:if>
					<xsl:if test="@label">
						<label class="control-label" for="{@id}">
							<xsl:value-of select="@label"/>
							<xsl:if test="@required = 1">
								<span class="required">*</span>
							</xsl:if>
						</label>
					</xsl:if>
					<xsl:choose>
						<xsl:when test="@type='textarea'">
							<textarea class="form-control">
								<xsl:copy-of select="@id"/>
								<xsl:if test="@class">
                  <xsl:text>form-control </xsl:text>
                  <xsl:value-of select="@class"/>
                  <!-- @todo see highlight-->
                </xsl:if>
								<xsl:copy-of select="@name"/>
								<xsl:copy-of select="@title"/>
								<xsl:copy-of select="@caption"/>
								<xsl:copy-of select="@placeholder"/>
								<xsl:copy-of select="@minlength"/>
								<xsl:copy-of select="@maxlength"/>
								<xsl:copy-of select="@pattern"/>
								<xsl:if test="@required = 1">
									<xsl:attribute name="required">required</xsl:attribute>
								</xsl:if>
								<xsl:if test="@readonly = 1">
									<xsl:attribute name="readonly">readonly</xsl:attribute>
								</xsl:if>
								<xsl:if test="@disabled = 1">
									<xsl:attribute name="disabled">disabled</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="@value"/>
								<xsl:if test="@value = ''"><xsl:text> </xsl:text></xsl:if>
							</textarea>
							<xsl:if test="@highlight = 1">
								<span class="help-inline">
									<xsl:value-of select="@title"/>
								</span>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<input class="form-control">
								<xsl:copy-of select="@id"/>
                <xsl:if test="@class">
                  <xsl:text>form-control </xsl:text>
                  <xsl:value-of select="@class"/>
								  <!-- @todo see highlight-->
                </xsl:if>
								<xsl:copy-of select="@type"/>
								<xsl:copy-of select="@name"/>
								<xsl:copy-of select="@value"/>
								<xsl:copy-of select="@title"/>
								<xsl:copy-of select="@caption"/>
								<xsl:copy-of select="@placeholder"/>
								<xsl:copy-of select="@minlength"/>
								<xsl:copy-of select="@maxlength"/>
								<xsl:copy-of select="@pattern"/>
								<xsl:if test="@required = 1">
									<xsl:attribute name="required">required</xsl:attribute>
								</xsl:if>
								<xsl:if test="@readonly = 1">
									<xsl:attribute name="readonly">readonly</xsl:attribute>
								</xsl:if>
								<xsl:if test="@disabled = 1">
									<xsl:attribute name="disabled">disabled</xsl:attribute>
								</xsl:if>
							</input>
							<xsl:if test="@type='file' and contains(@class, 'fancy')">
								<xsl:comment>Here happens some javascript-magic on the clientside</xsl:comment>
							</xsl:if>
							<xsl:if test="@highlight = 1">
								<span class="help-inline">
									<xsl:value-of select="@title"/>
								</span>
							</xsl:if>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>