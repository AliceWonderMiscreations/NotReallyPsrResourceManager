NotReallyPsrResourceManager
===========================

The funny not very user friendly namespace is being used because this is a
developer preview of a concept I hope will be adopted and refined by an
actual standards body. I do not want to be an actual standards body, I want
to *implement* what a standards body says to implement.

Specification, interfaces, and abstract classes for web application management of 3rd party JS/CSS

Subject to change, not finished, and deity I hope to get input from others.

A reference implementation even though this isn't finished yet is at
[AliceWonderMiscreations/ResourceManager](https://github.com/AliceWonderMiscreations/ResourceManager)

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

1. [The Problem](#the-problem)
2. [The Solution](#the-solution)
3. [PHP Part of Solution](#php-part-of-solution)
4. [FileResource Abstract Class](#fileresource-abstract-class)
5. [JavaScriptResource Interface](#javascriptresource-interface)
6. [CssResource Interface](#cssresource-interface)
7. [ResourceManager Interface](#resourcemanager-interface)
8. [ResourceServer Interface](#resourceserver-interface)
9. [ResourceManagerException Interface](#resourcemanagerexception-interface)
10. [JSON Part of Solution](#json-part-of-solution)
11. [Common JSON Elements to JavaScript and CSS](#common-json-elements-to-javascript-and-css)
12. [JavaScript JSON](#javascript-json)
13. [CSS JSON](#css-json)
14. [File System and Config File Naming](#file-system-and-config-file-naming)
15. [Wrapper Script](#wrapper-script)


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


### ResourceServer Interface

Web Applications that implement this should use a class that implements this
interface to serve requests for third party JavaScript within the `/js`
directory. It defines three public functions:

* `serveFileResource($fileResource, bool $minify = false)`  
  Takes a `\AWonderPHP\NotReallyPsrResourceManager\FileResource` object as the
  first argument and an optional boolean as the second argument. Implementors
  are not required to minify if the second argument is set to true, but may do
  so if the `FileResource` both has a `minified` property *and* that proprty is
  set to false, and the FileResource does __NOT__ have a checksum property.
  This is a public function but the intent is actually for use by the next two
  public functions.
* `serveJavaScript(string $vendor, string $product, string $name, $version, $variant = null, bool $minify = false)`  
  First attempts to get the `FileResource` object associated with the
  parameters and then use that object with the `serveFileResource` function to
  serve the file to the client, passing the `$minify` parameter along.
* `serveCSS(string $vendor, string $product, string $name, $version, $variant = null, bool $minify = false)`  
  First attempts to get the `FileResource` object associated with the
  parameters and then use that object with the `serveFileResource` function to
  serve the file to the client, passing the `$minify` parameter along.

All three functions are expected to return `false` on failure, `true` on
success.


### ResourceManagerException Interface

Does not define any public functions, solely exists as a way to catch
exceptions and have them identified as associated with the ResourceManager.
This is the same concept PSR-16 and others use.


JSON Part of Solution
---------------------

The ResourceManager will hunt for a JSON configuration file that matches the
resource the web application requests and then create the object to return from
the JSON file. That needs a standard JSON file format.

### Common JSON Elements to JavaScript and CSS

* `name` String, required:  
  The name of the script or css, sans version and variant, e.g. `jquery`.
* `homepage` String, not strictly required:  
  The URL homepage of the project
* `version` String, required:  
  The version of the script being described.
* `license` Array, required:  
  An array containing one or more applicable license for the script. Each
  element of the array should contain license name and a URL for the
  license.

Those fields are not utilized by the classes but are metadata that is useful to
a system administrator. Other fields common to both JS and CSS:

* `mime` String, required:  
  The MIME type that should be used when serving the file.
* `checksum` String, recommended:  
  The `algo:checksum` described in the `FileResource` abstract class.
* `filepath` String, optional:  
  If the file is present on the server, the filesystem path to the file.
* `lastmod` String, recommended:  
  A string that can be parsed by `strtotime()` to create a UNIX timestamp
  indicating when the file was last modified. For many projects, this is
  specified in a comment header of the file itself, and in those cases, that
  string should be used.
* `srcurl` String, required:  
  What should go in the `src` or `href` attribute. Must be parseable by the
  `parse_url` function and internationalized domains should be in punycode.
* `minified` Boolean, recommended:  
  If set to false *and* the `integrity` attribute is not being used, some file
  wrappers may wish to minify on the fly.

### JavaScript JSON

A sample of what a JavaScript JSON might look like:

    {
        "name": "jquery",
        "homepage": "https://jquery.com/",
        "version": "3.3.1",
        "license": [
            {
                "name": "MIT",
                "url": "https://jquery.org/license/"
            }
        ],
        "mime": "application/javascript",
        "checksum": "sha256:160a426ff2894252cd7cebbdd6d6b7da8fcd319c65b70468f10b6690c45d02ef",
        "filepath": "awonderphp/commonjs/js/jquery-3.3.1.min.js",
        "lastmod": "2018-01-20T17:24Z",
        "minified": true,
        "srcurl": "/js/jquery-3.3.1.min.js",
        "async": true
    }

JavaScript Specific Fields:

* `async` Boolean, optional:  
  Only needed if it is desired to have that attribute, then set to `true`.
* `defer` Boolean, optional:  
  Only needed if it is desired to have that attribute, then set to `true`.
* `nomodule` Boolean, optional:  
  Only needed if it is desired to have that attribute, then set to `true`.
* ??`type`?? Needs Exploration:  
  In most cases, the `type` attribute is set to the MIME type, but it may be
  necessary to set it to `modular` for ES6 modular feature, I still need to
  learn about that.

### CSS JSON

Needs to be written


File System and Config File Naming
----------------------------------

All scripts should be installed with a hierarchy of
`$base/VendorName/ProductName` where `VendorName` and `ProductName` are lower
case, as is Composer convention for PHP libraries.

In the case of Composer install of JS/CSS libraries, the Composer `vendor`
directory would be the `$base` directory.

Within the `ProductName` directory, an `etc` directory __MUST__ exist that has
the JSON configuration files, and it is *RECOMENDED* that the actual JavaScript
files reside in a `js` directory and CSS files reside in a `css` directory.

An example of what this would look like is at
[AliceWonderMiscreations/CommonJS](https://github.com/AliceWonderMiscreations/CommonJS)

Default configuration files would be named using:

    ScriptName-Version-Variant.json.dist

Where ScriptName is the name of the script (e.g. `jquery`), Version is the
version of the script (e.g. `3.3.1`), and if present, Variant would be the
script variant (e.g. `min` or `slim` or `slim.min`).

The default configuration should have the `srcurl` point to a local URL, e.g.

    "srcurl": "/js/jquery-3.3.1.min.js",

When a system administrator wants to customize what is in the configuration,
they simply copy the file so that it no longer ends in `.dist` and then they
can modify it (e.g. to set `srcurl` to a CDN).

The ResourceManager __MUST__ give priority to the configuration file without
the `.dist` if it is found.

With the `major.minor.point` versioning scheme, a configuration file should
exist for both `major.minor` and `major` that are identical to the default
configuration file for the most recent `major.minor.point` that match.


Wrapper Script
--------------

Web Applications that implement this __MUST__ be able to handle requests to the
default `/js/` and `/css/` locations.

This can be accomplished by a wrapper script. An interface should be written.

The way it would work, the web applications would need to have `mod_rewrite`
or whatever configured to handle requests for the script, fetch the object
using the ResourceManager, and serve the file.

I plan to extend my
[FileWrapper](https://github.com/AliceWonderMiscreations/FileWrapper) class to
work for this, once an interface is created to implement with the extended
class.































