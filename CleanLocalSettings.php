

<?php



define('MW_INSTALL_PATH','/var/www/w');

if( defined( 'MW_INSTALL_PATH' ) ) {
	$IP = MW_INSTALL_PATH;
} else {
	$IP = dirname( __FILE__ );
}

$path = array( $IP, "$IP/includes", "$IP/languages" );
set_include_path( implode( PATH_SEPARATOR, $path ) . PATH_SEPARATOR . get_include_path() );

require_once( "$IP/includes/DefaultSettings.php" );


require_once('extensions/Nuke/SpecialNuke.php');
$wgGroupPermissions['sysop']['nuke'] = true;


require_once("$IP/extensions/Renameuser/SpecialRenameuser.php");

require_once("$IP/extensions/UserMerge/UserMerge.php");
$wgGroupPermissions['bureaucrat']['usermerge'] = true;

require_once("$IP/extensions/Calendar/Calendar.php");
$wgExtraNamespaces[96] = "Group";
$wgExtraNamespaces[97] = "Group_talk";
$wgExtraNamespaces[98] = "Event";
$wgExtraNamespaces[99] = "Event_talk";
$wgExtraNamespaces[100] = "Calendars";
$wgExtraNamespaces[101] = "Calendars_talk";

$wgNamespacesToBeSearchedDefault[96] = true;
$wgNamespacesToBeSearchedDefault[98] = true;
$wgNamespacesToBeSearchedDefault[100] = true;
$wgEnableMWSuggest = true;

$wgNamespacesWithSubpages = array_fill(0, 200, true);

include_once("$IP/extensions/SemanticMediaWiki/includes/SMW_Settings.php");
$smwgNamespacesWithSemanticLinks[96] = true;
$smwgNamespacesWithSemanticLinks[98] = true;
$smwgNamespacesWithSemanticLinks[100] = true;
$smwgInlineErrors = false;
enableSemantics('libreplanet.org');

$sfgNamespaceIndex = 150;
include_once("$IP/extensions/SemanticForms/includes/SF_Settings.php");

require_once("$IP/extensions/Cite/Cite.php");

ini_set( 'memory_limit', '50M' );

$wgMaxShellMemory   = 512000;

if ( $wgCommandLineMode ) {
	if ( isset( $_SERVER ) && array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
		die( "This script must be run from the command line\n" );
	}
}

$wgSitename         = "LibrePlanet";

$wgScriptPath       = "/w";
$wgScript           = "/wiki";
$wgRedirectScript   = "$wgScriptPath/redirect.php";
$wgScriptExtension  = ".php";


$wgUsePathInfo = true;

$wgStylePath        = "$wgScriptPath/skins";
$wgStyleDirectory   = "$IP/skins";
$wgLogo             = "$wgStylePath/common/images/groups-logo.png";

$wgUploadPath       = "$wgScriptPath/images";
$wgUploadDirectory  = "$IP/images";


$wgCheckFileExtensions = false;
$wgStrictFileExtensions = false;
$wgVerifyMimeType = false;
$wgFileBlacklist = array();
$wgMimeTypeBlacklist= array();

$wgEnableEmail      = true;

$wgEmergencyContact = "sysadmin@gnu.org";
$wgPasswordSender = "sysadmin@gnu.org";

$wgEmailAuthentication = false;


$wgFileExtensions[] = 'svg';
$wgFileExtensions[] = 'pdf';
$wgAllowTitlesInSVG = true;

$wgDBtype           = "mysql";
$wgDBserver         = "localhost";
$wgDBname           = "mediawiki";
$wgDBuser           = "mediawiki";
$wgDBpassword       = "mUqL3juF";

$wgDBprefix         = "";

$wgDBTableOptions   = "ENGINE=InnoDB, DEFAULT CHARSET=binary";


$wgMainCacheType = CACHE_ACCEL;
$wgMemCachedServers = array();

$wgEnableUploads       = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgUseImageResize              = true;

$wgShellLocale = "en_US.utf8";


$wgUseTeX           = false;
$wgMathPath         = "{$wgUploadPath}/math";
$wgMathDirectory    = "{$wgUploadDirectory}/math";
$wgTmpDirectory     = "{$wgUploadDirectory}/tmp";

$wgLocalInterwiki   = $wgSitename;

$wgLanguageCode = "en";

$wgProxyKey = "60ec8ea333683be6e7d1146155a2e974de29e1cb05f310f911c765b7ef23503f";

$wgDefaultSkin = 'vector';

$wgEnableCreativeCommonsRdf = true;
$wgRightsText = "GNU Free Documentation License 1.3";
$wgRightsIcon = "${wgScriptPath}/skins/common/images/gnu-fdl.png";

$wgAllowDisplayTitle = true;

$wgDiff3 = "/usr/bin/diff3";


$wgCookieDomain = '.libreplanet.org';

$wgGroupPermissions['*']['edit'] = false;

$wgRestrictDisplayTitle = false;

$wgGroupPermissions['user']['delete'] = true;
$wgGroupPermissions['user']['bigdelete'] = true;
$wgGroupPermissions['user']['undelete'] = true;



require_once( "$IP/extensions/ConfirmEdit/ConfirmEdit.php" );
$wgGroupPermissions['*'            ]['skipcaptcha'] = false;
$wgGroupPermissions['user'         ]['skipcaptcha'] = false;
$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;

include_once( "$IP/includes/DatabaseFunctions.php" );
include( "$IP/extensions/bad-behavior/bad-behavior-mediawiki.php" );



require_once( "$IP/extensions/MultiPages/MultiPages.php" );


require_once( "$IP/extensions/SubPageList3/SubPageList3.php" );

$wgGroupPermissions['bureaucrat']['maintenance'] = true;
require_once("$IP/extensions/Maintenance/Maintenance.php");

$wgCacheEpoch = max( $wgCacheEpoch, gmdate( 'YmdHis', @filemtime( __FILE__ ) ) );


$wgShowExceptionDetails = true;

$wgEnableMWSuggest = true;

require_once( "$IP/extensions/ParserFunctions/ParserFunctions.php" );

require_once( "$IP/extensions/WikiCurl/WikiCurl.php" );
require_once( "$IP/extensions/CASAuth/CASAuth.php" );

require_once( "$IP/extensions/MagicNoCache.php" );

$wgFileExtensions[] = 'zip';






wfLoadSkin( 'CologneBlue' );
wfLoadSkin( 'Modern' );
wfLoadSkin( 'MonoBook' );
wfLoadSkin( 'Vector' );

$wgLogo            = "$wgStylePath/logo.png";
$wgEnableUploads  = true;
$wgUseImageMagick = false;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgTmpDirectory = "$IP/images/temp";

?>
