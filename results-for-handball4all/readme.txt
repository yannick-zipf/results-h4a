=== Results for Handball4All ===
Contributors: yannickzipf
Tags: sports, handball, h4a, results, timetable, standing
Tested up to: 6.4
Requires PHP: 7.0
Requires at least: 5.0
Stable tag: 1.3.2
License: GPLv2 or later

Show timetables with results and standings of handball teams and leagues provided by Handball4All.de.

== Description ==
Easily integrate the data provided by Handball4All on your website. You will create shortcodes for timetables, standings and next matches and configure them individually. An intelligent caching algorithm ensures that the data is only loaded when necessary.

= ✨ Features =
* Integrate shortcodes on any pages or blog posts (also custom post types).
* Activate the intelligent caching mechanism to decrease http requests without bloating your server load.
* German translation included.
* Create standings for leagues.
* Hide columns in standing table.
* Create timetables for leagues and teams.
* Highlight your team in standings and timetables.
* Create a list of next matches for one or more teams.
* Activate or deactivate individual shortcodes without disturbing your remaining content.
* Remove default CSS if you want to style the items yourself.

= Credits =
[Beach Vectors by Vecteezy](https://www.vecteezy.com/free-vector/beach)

== Installation ==
Installing the plugin is easy. Just follow one of the following methods:

= Install Results for Handball4All from within Wordpress =

1. Visit the plugins page within your dashboard and select ‘Add New’
2. Search for \"Results for Handball4All\"
3. Activate Results for Handball4All from your Plugins page
4. You\'re done!

= Install Results for Handball4All Manually =

1. From the dashboard of your site, navigate to Plugins --> Add New.
2. Select the Upload option and hit \"Choose File.\"
3. When the popup appears select the results-h4a-x.x.zip file from your desktop. (The \'x.x\' will change depending on the current version number).
4. Follow the on-screen instructions and wait as the upload completes.
5. When it\'s finished, activate the plugin via the prompt. A message will show confirming activation was successful.

That\'s it! 

== Frequently Asked Questions ==
= Where do I get the League ID oder Team ID or Club ID From? =

Visit www.handball4all.de and navigate via the links on the page to the site that shows the data you want to integrate into WordPress. 
Examples:
* Timetable for League & Standing: https://www.handball4all.de/home/portal/bhv#/league?ogId=35&lId=67731
* Timetable for Team: https://www.handball4all.de/home/portal/bhv#/league?ogId=35&lId=67731&tId=720111

From the URL you can see the corresponding IDs:
* lId = League ID (e.g. 67731)
* tId = Team ID (e.g. 720111)

= How do I get the created standing or timetable to display on my website? =

Just copy the shortcode (e.g. [rh4a-timetable 1]) and insert it on any page or post as a shortcode.

== Screenshots ==
1. Frontend: Standing with team highlighted (German) (Theme: Twenty-Twenty-One)
2. Frontend: Timetable of type league with team highlighted (German) (Theme: Twenty-Twenty-One)
3. Frontend: Next matches for two teams (German) (Theme: Twenty-Twenty-One)
4. Backend: Standings overview (German)
5. Backend: Standing edit page (German)
6. Backend: Timetables overview (German)
7. Backend: Timetable edit page (German)
8. Backend: General Options (German)

== Changelog ==
= [1.3.2] 2023-11-26 =
* Docs: Tested up to WordPress 6.4
= [1.3.1] 2023-05-12 =
* Fix: Enhance error handling for empty results from H4A servers
* Fix: Correct item type property in status change link for next-matches
= [1.3.0] 2022-11-20 =
* Feature: Add 'Delete cache' button on settings page
* Feature: Highlight team name also for timetable with type team, not only type league
= [1.2.0] 2022-09-30 =
* Feature: Change permissions to allow editors instead of admins
= [1.1.1] 2022-09-28 =
* Fix: Delete all transients with DB query
* Fix: Enqueue dashicons on the frontend
* Version bump: Tested with 6.0.2
= [1.1.0] 2022-04-23 =
* Feature: Introduce next matches
* Feature: Change caching object to whole json response, not only dataList attribute
= [1.0.0] 2022-03-05 =
* Initial Version