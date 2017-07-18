=== bbPress API ===
Contributors: casiepa
Donate link: http://casier.eu/wp-dev/
Tags: bbpress,api,rest,rest api
Requires at least: 4.7
Tested up to: 4.8
Stable tag: 1.0.13
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A first attempt for a bbPress API.

== Description ==
A first attempt for a bbPress API.

**WARNING This API will show all forums, topics, replies that bbPress has access to. If you have any extra plugin to restrict bbPress content, please double and triple check that everything works correctly.**

Current routes for READING (GET):

* /wp-json/bbp-api/v1/forums/       (list all forums)
* /wp-json/bbp-api/v1/forums/*id*   (includes latest topics and subforums)
* /wp-json/bbp-api/v1/topics/       (latest topics from all forums)
* /wp-json/bbp-api/v1/topics/*id*   (includes latest replies)
* /wp-json/bbp-api/v1/replies/*id*  (show one reply)
* /wp-json/bbp-api/v1/topic-tags/
* /wp-json/bbp-api/v1/stats/

Parameters for /forums/*id* and /topics/*id* (following https://developer.wordpress.org/rest-api/using-the-rest-api/pagination/#pagination-parameters )

* per_page  (records per page)
* page      (page number)

Parameter for /topics/*id* (following https://developer.wordpress.org/rest-api/using-the-rest-api/linking-and-embedding/#embedding )

* _embed    (showing content for replies)

Current routes for WRITING (POST):

* /wp-json/bbp-api/v1/forums/*id*   (create a new topic in this forum)
* /wp-json/bbp-api/v1/topics/*id*   (create a reply to this topic ID)
* /wp-json/bbp-api/v1/replies/*id*  (create a reply to this reply ID)

Follow development on https://github.com/ePascalC/bbp-API/ !

Many thanks and credits to:

* Daniel Turton (mistertwo) for the topics and replies POST functions
* Tony Korologos (@tkserver) for his input and testing with his app

Consider also the following plugins:

* bbP Toolkit
* bbP Move Topics

== Installation ==
Option 1:

1. On your dashboard, go to Plugins > Add new
1. search for *bbPress API*
1. install and activate the plugin

Option 2:

1. Unzip the contents to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.

== Frequently Asked Questions ==
= Can I make feature requests =
Of course ! Just post something on the support tab

= I love your tool =
Thanks. Please leave a review or donate 1 or 2 EUR/USD for a coffee.

== Changelog ==
= 1.0.13 =
* Fixing https://github.com/ePascalC/bbp-API/issues/22

= 1.0.12 =
* Add previous page (=0) even if there is none
* Add current page
 
= 1.0.11 =
* Latest topics pagination
* Previous page, total pages and total items added for every loop
* Fix issue where next page would not be specified 

= 1.0.10 =
* Latest topics from all visible forums

= 1.0.9 =
* If a forum is a category, do not allow a new topic on POST
* Fix reply to reply POST issue
* Return an ARRAY after a POST request with all necessary elements.

= 1.0.8 =
* Fix ID for POST requests

= 1.0.6 =
* Add forum ID and title to topic
* Add forum ID, title and topic ID,title to reply
* Add a title to the reply to post and reply to topic

= 1.0.5 =
* Rewriting of all POST actions
* Validation of reply, topic and forum IDs

= 1.0.4 =
* Adding parameters
* re-Adding the orginal /forums /topics /replies routes

= 1.0.3 =
* Switching to real forum- topic- and reply-slugs
* Several fixes

= 1.0.2 =
* Added the subforums to /forums/*id*
* Added topic-tags

= 1.0.1 =
* Added the stats route

= 1.0.0 =
* Initial release with basic routes