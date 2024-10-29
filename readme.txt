=== Annotation Plugin ===
Contributors: Radii
Author URI: http://vandeft.com/
Plugin URI: http://vandeft.com
Tags: posts, post, page, pages, annotate, annotation
Requires at least: 2.7.1
Tested up to: 2.7.1
Stable tag: 0.3

Creates an annotation link to take out "unneeded" text.

== Description ==

BBCode Annotator helps you annotate text so that you don't need to go through the hassle of typing out the HTML code. For example, if you were to digress from a topic in your post, you can type out an annote and allow the viewers to stay in synch of your topic but at the same time see what extra (and sometimes unneeded) information you added. If you have any annotes in your post, the text will also be added at the end of your post.

Format:
`[annotate="*hover_over_text*" show="*text_seen_in_post*"]*full_description_of_annote*[/annotate]`
`[annote="*hover_over_text*" show="*text_seen_in_post*"]`

The *hover_over_text* is REQUIRED! The other two aren't.
If the *text_seen_in_post* isn't depicted, it will use the annote ID (it is automatically generated ID).
If the *full_description_of_annote* isn't depicted, it will use the *hover_over_text*.

== Installation ==

1. Upload the folder `bb-code` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use it in your post.

== Frequently Asked Questions ==

1. <strong>I embedded [annote] into my [annotate]! Now my post is messed up!</strong><br />
You are not supposed to have notes within your notes; they should be separate from one another. [annote] was added to simplify annotations if you didn't want to add a full description for it.

1. <strong>I tried to add in double quotes, ", into my annotate/annote but it cuts off my text! What's wrong?</strong><br />
Annotate/annote checks for the first and second occurrance of double quote. Please refrain from using double quotes these two quotes and instead use two single quotes. Two single quotes are automatically converted to double quotes.

Ask your own question at http://forum.vandeft.com/list.php?12

== Screenshots ==

1. The highlighted text is an example of how `[annote]` should be formatted.
2. Annotations in action.