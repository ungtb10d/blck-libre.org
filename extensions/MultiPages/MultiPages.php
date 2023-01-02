<?php
##########################################################################
#    Copyright (C) 2009  PM Gostelow
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
#    along with this script.  If not, see <http://www.gnu.org/licenses/>.
##########################################################################

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/MultiPages/MultiPages.php" );
EOT;
        exit( 1 );
}

// DO NOT EDIT CLASS!
// The purpose is to provide a consistent api to extensions
// When the credit array changes from under us, update this class
abstract
class spExtAbstractCredits
{
	const cdNAME          = 'name';           // for package name
	const cdURL           = 'url';            // for site link
	const cdAUTHOR        = 'author';         // for author name/s
	const cdVERSION       = 'version';        // for package release
	const cdNOTE          = 'description';    // for short note
  const cdNOTEMSG       = 'descriptionmsg'; // for long note
	const URLPREFIX       =                   // for link name
		"http://www.mediawiki.org/wiki/Extension:";
	protected
	static $url           = '';               // for our site page
	// always override, always call
	abstract static function register();      // for LocalSettings.php

	// seldom override, always call
	// updates and hooks credits into the extension
	static
	function register_credits( $hook, $pkgCredits, array $pages = array() )
	{
		global $wgExtensionCredits;
		if (empty(self::$url))
			self::$url = self::URLPREFIX . $pkgCredits[self::cdNAME];
		$pkgCredits[self::cdURL] = self::$url;
		foreach( $pages as $key => $value )
			$pkgCredits[$key] = $value;
		$wgExtensionCredits[$hook][] = $pkgCredits;
	}
};

// Support special page files
// The purpose is to provide a consistent api to extensions
// When the file handling changes, update this class
abstract
class spExtAbstractPages extends spExtAbstractCredits
{
	const sxBODY          = '_body';          // for page file name
	const sxMSG           = '.i18n';          // for language file name
	const sxALIAS         = '.alias';         // for alias file name
	const sxFormat        = "%s/%s%s.php";    //file format
	static $ext           = array(            // for generating file names
		self::sxBODY, self::sxMSG, self::sxALIAS );
	static $hkCredit      = 'specialpage';    // for special page hook
	static $dir           = '';               // for our directory

	// seldom override, always call
	protected static
	function register_files( $pkgName, $page )
	{
		global $wgAutoloadClasses, $wgExtensionMessagesFiles,
			$wgExtensionAliasesFiles;

//		echo( "registering :".$page."<br>" );
		if ( empty( self::$dir ))
			self::$dir = dirname( __FILE__ );
		$wgAutoloadClasses[$page] =
			sprintf(self::sxFormat, self::$dir, $page, self::$ext[0] );
		$wgExtensionMessagesFiles[$page] =
			sprintf(self::sxFormat, self::$dir, $pkgName, self::$ext[1] );
		$wgExtensionAliasesFiles[$page] =
			sprintf(self::sxFormat, self::$dir, $pkgName, self::$ext[2] );
	}
};

// Package registration class
class spExtPages extends spExtAbstractPages
{
	const AUTHOR          = 'Peter Gostelow'; // to display name
	const PACKAGE         = 'MultiPages';     // for file names
	const VERSION         = '0.0.4';          // to display release
	const mcHOOK          = 'hook';
	const mcCREDIT        = 'credit';
	static $page          = array(            // for page specific credits
		'MultiMove'   => array(
			self::mcHOOK => array(
				'specialpage' ),
			self::mcCREDIT => array(                 // override $credits info
			self::cdNAME     => 'MultiMove',      // the page class and notes
			self::cdNOTE     => 'Move page and all its sub pages',
			self::cdNOTEMSG  => 'Move or rename a page and all its sub pages.')),
		'MultiDelete' => array(
			self::mcHOOK => array(
				'specialpage' ),
			self::mcCREDIT => array(
			self::cdNAME     => 'MultiDelete',
			self::cdNOTE     => 'Delete page and all its sub pages',
			self::cdNOTEMSG  => 'Delete a page and all its sub pages.')),
		// add more pages here ...
		);
	static $credits       = array(            // for default credits
			self::cdAUTHOR   => self::AUTHOR,     // default author
			self::cdNAME     => self::PACKAGE,    // for page name
			self::cdURL      => '',               // for site link
			self::cdVERSION  => self::VERSION,    // default page version
			self::cdNOTE     => 'Error',          // for page NOTE
			self::cdNOTEMSG  => 'No pages used'); // for page NOTEMSG

	// The class should never be instatiated
	private
	function __construct(){	parent::__construct(); }

	// seldom override, always call
	// add all the hooks and credit info
	static
	function register()
	{
		global $wgSpecialPages, $wgHooks, $mpSettings;

		// $page keys are our special page class names
		foreach( array_keys(self::$page) as $func )
		{
			self::$hkCredit = self::$page[$func][self::mcHOOK][0];
			self::register_credits(
				self::$hkCredit, self::$credits, self::$page[$func][self::mcCREDIT]);
			self::register_files( self::PACKAGE, $func );
			$wgSpecialPages[$func] = $func;
			if ( 1 < count( self::$page[$func]))
			foreach ( array_slice( self::$page[$func][self::mcHOOK], 1 ) as $hkOther )
			{
//				echo "<br><br><br>Hook=".$func." value = ".$hkOther;
				if ( $mpSettings['mp'.$hkOther] )
					$wgHooks[$hkOther][] = $func. "::" . $hkOther;
			}
		}
	}
};

// Called by LocalSettings.php
spExtPages::register();
?>