<?php
##########################################################################
#    MultiPages.i18.php Copyright (C) 2009  PM Gostelow
#
#    This script is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This script is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
##########################################################################
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$messages = array();
 
/* *** English *** */
$messages['en'] = array(
	'multidelete' => 'Multiple Page delete',
	'multidelete-desc' => "Use the form below to delete a page and its subpages in all namespaces. This is a restructuring tool and should not be used for routine deletions. It is most useful for pages that follow a strict pathname convention. It will not delete protected pages. It is your responsibility to ensure pages still link correctly after a multi-delete.
foobar
====Redirect checkbox====
The Redirect checkbox allows you to delete <i>redirect</i> or <i>normal</i> pages, but not both. To delete redirect pages, '''check''' the '''Redirect''' checkbox. To delete non-redirect pages, '''uncheck''' the '''Redirect''' checkbox.

<big>'''Warning:'''</big> This can have catastrophic consequences and should only be attempted by experienced users. Check help on Pathnames and Namespaces to make sure you understand the affects.
",

	'multimove' => 'Multiple Page Move',
	'multimove-desc' => "Use the form below to move a page and all matching subpages in the same namespace. You may also move similar pages in other namespaces ''including'' the Category namespace. This is mostly useful when managing educational material, such as modules, where the schema, title, or chapters need changing. It is ''not'' useful for single pages or pages that do not follow a strict pathname convention; use the page's Move link instead.

Protected pages and their subpages will be ignored and not moved. This means subpages will be moved until a protected page is reached, leaving it and all its subpages with the current name. It is your responsibility to ensure pages still link correctly after a multi-move.

<big>'''Warning:'''</big> This can have catastrophic consequences and should only be attempted by experienced users. Check help on Pathnames and Namespaces to make sure you understand the affects.
",

	'multiform' => 'Create Page',
	'multiform-desc' => 'Complete the form to generate the page',

	'loginpage' => 'Create Account',
	'loginpage-desc' => "You may only create an account if you have been invited to join a group. The group member will enable the site to register you with the group. If you cannot create an account, please contact the group member.",
	'logingroup' => 'LP Group',

	'multipagesettings' => 'Multi page Configure',
	'multipagesettings-desc' => "Setup the multipage extension according to your needs. This page enables admins to dynamically change the extension settings without editing the LocalSettings.php file. The actual settings are stored in the extension directory and it must give mediawiki read and write access.

You may prefer to disable this special page and change the settings manually in <i>MultiPagesSettings.php</i>",
);
 

?>