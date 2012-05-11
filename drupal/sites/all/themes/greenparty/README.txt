$Id: README.txt,v 1.4 2009/07/13 23:52:58 andregriffin Exp $

Drupal 6 Framework theme
Created by Andre Griffin > user andregriffin


Framework is a blank canvas for theme developers. Use Framework as a user friendly starting point to help facilitate your theme development.

Features

		* Framework gives general placement and formatting to basic Drupal elements
		* Supports one, two, and three-column layouts
		* Set to a 24 column grid of 950px
		* CSS file is highly organized, including a table of contents, section flags, alphabetical properties, etc.
		* Includes a CSS reset and a list of CSS utility classes for easy content formatting
		* Em unit text sizing with vertical rhythm
		* Search in sidebar (as a block) and header (as a theme configuration option)
		* Included support for Dynamic Persistent Menu
		* Quick block and view editing links
		* Clean and simplified code, file structure, and administration section
		* Works nicely in mobile browsers
		* W3C valid CSS 2.1 / XHTML 1.1
		* Verified and tested with Firefox 3, Firefox 2, IE7, IE6, Safari 4, Chrome


Framework is not intended to be everything to everyone. It is built with simplicity and ease of modification in mind.

Although it is not necessarily intended, Framework can be used as is if you so choose.

Framework is actively developed and supported on my own time. If you would like to say thanks, please consider donating via:
https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1532730

Development sites:
http://d6.andregriffin.com
http://d5.andregriffin.com

-------------------------------
>>> INSTALL to sites/all/themes


>>> CONFIGURATION NOTES:

To add regions, see: http://drupal.org/node/242107#comment-798428

To enable current node to show in the breadcrumb trail, remove comment slashes on line 41 of template.php

To add IE6 stylesheet, create a file in theme directory called fix-ie6.css.
Place this under IE7 stylesheet in page.tpl: <!--[if lt IE 7]><?php print phptemplate_get_ie6_styles(); ?><![endif]--><!--If Less Than (lt) IE 7-->


>>> SUPPORT

If you have questions or problems, check the issue list before submitting a new issue: 
http://drupal.org/project/issues/framework

For general support, please refer to:
http://drupal.org/support

To contact me directly, please visit: 
http://drupal.org/user/78099/contact