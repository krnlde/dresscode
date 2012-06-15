dresscode framework
===================

The dresscode framework is a PHP5-driven, completely object-oriented web-framework which makes use of **XML** and **XSLT** to hold and display data. This means there is no requirement for any database (SQL or NoSQL), but you can make use of everything - as you wish of course.

Main features
-------------

* Asset Management System [Assetic](https://github.com/kriswallsmith/assetic) to combine and minify LESS, Sass, CSS and JS (also used by [Symfony](https://github.com/symfony/symfony))
* [Bootstrap](https://github.com/twitter/bootstrap) together with its [Font-Awesome](https://github.com/FortAwesome/Font-Awesome)-plugin
* An [OPL](https://github.com/OPL/opl3-autoloader) compliant Autoloader
* Image editing library [Imagine](https://github.com/avalanche123/Imagine) to perform simple tasks like cropping, scaling and resizing
* XML as a model base
* XSLT as template engine - believe me, it's f$*#ing awesome
* module oriented environment for a kick-ass fast development of new modules
* An Event-System based on jQuery Events; it even makes use of callbacks (Closures)
* Simply traverse the XML model tree with `find`, `findOne` and `closest`

In fact, I just assembled all these best-practices, recommendations and frameworks of the today's web and added some spice.

Clone
-----

To clone this repo please use `git clone git://github.com/krnlde/dresscode.git --recursive`.

**The `--recursive` parameter is very important because it enables git to clone all the submodules too.**

FYI: The default page comes with a demo site located in `applications/127.0.0.1/models/model.xml`.