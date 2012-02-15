# mocovi web framework

mocovi is a [single-source-](http://de.wikipedia.org/wiki/Single_Source_Publishing) and cross-media-publishing framework, based on an hierarchical MVC pattern. The model is provided by an XML database, controllers are written in PHP5-classes and the view is based on XSLT, which allows e.g. HTML output. It's the best choice when you prefer to manage and control your content with XML, you like to program your ideas with object-oriented PHP and handle the resulting output with XSLT templates.

## Why mocovi?

For me _understanding_ was the most complicated part in programming. So I decided to start over an make everything easy, clear and human-readable. No abbreviations. No "$v1, $v2, $v3" and [stuff](http://wikipedia.org/wiki/Anti-pattern). Read everything like a book.

Starting with reading and observing other products I saw that many Content Management Systems and Frameworks lack in structured models and are being developed by many different programmers over many years - which means patchworked code and furthermore inconsistencies. Plus, nearly every CMS uses [MySQL](http://wikipedia.org/wiki/MySQL) to store every kind of data. In my opinion databases should only be used for huge loads of constantly changing and indexable data. MySQL is hard to understand and to handle [relational tables](http://dev.mysql.com/doc/refman/5.5/en/innodb-foreign-key-constraints.html). This is why mocovi is based on [W3C standards](http://www.w3.org/standards/xml/) ([XML](http://www.w3.org/TR/xml/) and [XSLT](http://www.w3.org/TR/xslt)) and programming design patterns (written in [PHP](http://www.php.net/)).

## Why [XML](http://wikipedia.org/wiki/XML) as data model

XML is readable by humans and computers likewise. XML won't be a problem if you already dealed with HTML. Its structure is easy to understand and some nice features like xi:includes and xref:references can speed up your work a lot!

## Why [PHP](http://wikipedia.org/wiki/PHP) as controller and [Apache](http://wikipedia.org/wiki/Apache_webserver) as server

PHP became more serious since version [5.3](http://php.net/releases/5_3_0.php) (and of course 6). Many deficiencies were cleaned up. The object oriented features like [Late Static Binding](http://php.net/manual/language.oop5.late-static-bindings.php), [Namespaces](http://php.net/manual/language.namespaces.php) and even [Closures](http://php.net/manual/functions.anonymous.php) were added recently. The support of PHP on the webservers of the providers is common.

Apache is one of the most powerful and secure webserver in the world. With .htaccess you can control nearly every behaviour of the server. URL-Rewriting for instance.

## Why XSLT as view

Because you can choose either a formatted output for better reading or a stripped one for more performance on the client side. Well formed and W3C compliant!

More than just one output format: Plain-Text, [XML](http://wikipedia.org/wiki/XML), [JSON](http://wikipedia.org/wiki/Json), [PDF](http://wikipedia.org/wiki/PDF) and everything else you want.

## Summarized

* single-source XML models
* cross-media publishing via XSLT templates
* Object Oriented PHP5 controllers
* No other dependencies
* HTML5-ready
* Multi-Domain support
* Token based translations

## Requirements

* PHP 5.3.5+
	* libxml 2.7.7+
	* libxslt 1.1.23+
* Apache 2.2.17+

## Install

Go to your htdocs directory in your command line and execute the following command `git clone git://github.com/krnlde/mocovi.git mocovi`.

Now open your web-browser at http://127.0.0.1/mocovi/ and see the rendered website.

Please note that the directories found in `mocovi/applications` represent the current domain, which means the contents of the directory `127.0.0.1` will be used to render the output.

You can set a default domain instead of using the domain recognition in the `mocovi/options.php`.

Play around and explore the rest :)