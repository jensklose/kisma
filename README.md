![Kisma](https://github.com/lucifurious/kisma/raw/master/assets/logo-kisma.png)

# Kisma&trade;: PHP Utility Kernel v0.3.x-dev
Thanks for checking out *Kisma*!

<a href="http://www.jetbrains.com/phpstorm/" style="display:block; background:#fff url(http://www.jetbrains.com/phpstorm/documentation/phpstorm_banners/phpstorm1/phpstorm468x60_white.gif) no-repeat 10px 50%; border:solid 1px #5854b5; margin:0;padding:0;text-decoration:none;text-indent:0;letter-spacing:-0.001em; width:466px; height:58px" alt="Lightning-smart IDE for PHP development" title="Lightning-smart IDE for PHP development"><span style="margin: 3px 0 0 65px;padding: 0;float: left;font-size: 12px;cursor:pointer;  background-image:none;border:0;color: #5854b5; font-family: trebuchet ms,arial,sans-serif;font-weight: normal;text-align:left;">Developed with</span><span style="margin:0 0 0 185px;padding:18px 0 2px 0; line-height:13px;font-size:13px;cursor:pointer;  background-image:none;border:0;display:block; width:270px; color:#5854b5; font-family: trebuchet ms,arial,sans-serif;font-weight: normal;text-align:left;">Lightning-smart IDE for PHP<br/>development</span></a>

# About the name...
Besides being a town in the Rift Valley of Kenya, "kisma" is the [Quechuan](http://en.wikipedia.org/wiki/Quechua_people) word for "womb". Since all living things are birthed
from a womb (sort of) I thought why not applications? So the that's where I came up with the name. Yes, it's whimsical. Big whoop, whaddya gonna do about it?

The base class of many Kisma&trade; classes is called "Seed", as it is from this class that all life (i.e. application functionality) springs. This is a lightweight base object that
provides very limited, but useful functionality (like an event hook interface). No magic methods, no chicanery. Just pure PHP.

Secondly, the "size" of the library is labeled as "fun-sized". Yes, more whimsicality. I've grown weary of the micro-, macro-, nano-, mega- framework arguments of late. So cope.

A library is supposed to help you, the coder, develop whatever it is you're trying to develop in a timely, productive fashion. If you have to jump through a thousand hoops just
to bootstrap the damned utility, it's not easy.  If there are choices for configuration file formats, that's not easy. I'm all for flexibility,
but I'm more in favor of maintainability. I can't have one person on my team writing his config files in YAML, another in PHP, one in XML,
etc. I'm not knocking frameworks that accept/allow this. I'm just saying that I've avoided that for the sake of consistency, maintainability, ease of use, and readability.

# Design Goals

These are the design goals of Kisma&trade;. My original goal was to create a really kick-ass web framework. But I don't have the time nor the inclination to take on that level of
coding. So I scaled it way back to just be a library of cool shit. This is basically all the utility classes and whatnot that I've written over the last decade assembled into a
5.3 namespaced library. You can use as much or as little of it as you want.

While the library is NOT specifically designed for ultra-fast performance (it ain't slow either), execution speed was the primary goal of certain areas (i.e. caching data for
subsequent calls, limited instantiation/invokation within loops, etc.). While the code is, for the most part, stream-lined and fast, I'm sure there are areas where it could be
improved to make it faster. However, I've focused on readability and consistency over speed. Can you use this library on your web site? Absolutely. Will it freak out
 (Symfony|Yii|Cake|Silex|<framework-du-jour>)? It shouldn't. Well, that's cool!

* Fully leverage PHP 5.3, its features such as namespaces, embracing the DRY KISS.
* Use built-in PHP library calls whenever possible for speed.
* Consistent access/interface usage by all objects
* Completely extensible from the base up, minimal cohesion and coupling.
* Usable from/with any other framework or library
* ABSOLUTELY NO USE OF MAGIC __get() AND __set() or public properties.

I will be working on more documentation when I flesh out my model more.

# Features

* Easy to use and lightweight
* Quicker to code repetitive tasks
* All setters return `$this` for easy chaining
* Easy to configure
* PSR-* compliant
* Packagist/Composer-compatible!

# Installation

## Composer

<blockquote cite="https://getcomposer.org/doc/00-intro.md">
Composer is a tool for dependency management in PHP. It allows you to declare the dependent libraries your project needs and it will install them in your project for you.
<br/>
<div style="display: inline-block; font-size: 75%; color: #999999"><small><em>-- Composer Website</em></small></div>
</blockquote>

Kisma&trade; is PSR-0 compliant and can be installed using [composer](http://getcomposer.org/).  Simply require `kisma/kisma` to your composer.json file.

    {
        "require": {
            "kisma/kisma": "@stable"
        }
    }

## Install from Source

Because Kisma&trade; is PSR-0 compliant, you can also just clone the repo and use a PSR-0 compatible autoloader to load the library, like the one in [Symfony](http://symfony.com/doc/current/components/class_loader.html).

## Phar

A [PHP Archive](http://php.net/manual/en/book.phar.php) (or .phar) file is not yet available.

# Requirements

* PHP v5.3+
