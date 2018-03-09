NotReallyPsrResourceManager
===========================

Specification, interfaces, and abstract classes for web application management of 3rd party JS/CSS

The [PHP-FIG](https://www.php-fig.org/) creates useful standards for web
applications and frameworks to use that allow interoperability with each other.

Well, I have one I would like to propose but it did not seem to get a lot of
enthusiasm from the
[PHP-FIG Google Group](https://groups.google.com/forum/#!forum/php-fig).

Hopefully a proposal here will gain more traction, either with the PHP user
community at large or even better, with PHP-FIG which is where I believe this
kind of standard actually belongs.

Maybe if I worked for a big company that funded them, it would be easier. But
I am a nobody that has no social skills, no job, but what I believe is a valid
problem with a solution that can be standardized.

Yes, I am being cynical. Cue the Classic Mac OS system sound sosumi. Okay the
truth is I have an incredible amount of respect for the PHP-FIG and I am very
glad they exist. I also believe that I have trouble communicating, and if they
understood what I was really proposing, it would have had more enthusiasm.


The Problem
-----------

Web Applications quite frequently utilize third party JavaScript libraries
(e.g. jQuery) and third party CSS (e.g. Normalize).

Very frequently these web applications include a local copy of these third
party resources which can sometimes become stale with bugs that have been
fixed by the upstream developers.

Very often these web applications do not include things like the `integrity`
attribute which is critical in cases where the script is hosted on a CDN or
other remote server.


The Solution
------------

The solution is to create a standard way that a web application can use third
party JavaScript and CSS without needing to bundle these resources themselves,
so they can easily be updated by the system administrator when API compatible
updates are released, and in a way that automatically adds the `integrity`
attribute which increases the security of the end user, protecting the end user
from potentially malicious code that has been installed on a CDN or other host.


PHP Part of Solution
--------------------

The approach to solving the problem is to create some standards that PHP
libraries can implement. Below are what I currently have in mind.

### FileResource Abstract Class

A standardized abstract class called `FileResource` will exist that has some
basic information about files a web application may wish to embed in their
web pages.

In the context of the Resource Manager, these files are JavaScript and CSS but
I believe this abstract class should be basic enough in scope that it is an
appropriate choice for other uses as well.

This abstract class probably should contain the following properties:

* `protected $mime = null;`  
  The MIME type the file should be served with.
* `protected $checksum = null;`  
  The checksum of the file as `algo:checksum` where `algo` is an element of the
  array returned by the `hash_algos()` command and `checksum` is either hex or
  base64 encoded.
* `protected $filepath = null;`  
  The path to the resource on the local filesystem.
* `protected $lastmod = null;`  
  The last time the file was modified. Note that this is often different than
  the time stamp on the filesystem.
* `protected $urlscheme = null;`  
  Since this class is for file resources included in web pages, it really IMHO
  should be restricted to `http` or `https` but I am open to others. Please
  note I am not a fan of non-standard ports being used by web applications, the
  practice can be dangerous, so I do not feel this class should support them.
* `protected $urlhost = null;`  
  The hostname the file is hosted on, which may not be the local host.
* `protected $urlpath = null;`  
  The server path to the file.
* `protected $urlquery = null;`  
  Sometimes a query string is used by servers as part of determining which file
  to serve. A practice I do not like, but very common.

Note that all those properties in the abstract class *may* be null.

The abstract class should have the following public functions defined:

* `showMime()`  
  Returns the contents of the `$mime` property.

* `showChecksum()`  
  Returns the contents of the `$checksum` property.

* `validateFile()`  
  When both the `$checksum` property and the `$filepath` property are set, the
  ability to validate the file should exist.

* `resourceURI()`  
  Build a URI out of the various `$url*` properties.

* `getTimeStamp()`
  Return the UNIX timestamp.


### JavaScriptResource Interface

Classes will need to extend the abstract FileResource class and extend this
interface to create an object for JavaScript resource. The interface defines
the following public functions:

* `getSrcAttribute()`  
  What goes into the `src` attribute of a `<script>` node. This may actually
  be redundant given the `resourceURI()` method in the FileResource abstract
  class.
* `getTypeAttribute()`  
  What goes into the `type` attribute of a `<script>` node. Usually the MIME
  type but not always.
* `getTypeAttribute()`  
  Whether or not the boolean `async` attribute should be present.
* `getCrossOriginAttribute()`  
  What goes into the `crossorigin` attribute, if present.
* `getDeferAttribute();`  
  Whether or not the boolean `defer` attribute should be present.
* `getIntegrityAttribute()`  
  What goes into the `integrity` attribute when present.
* `getNoModuleAttribute()`  
  Whether or not the boolean `nomodule` attribute should be present.
* `generateScriptDomNode($dom, $nonce = null)`  
  Creates a `\DOMNode` `<script>` node, with an optional nonce.
* `generateScriptString(bool $xml = false, $nonce = null)`  
  Creates a string, either HTML or XHTML, for the script node.

### CssResource Interface

Not yet described or thought out.


### ResourceManager Interface

Classes that implement this interface are what web applications would directly
interact with. It should define two public functions:

* `getJavaScript(string $vendor, string $product, string $name, $version, $variant = null)`  
  Returns an instance of the `FileResource` object that implements the
  `JavaScriptResource` interface on success, null on failure.
* `getCss(string $vendor, string $product, string $name, $version, $variant = null)`  
  Returns an instance of the `FileResource` object that implements the
  `CssResource` interface on success, null on failure.

A web application would use it like this:

    $foo = new \whatever\namespace\ResourceManager;
    $obj = $foo->getJavaScript('awonderphp', 'commonjs', 'jquery', 3, "slim.min");

Then from the `$obj` the web application could use either
`generateScriptDomNode()` or `generateScriptString()` to add the current slim
variant of jQuery 3 to their web application.


JSON Part of Solution
---------------------

write more later

































