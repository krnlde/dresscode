dresscode framework
===================

The dresscode framework is a PHP5-driven, completely object-oriented web-framework which makes use of **XML** and **XSLT** to hold and display data. This means there is no requirement for any database (SQL or NoSQL), but you can make use of everything - as you wish.

Main features
-------------

* Asset Management System [Assetic](https://github.com/kriswallsmith/assetic) to combine and minify CSS & JS (also used by Symfony)
* [Bootstrap](https://github.com/twitter/bootstrap) together with its [Font-Awesome](https://github.com/FortAwesome/Font-Awesome)-plugin
* An [OPL](https://github.com/OPL/opl3-autoloader) compliant Autoloader
* XML as a model base
* XSLT as template engine - believe me it's awesome
* module oriented environment
* An Event-System based on jQuery Events; it even makes use of callbacks (Closures)
* Simply traverse the XML model tree with `find`, `findOne` and `closest`

In fact, I assembled just all the best practices, recommendations and frameworks of the today's web.

Clone
-----

To clone this repo please use `git clone git://github.com/krnlde/dresscode.git --recursive`.

**The `--recursive` parameter is very important because it enables git to clone all the submodules too.**