=== Solidres - Hotel booking plugin for WordPress ===
Contributors: solidres
Donate link: http://www.solidres.com/
Tags: booking, booking system, hotel, reservation, online booking, online reservation, B&B, hotel booking, reservation system, hospitality, reserve
Requires at least: 4.0
Tested up to: 4.8
Stable tag: 0.8.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Solidres is a hotel booking plugin for WordPress that helps you create your online booking business easily and beautifully.

== Description ==

[Solidres](http://www.solidres.com) can transform your beloved WordPress website into a hotel booking website. It is time to empower your hospitality business with your own hotel booking website, don't just rely on OTA websites.

= A native WordPress plugin =

Solidres is built as a native WordPress plugin from the ground up (no bridge, no hack, no strange installation procedure). As a user you will find it easy to use with familiar user interface and workflow. As a developer, understanding our code is convenient because we use and extend a lot of WordPress core functions.

= Features =

For more details and screenshot you can visit our [Feature Highlights page](http://www.solidres.com/features-highlights).

* Support booking per night or per day
* One page AJAX + well organized reservation form: it is one of our strongest feature, it makes booking a nice experience for your guests, this is very important factor for booking because most of the guests will stop booking if they find it hard to understand the user interface or having to fill a horrible form with too many fields.
* Flexible tariff configuration
* Availability calendar
* Easy media management with drag & drop re-ordering
* Multiple currencies
* Custom fields
* Coupon
* Tax supports
* Deposit support: configure whether to accept deposit and configure deposit amount. Deposit can be fixed amount or percentage of booking cost or per stay length (for example charging first 2 nights's cost as deposit)
* Extra items can be configured as mandatory and charged per booking or per room.
* Google Map integrated: allow drag and drop on map to find your location visually.
* Built-in Simple gallery
* Built-in payment methods: Pay Later and Bank Wire
* Responsive layout
* Responsive email templates
* Export reservation as CSV
* Faceboook open graph support
* Back end reservation creation or amend for staff
* Single use supplement
* Support multilingual with qTranslatex
* Live reservation unread count
* Ability to send reservation notification emails to multiple email addresses
* Support Bootstrap 2 and Bootstrap 3

For reporting bug or requesting feature or any questions you may have when using Solidres, please contact us via our website.

== Installation ==

= Minimum requirements =

Check our [online documentation](http://www.solidres.com/documentation) for full instruction

* WordPress 4.0 or greater
* PHP version 5.3.10 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Log in to your WordPress dashboard, then go to the Plugins menu and click on Add new.

In the search field, enter "Solidres" and click Search Plugins. Once Solidres plugin is found and displayed in the screen, simply clicking "Install now".

= Manual installation =

1. Upload folder `solidres` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Upgrade Notice ==

* There is no upgrade notice for this initial version

== Frequently Asked Questions ==

= Where can I ask questions and talk to other users? =

If you have any issues, you can ask for help on the [Solidres Community Forum](http://www.solidres.com/forum/index)

== Screenshots ==

1. Reservation asset single view
2. Room type view
3. Selecting a room to book
4. Entering guest information in reservation step 2
5. Confirm a reservation
6. Room type edit screen in back end
7. Sorting media via drag and drop


== Changelog ==

= 0.8.5 =

* Fix min length of stay 0 value

= 0.8.4 =

* Add diagonal line for check in and check out in front end availability calendar (asset view)
* Add more currencies into sample data (apply for new installation)
* Add notification about complex tariff usage in room type edit screen
* Fix room type ordering in front end
* Fix overlapping issue with theme that has sticky menu or sticky header
* Add missing country's state in address
* Fix issue that child age not sent correctly in backend reservation creation
* Fix singe supplement with rate per room per stay
* Fix issue that break reservation creation in front end in some scenarios
* Fix pagination issue in new installation

= 0.8.3 =

* Fix PHP Fatal related to extra per person types
* Fix typo in email text
* Fix issue with booking per day

= 0.8.2 =

* Fix tariff saving with decimals number
* Add locale into date displaying in Check Availability widget
* Fix PHP 5.3 compatibility issue

= 0.8.1 =

* Fix social network icon links
* Fix PHP Fatal related to update checking
* Sort tariffs in front end by valid_from date

= 0.8.0 =

* Add support for Bootstrap 3
* Add check update feature
* Fix time zone issue
* Fix discount issue not filter by asset
* Fix sending reservation email with PayPal

= 0.7.0 =

* Add option to configure whether deposit take extra cost into calculation or not
* Improve responsiveness for front end
* Improve translation, fix missing strings
* Fix localization issue with date picker and JS
* Fix issue when standard tariff does not take min day book in advance into consideration
* Fix broken email sending with WP 4.6
* Fix reservation amending does not take new check in and check out dates
* Fix various PHP notice message
* Fix broken social network icon in email template

= 0.6.0 =

* Support sending notification emails to multiple email addresses
* Improve responsiveness in backend list view and form view
* Add option to hide the number of available rooms
* Add option to show or hide unavailable rooms
* Add support for Google Analytics plugin
* Fix regression that submit button in reservation step 3 is not disabled by default
* Fix uninstall issue that did not remove session table
* Fix broken quick booking in statistics dashboard
* Fix several i18n support

= 0.5.0 =

* Add backend reservation creation/amending
* Refactor session handler
* Add support for Google Map API Key
* Refine reservation edit screen
* Improve i18n support
* Fix incorrect booking type value

= 0.4.0 =

* Support multilingual
* Support more date format
* Change font fields in Solidres Options - Invoice to select box

= 0.3.0 =

* Add better support for apartment/villas booking when there is only 01 quantity, now you can replace the quantity dropdown with a single button, you can also hide the room form so that guest can book directly too.
* Add support for front end log in box (for User plugin)
* Add live unread reservation count
* Add inline change state for reservation listing view
* Add option to show/hide the front end asset's custom fields (Facilities, Policies)
* Add calendar icon for front end date picker fields
* Add check to see whether your server has full support for Paypal new requirement
* Add check availability form for asset view
* Increase the size of map modal in front end
* Fix reservation start over link
* Fix issue that break WP post's feature image functionality
* Fix reservation ordering in backend
* Fix default values for date format
* Fix issue when unpublished country still showed in front end
* Fix issue when date picker does not handle min/max night constraint properly
* Fix min people and max people constraint in room type form
* Fix issue with currency exchange
* Fix loading tariffs according to customer group (for Complex Tariff plugin)

= 0.2.1 =
* Add theme override feature for Check Availability and Currency widgets
* Add horizontal layout to Check Availability widget
* Fix Paypal redirection error
* Fix Geocomplete loading in HTTPS pages
* Fix asset custom field loading when hiding asset's description
* Fix room occupation with reservation status Checked In
* Fix room type show/hide options for adult,child,name,smoking fields
* Fix incorrect read more tag
* Fix adult number issue for room reservation
* Fix single supplement issue

= 0.2.0 =
* The first stable release which has many new features and improvements

= 0.1.0 =
* Initial release