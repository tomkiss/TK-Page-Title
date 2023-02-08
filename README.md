# TK Page Title Expression Engine 1.x Module

This Expression Engine Add-On allows you to automatically output your own image-based titles for weblog entries. It does so with gradient-filled backgrounds and 24-bit PNG transparency.

## Requirements

 - The CP JQuery extension is required in order to use the included JQuery colour picker [1.1.0]
 - Tested on Expression Engine 1.6.4, 1.6.5 and 1.6.6 (may work with lower versions...)
 - Tested only on PHP 5.2.6 (should work with lower versions...)
 - Requires GD 2.0.1+
 - Knowledge of Cascading Style Sheets (CSS)
 - Knowledge of the Internet Explorer .htc PNG fix to allow for full cross browser support


## Parameters

 - `antialias` - [boolean] - Allows for text Anti-Aliasing to be turned off if set to false. [1.0.5]
 - `align` - [string] - Overrides the default text alignment and allows text to be aligned within the specified width of the image. Accepted values are: left, center, right, left-adjust, center-adjust, right-adjust or adjust [1.0.4]
 - `case` - [string] - Converts the case of any string. accepts 'upper' and 'title'. Use with caution on localised text.
 - `class` - [string] - Defines an HTML class attribute for the containing HTML element. Will always contain an HTML class called pagetitle however.
 - `color` - [text] - Sets the font colour in RGB, eg: FFCC00. This will be used if a gradient fill image is not defined. [1.0.7]
 - `cssonly` - [boolean] - If set to true, this outputs only the style attribute containing all relevant css to display the image [1.0.9]
 - `debug` - [boolean] - Prevents image caching, outputs some variables when generating the image.
 - `fsize` - [int] - Sets the font size. Overrides the default defined in the page title type.
 - `gradient` - [string] - A .png image location from URL root on the server, eg: /images/file.png. Overrides the default defined in the page title type.
 - `id` - [string] - Defines an id attribute for the containing HTML element.
 - `leading` - [int] - Defines leading for the image. [1.1.0]
 - `link` - [string] - This will add an a tag within the wrapping tag and link to that place automatically
 - `name` - [boolean] - Outputs name attribute on containing element or a tag, if present. [1.1.2]
 - `nocache` - [boolean] - Prevents image caching. Use only for testing. [1.1.0]
 - `style` - [string] - Defines additional style properties for the containing HTML element. Will always contain the default styles that define the background-image, width etc. [1.0.4]
 - `tag` - [string] - This defines the wrapping HTML tag for that item (h1, h2, h3, p etc). Overrides the default defined in the page title type.
 - `target` - [string] - Defines target value for links, eg: _blank, _self.
 - `type` - [string] - The name of a page title type, a set of pre-defined properties saved in the TK Page Title Control Panel.
 - `width` - [int] - Defines a specific. Overrides the default defined in the page title type.

## Installation

As with installing all modules, you would be wise to backup your database before installing.

To install TK Page Title, you must:

 - First, copy the its files to your Expression Engine installation. The zip archive is organised so that you can copy the files directly to the root of a 'regular' EE installation.
 - Change the permissions on the directory where the images created will be stored using your FTP client, CHMOD 777 the directory /themes/tk_page_title/pagetitles (the module will not work without these permissions set).
 - Login to your Expression Engine control panel and navigate to modules page. From here, click 'Install' next to the TK Page Titles module.
 
With that complete, you are now ready to roll.

## Example

The following example should give you an idea of how to use the TK Page Title module.

```php
{exp:tk_page_title}My title or weblog title {title} {/exp:tk_page_title}```

This will output the following HTML:

```html
<h1 style="display:block; background-repeat: no-repeat; background-image: url(/themes/tk_page_title/pagetitles/sample.png); height: 32px; width: 600px" class="pagetitle" title="My title or weblog title"><span style="display: none">My title or weblog title</span></h1>```

## Control Panel

Here you can create different types of page title, each with their own fonts, sizes and colours...

## Important notes

Remember that if you upload unlicensed fonts to your server for use with TK Page Title, you may be breaking the law! You are advised to store your fonts outside of your sites public directory (public_html, www, httpdocs or similar); doing this will almost guarantee unauthorised access/distribution of your fonts.
Feedback and discussion

If you have any feedback, feel free to comment on this page. There is also an EE Forum post and the module is listed on devot-ee.

## Credits

In addition to its core code, written by myself, this module uses other PHP scripts made freely available on the web. Much thanks to Andrew Collington for his imagemask class and to Matsuda Shota for his awesome font classes.

For more information, please visit: https://2010.tomkiss.net/ee/add-on/tk_page_title
