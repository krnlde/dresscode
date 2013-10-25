jQuery(function ($) {
  "use strict";


   // Based on www.jottings.com/obfuscator.htm
  function decrypt(str, cipher) {
    var shift = str.length;
    var decrypted = '';
    for (var i = 0; i < str.length; i++) {
      if (cipher.indexOf(str.charAt(i)) == -1) {
        decrypted += (str.charAt(i));
      }
      else {
        var ltr = (cipher.indexOf(str.charAt(i)) - shift + cipher.length) % cipher.length;
        decrypted += (cipher.charAt(ltr));
      }
    }
    return decrypted;
  }

  $('a[data-cipher]').each(function () {
    var $this = $(this);
    var title = $this.attr('title');
    var cipher = $this.data('cipher');

    $this.attr('href', decrypt($this.attr('href'), cipher));
    $this.attr('title', decrypt($this.attr('title'), cipher));
    $this.text(decrypt($this.text(), cipher));
    $this.data('cipher', undefined);
    $this.removeAttr('data-cipher');
  });
});