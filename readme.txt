=== WP-Posts to Image Plugin ===
Contributors: utdemir
Tags: last post, bbcode, forum, sign, banner
Requires at least: 2.9
Tested up to: 3.0
Stable tag: 0.5.9

Let's you show your blog's last posts on a forum signature or stg. uses bbcode.

== Description ==

With this plugin, you will get two links; One link for the image, and one link for the redirect page. Then you can use these links in a forum signature like this:

[url=example.com/wp-content/plugins/p2i/1.php][img]example.com/wp-content/plugins/p2i/1.png[/img][/url]

It also supports shortening the url's with "is.gd".

== Installation ==

1. Upload the contents of the archive to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Do "chmod 777" to "/wp-content/plugins/wp-posts-to-image-plugin"
4. You can get the links from "Posts to Image" in the menu(!!!You should click "Create Images" first!!!).
5. Use it like "[url=example.com/wp-content/plugins/p2i/1.php][img]example.com/wp-content/plugins/p2i/1.php[/img][/url]" in wherever you want.


== Changelog ==
= 0.6 =
Using load_plugin_textdomain instead of load_textdomain for compatiblity. Thanks to Li-An :).
Very very minor bugfixes
= 0.5.9 =
Added .htaccess support for performance improvement.
= 0.5.8 =
Added French Language(I didn't added it to svn, now i did.).
Show a link to options page in the alert message.
= 0.5.7 =
Updated translations.
= 0.5.6 =
New: Show warning when plugin is not ready.
Added: French language  
= 0.5.5 =
New: Localization support. (pot file in locale directory)
Added: Turkish Language  
= 0.5.4 =
New: Uninstall button for clean database!
= 0.5.3 =
Deleted deactivation hook. Now you can re-enable the plugin without losing your configuration
= 0.5.2 =
Fixed: "Headers already sent" error message
= 0.5.1 =
Fixed: The images was not re-creating when publishing posts.
= 0.5 =
It's now using GPL license.
= 0.4 =
Added a warning when the plugin directory is not writable.
= 0.3 =
NEW: You can change the size, font, colors from the options menu.
NEW: Showing preview in options menu now.
= 0.2 =
Bugfix: Links in Options menu is now working.
= 0.1 =
First release. Yet, it's not even alpha, just like a draft.

