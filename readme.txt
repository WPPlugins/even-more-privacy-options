=== Even More Privacy Options ===
Contributors: zaantar
Tags: superadmin, multisite, privacy, private, feed, feed key
Donate link: http://zaantar.eu/index.php?page=Donate
Requires at least: 3.3
Tested up to: 3.3.1
Stable tag: 1.0

Modifies behaviour of More Privacy Options and Private Feed Keys plugins in multiple ways.

== Description ==

This plug-in is being developed for a specific website, but it may be useful for someone else, too. It is supposed to work in combination with [More Privacy Options](http://wordpress.org/extend/plugins/more-privacy-options/) 3.2.1.1 and [Private Feed Keys](http://wordpress.org/extend/plugins/private-feed-keys/) 1.0 by modifying their behaviour.

Features:

* for blogs available only to site admins/members/network users: possibility to set different page for non-authorised visitors
* this page (if set) will be available to everyone, of course
* remove login form messages
* supports users' feed keys from the [Members Only](http://wordpress.org/extend/plugins/members-only/) plugin
* show each user's private feed key for actual blog on their profile page (if the blog isn't public)
* another privacy option: custom privacy management, only activate private feed key support (usable for example with [User Access Manager]((http://wordpress.org/extend/plugins/user-access-manager/))
* if [WordPress Logging Service](http://wordpress.org/extend/plugins/wordpress-logging-service/) and [Superadmin Helper](http://wordpress.org/extend/plugins/superadmin-helper/) are present, logs changes instead of sending notification e-mails to network admin.

Prerequisities:

* there are few neccessary modifications of the [More Privacy Options](http://wordpress.org/extend/plugins/more-privacy-options/) plugin (version 3.2.1.1) you have to perform before the plugin starts working. See Usage section.

In the future there probably will be a network settings page and some options (if someone requests that)

Developed for private use, but has perspective for more extensive usage. I can't guarantee any support in the future nor further development, but it is to be expected. Kindly inform me about bugs, if you find any, or propose new features: zaantar@zaantar.eu.

See Usage and FAQ for more information.

== Frequently Asked Questions ==

None asked yet.

== Usage ==

Before you start using the plugin, there are (very) few modification you have to do in the More Privacy Options plugin (file ds_wp3_private_blog.php):

1. replace 
	wp_login_url()
with 
	apply_filters( 'ds_wp3_private_blog_login_url', wp_login_url() )
in "ds_users_authenticator", "ds_members_authenticator" and "ds_admins_authenticator" functions if you want to use the custom redirection for unauthorised users.

2. replace "mail" function by "wp_mail" in "ds_mail_super_admin" function (nicer, optional)

And that's it!

== Changelog ==

= 1.0 =
* first version
* submitted to wordpress.org
