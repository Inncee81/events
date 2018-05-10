=== Baqend ===
Contributors: baqend
Tags: performance, caching, optimization, fast, secure, static website generator, speed
Requires at least: 4.6.0
Tested up to: 4.9.5
Stable tag: 1.6.2
Requires PHP: 5.5.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily use Baqend's cloud for your WordPress site.

== Description ==

= Speed Kit =

The WordPress plugin makes using **Baqend Speed Kit** a breeze. Simply head over to the *Speed Kit* tab and check *enabled* next to *Speed Kit Integration*.

Baqend Speed Kit accelerates your existing website by rerouting the requests through Baqend's caching infrastructure. Using the configuration described in this document, it works for any WordPress website and achieves a performance boost of typically 50-300%.

= Why Speed Kit? =

Page load time is money. This is not only true for companies like Amazon that loses more than 1.3B USD in revenue per year, if their website is a 10th of a second slower. It is also true for publishers, whose business model depends on a user experience that facilitates consumption of as much content as possible. However, many brands, publishers, and e-commerce businesses have heterogeneous and complex technology stacks that make it extremely hard to tackle performance, scalability, and page load time. Novel browser technologies now offer a means of making web performance as simple as including a script.

Baqend has developed **Speed Kit** that directly hooks into an existing website and makes it **50-300% faster**. Therefore, Speed Kit uses [Service Workers](https://developers.google.com/web/fundamentals/getting-started/primers/service-workers) which come with a **browser support of \> 75%** and automatically enable an **offline mode** for users of your website. Because it **works for any website**, it is the perfect solution for Publishers, Landing Pages, E-Commerce, Brands, and Agencies.

= Hosting =

The WordPress plugin also makes it very easy to use **Baqend Hosting**. A static copy of your WordPress page is created and published via our cloud. Performance enhancements of 50-500% are possible since Baqend's cloud technology relies on the latest standards, a very good network infrastructure and efficient browser caching.

== Installation ==

= Within WordPress =

1. Log into your WordPress website.
2. On the left menu, hover over Plugins and then click on Add New.
3. In the Search Plugins box, type in "Baqend" and press the Enter key.
4. You will see a list of search results which should include the Baqend plugin. Click on the Install Now button to install the plugin.
5. After installing the plugin you will be prompted to activate it. Click on the Activate Plugin link.
6. The Baqend plugin is now installed and can be found on the left menu.

= Upload Manually =

1. Download the official **Baqend plugin** from [the WordPress Plugin Repository](https://wordpress.org/plugins/baqend/) or [our website](https://www.baqend.com/wordpress-plugin/latest/baqend.zip).
2. On the left menu, hover over Plugins and then click on Add New.
3. Click on upload plugin and select the ZIP archive you just downloaded.
4. Activate the plugin.
5. The Baqend plugin is now installed and can be found on the left menu.

== Changelog ==
= v1.6.2 (2018-5-8) =


Bug Fixes

* Fix layout of account form
* Fix log out button bug

= v1.6.1 (2018-5-2) =


Bug Fixes

* Fix bug while updating a widget

= v1.6.0 (2018-4-24) =


Features

* Make revalidation more prominent
* Add dashboard widget
* Change design of Speed Kit config section
* Add update widgets action to trigger revalidation
* Add additional actions to trigger revalidation
* Extend default whitelist with common CDNs
* Add new cookies to blacklist

Bug Fixes

* Be more robust in finding the Speed Kit config entry

= v1.5.1 (2018-4-3) =


Features

* Display error messages if Speed Kit update fail
* Increase Speed Kit update frequency

Bug Fixes

* Fix pathname generation for WooCommerce
* Fix Speed Kit file updates
* Hide hosting on Getting Started page

= v1.5.0 (2018-3-21) =


Features

* Add French, German (Switzerland), and German (formal) translation
* Automatically enable Speed Kit on first activation
* Automatically login user on first activation
* Redirect to “Getting Started” if not logged in
* Redirect to “Getting Started” on plugin activation
* Add warning if user has not enabled SSL
* Add hint to the help in Speed Kit and Hosting tab
* Hide hosting help if hosting is disabled

Bug Fixes

* Fix plugin URI
* Fix typos and URLs in texts
* Update help text
* Fix building of admin URLs
* Fix PHP compatibility check

= v1.4.5 (2018-3-16) =


Improvements

* Improve hosting deployment speed
* Reduce plugin size by 70 kB

Bug Fixes

* Fix filenames not found by translate.wordpress.org

= v1.4.4 (2018-3-12) =


Features

* Translate into formal German (Switzerland)
* Make German (Germany) translation informal
* Update Baqend SDK

Bug Fixes

* Fix calculated scope URL
* Fix wrong example content in Speed Kit tab

= v1.4.3 (2018-3-12) =


Features

* Calculate the Service Worker scope by its URL

= v1.4.2 (2018-3-9) =


Bug Fixes

* Fix paths in subdirectory installations of WordPress

= v1.4.1 (2018-3-9) =


Features

* Reduce built ZIP size by 200 kB
* Improve logging with Monolog
* Improve bootstrapping performance

Bug Fixes

* Fix check whether login is broken
* Fix bug in check_plugin_update hook

= v1.4.0 (2018-3-7) =


Features

* Add API token support for revalidation
* Revalidate HTML after updating the plugin
* Add Speed Kit update cron hook

Bug Fixes

* Fix token revalidation code

= v1.3.5 (2018-2-19) =


Bug Fixes

* Fix release process

= v1.3.4 (2018-2-19) =


Bug Fixes

* Fix check for connection in frontend
* Update release process

= v1.3.3 (2018-2-19) =


Bug Fixes

* Fix resolving dependencies with plugin in subdirectory

= v1.3.2 (2018-2-12) =


Bug Fixes

* Fix bug with "maxStaleness" in config
* Fix dependencies for PHP 5.5.9

= v1.3.1 (2018-2-8) =


Bug Fixes

* Register new users with Speed Kit sign-up type
* Move composer.json up and remove Dockerfile

= v1.3.0 (2018-2-7) =


Bug Fixes

* Fix IDs in help view

Features

* Check the PHP version before activating
* Automatically retrieve fetch origin interval
* Tested up to WordPress 4.9.4

= v1.2.0 (2018-2-6) =


Features

* Tested up to WordPress 4.9.3
* Allow comments using semicolon in whitelist, blacklist, and cookie

= v1.1.5 (2018-2-1) =


Bug Fixes

* Fix PHP 7.0 Polyfill

= v1.1.4 (2018-2-1) =


Improvements

* Update Baqend PHP SDK

= v1.1.3 (2018-2-1) =


Bug Fixes

* Fix release cycle for README and CHANGELOG
* Add script to convert Markdown to WordPress README

= v1.1.2 (2018-2-1) =


Bug Fixes

* Tested up to WordPress 4.9.2

= v1.1.0 (2018-1-23) =


Bug Fixes

* Remove third-party plugin update checker
* Improve packed ZIP file
* Update translations
* Update meta properties

Features

* Add fetch_origin_interval option to Speed Kit
* Improve README

= v1.0.16 (2017-11-23) =


Bug Fixes

* Exclude some generated files from release ZIP

= v1.0.15 (2017-11-23) =


Bug Fixes

* Fix ZIP dist dir in release

= v1.0.14 (2017-11-23) =


Bug Fixes

* Fix ZIP command in release

= v1.0.13 (2017-11-23) =


Bug Fixes

* Fix pushd command in release

= v1.0.12 (2017-11-23) =


Features

* Improve released ZIP archives
* Update the README

= v1.0.11 (2017-11-16) =


Bug Fixes

* Fix bug with wish lists in WooCommerce
* Fix bug which manipulates the options
* Fix bug in SimplyStaticOptionsAdapter
* Immunize against all exceptions in revalidation trigger
* Do safely logout on exception

Features

* Add helpful heading in hosting
* Support new snippet

= v1.0.10 (2017-10-16) =


Bug Fixes

* Revalidate user if API access fails

= v1.0.9 (2017-10-10) =


Features

* Advanced WooCommerce handling: automatically parse blacklist from WooCommerce shops
* Update Speed Kit snippet

= v1.0.8 (2017-10-5) =


Features

* Add hosting tab checkbox

= v1.0.7 (2017-9-19) =


Bug Fixes

* Change ServiceWorker path to "/sw.js"

Features

* Show note when plugin is outdated

= v1.0.6 (2017-9-18) =


Bug Fixes

* Update only to version 1.X.X
* Update Composer hash
* Remove Hosting from help
* Fix content types, add track type
* Refactor config builder
* Fix preload ServiceWorker destination
* Improve error handling in upload_archive

Improvements

* Improve labels of Speed Kit options
* Remove Hosting from view
* Replace content types with checkboxes
* Add bypass cache by cookie configuration

= v1.0.5 (2017-9-7) =


Bug Fixes

* Fix default blacklist
* Fix empty lines in lists
* Fix line breaks in list views
* Fix ServiceWorker URL

Improvements

* Apply new ServiceWorker rules API
* Update the snippet

= v1.0.4 (2017-9-5) =


Bug Fixes

* Fix configuration parameters
* Map dev ServiceWorker from host to client
* Fix getting started design

Features

* Add max staleness and app domain settings

= v1.0.3 (2017-8-17) =


Bug Fixes

* Stop calling identity on every request
* Fix line breaks when showing settings
* Revalidate Speed Kit when its settings are changed
* Stop sending Service Worker with PHP

Features

* Add an example for white and blacklist in Speed Kit settings
* Add statistics to getting started

= v1.0.2 (2017-8-17) =


Bug Fixes

* Optimize frontend code
* Do not send Bloom filter preload anymore
* Send rel=serviceworker header
* Deactivate ServiceWorker manually in Admin
* Send revalidation asynchronously

Other

* Update README.txt metadata

= v1.0.1-alpha (2017-8-16) =


First Release

