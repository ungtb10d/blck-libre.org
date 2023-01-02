<?php
##########################################################################
#    SpecialClass.php Copyright (C) 2009  PM Gostelow
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
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

// This is an abstract class for special page classes. It is ideal
// for special pages that follow a view, preview, and save cycle,
// and view results in a table.


// Add your own consts and mExtraStr strings in extended classes
abstract
class SpecialClassError
{
	// CONSTANTS: ERROR NUMBER
	const mcEOK            = 0;            // for no error index
	const mcEDATA          = 1;            // for invalid data index
	const mcEFOUND         = 2;            // for empty data index
	const mcEINPUT         = 3;            // for input error index

	// VARIABLES: PROTECTED
	protected $mErrorStr   = array(        // for message text
		self::mcEOK    => '',
		self::mcEDATA  => 'No data meets form criteria.',
		self::mcEFOUND => 'No pages found, perhaps a spelling error?',
		self::mcEINPUT => 'Invalid input, please try again.'
	);
	protected $mExtraStr   = array();      // for adding messages
	protected $mCount      = 0;            // for index checking

	// VARIABLES: PRIVATE
	private $mErrorMsg     = array();      // for error messages

	// FUNCTIONS: CONSTRUCTOR / DESTRUCTOR
	function SpecialClassError()
	{
		$this->addMessages();
		foreach ( $this->mExtraStr as $key => &$value )
			$this->mErrorStr[$key] = $value;
		$this->mCount = count( $this->mErrorStr );
	}

	// FUNCTIONS: ABSTRACT

	// always override, never call
	// add array to mExtraStr
	abstract protected
	function addMessages();

	// FUNCTIONS: PUBLIC

	// seldom override, always call
	// adds an error to mErrorMsg
	public
	function addError( $number )
	{
		if ( self::mcEOK < $number && $this->mCount >= $number )
			$this->mErrorMsg[] = $this->mErrorStr[ $number ];
		else
			$this->mErrorMsg[] = 'Invalid number '.$number.' for addError';
	}

	public
	function eData()
	{
		$this->addError( self::mcEDATA );
	}

	public
	function eFound()
	{
		$this->addError( self::mcEFOUND );
	}

	public
	function eInput()
	{
		$this->addError( self::mcEINPUT );
	}

	// never override, always call
	// deletes all error messages
	public
	function clear()
	{
		$this->mErrorMsg = array();
	}

	// seldom override, always call
	// outputs error messages as html code
	public
	function error()
	{
		global $wgOut;

		$wgOut->addHTML( '<p style="font-size:large;font-weight:800;text-align:center">'
			.implode( "<br>", $this->mErrorMsg ).'</p>
		');
	}
};


abstract 
class SpecialClass extends SpecialPage
{
	// CONSTANTS: STATE
	const mcView           = 'wpView';       // for view button
	const mcPreview        = 'wpPreview';  // for preview button
	const mcSave           = 'wpSave';     // for save button
	// CONSTANTS: SQL
	const mcSqlTable       = 'table';      // for sql tables
	const mcSqlField       = 'field';      // for sql fields
	const mcSqlCond        = 'cond';       // for sql conditions
	const mcSqlOpt         = 'opt';        // for sql extras
	// CONSTANTS: HTML
	const mcSubmitName     = 'action';     // for button request
	// CONSTANTS: MISC
	const mcMain          = 'Article';
//	const mcHookMsg       = 'SpecialDeletepageAfterDelete';
	const mcUrlPrefix     = '../index.php/';
	// CONSTANTS: HTML
	const mcCheck         = 'on';
	const mcActive        = 'checked';


	// VARIABLES: PROTECTED
	protected $mPageTitle  = 'specialclass'; // for special page title
	protected $mPageText   = '';           // for special page description
	protected $mValidInput = false;        // for request validation
	protected $mState      = '';           // for selecting pages
	protected $mDataCount  = 0;            // for sql data index
	protected $mData       = array();      // for sql data
	protected $mSqlSelect  = array();      // for sql select queries
	protected $mSqlUpdate  = array();      // for sql updatie queries
	protected $mCaption    = '';           // for html table caption
	protected $mBttnTab;                   // for form buttons
	protected $mError;                     // for extended SpecialClassError
	// VARIABLES: PRIVATE
	private   $mSubmitAttr = array(        // for button attributes
		self::mcView => 'Submit',
		self::mcPreview => 'Preview',
		self::mcSave => 'Save', );

	// FUNCTIONS: CONSTRUCTOR

	function __construct( $myname )
	{
		parent::__construct( $myname, 'editinterface' );
		wfLoadExtensionMessages( $myname );
		$this->errorClass();
		$this->requests();
		$this->msgtext();
		$this->tabbuttons();
		$this->sqlSelect();
		$this->sqlUpdate();
	}

	// FUNCTIONS: ABSTRACT

	// always override, never call
	// create a SpecialClassError class for mError
	abstract protected
	function errorClass();

	// always overrid, never call
	// Get and store request data
	abstract protected 
	function requests();

	// always override, never call
	// set the mPageTitle and mPageText messages
	abstract protected 
	function msgtext();

	// always override, never call
	// set mSqlSelect for tables, fields, conditions, and options
	abstract protected 
	function sqlSelect();

	// always override, never call
	// set mSqlUpdate query to mcSave changes
	// Note: This can be an array of queries, to satisfy multiple updates
	abstract protected
	function sqlUpdate();

	// Alwaya override, never call. Store data in $mData[$row]
	// data is a fetched object
	abstract protected 
	function db_row( $row, $data );

	// Always override, seldom call.
	// Sort mData before displaying
	// see resultTable()
	abstract protected
	function sortData();

	// seldom override, never call	
	// an empty function usually does what you expect.
	abstract protected 
	function submitPage();
	
	// alwyas override, never call
	// Does the special page action
	abstract protected 
	function savePage();
	
	// always override, never call
	// A html table of labels and input tags.
	// see form()
	abstract protected 
	function input();

	// FUNCTIONS: PAGE

	// seldom override, never call
	// This previews the special page action
	protected 
	function previewPage()
	{
		$this->mState = self::mcView;
		if ( $this->db_select() )
			if ( !empty( $this->mData )) 
			{
				$this->mState = self::mcPreview;
				$this->resultTable();
			}
	}

	// FUNCTIONS: STATE

	// seldom override, never call
	// Moves the user between forms (mState transition table)
	protected 
	function tabbuttons()
	{
		$this->mBttnTab = array(
			self::mcView    => array( self::mcPreview ),
			self::mcPreview => array( self::mcPreview, self::mcSave ),
			self::mcSave    => array( self::mcView )
		);
	}

	// seldom override, always call
	protected
	function getState()
	{
		global $wgRequest;

//		$act = $wgRequest->getVal( 'action' );
//		if ( empty( $act ))
			foreach( $this->mSubmitAttr as $key => $value )
			{
				$act = ($value == $wgRequest->gettext( $key )) ? $key :'';
				if ( !empty( $act )) break;
			}
		$this->mState = empty( $act ) ? self::mcView : $act;
	}

	// never override, always call
	// entry point to special page class
	// $act is the $action returned by a submit button
	public 
	function execute( $pageName )
	{
		global $wgUser;

		if ( !$wgUser->isLoggedIn() || $this->userCanExecute($wgUser) )
		{
			$this->getState();
			$this->setHeaders();
			$this->title( $this->mSubmitAttr[$this->mState] );
			$this->description();
			$this->execState();
			$this->error();
			$this->form();
		} else $this->displayRestrictionError();
	}

	// seldom override, never call
	// select method based on global $action
	protected 
	function execState()
	{
		switch( $this->mState )
		{
			case self::mcPreview: $this->previewPage(); break;
			case self::mcSave:    $this->savePage(); break;
			default:
				$this->mState = self::mcView;
				$this->submitPage();
		}
	}

	// FUNCTIONS: SQL

	// seldom override, always call
	// runs query on dbase and populates $mData
	// Note: we assume index tells us whether mSqlSelect is an array
	//       or an array of queries.
	protected 
	function db_select( $index = NULL )
	{
		$result = false;

		if ( !empty( $this->mSqlSelect))
		if ( $this->mValidInput )
		{
			if ( is_null( $index ))
				$query = $this->mSqlSelect;
			else
				$query = $this->mSqlSelect[ $index ];
			$dbr = wfGetDB( DB_SLAVE );
			$res = $dbr->select( 
				$query[self::mcSqlTable],
				$query[self::mcSqlField],
				$query[self::mcSqlCond],
				'',
				$query[self::mcSqlOpt],
				__METHOD__);
			$rows = $dbr->numRows( $res );

			if ( 0 < $rows )
			{
				$this->mDataCount = 0;
				for ($row = 0; $rows > $row; ++$row ) 
					$this->db_row( $row, $dbr->fetchObject( $res ) );
				if ( 0 < $this->mDataCount )
					$result = true;
				else $this->mError->eData();
			} else $this->mError->eFound();

			$dbr->freeResult( $res );
		} else $this->mError->eInput();
		return $result;
	}


	// FUNCTIONS: HTML

	// seldom override, never call
	// see execute()
	protected 
	function error()
	{
		$this->mError->error();
	}

	// sometimes override, never call
	// returns a table header using mData keys
	protected 
	function tableHeader()
	{
		return '<th>'
			.implode( "</th><th>", array_keys( $this->mData[0] )).'</th>';
	}

	// often override, seldom call
	// return a formatted $value for column $name
	// default returns $value
	// see resultTable()
	protected
	function td_col( $record, $name = NULL, $value )
	{
		return $value;
	}

	protected
	function tableRows()
	{
		$rows = '';
		foreach( $this->mData as $row )
		{
			$rows .= "<tr>";
			foreach( $row as $col => $value )
				$rows .= Xml::tags( 'td',array(),
					$this->td_col( $row, $col, $value ))."\n\t\t";
			$rows .= "</tr>\n\t";
		}
		return $rows;
	}
	// seldom override, always call
	// displays special mcPreview and mcSave data
	// see previewPage()
	protected 
	function resultTable()
	{
		global $wgOut;

		$this->sortData();
		$wgOut->addHTML(
			Xml::tags( 'table', array( 'border' => '1' ),
				Xml::tags( 'caption',array(),
					wfElement( 'span', array('style' => 'font-weight:800;'), $this->mCaption )
					.' ('.count( $this->mData ).')'
				)
			.Xml::tags( 'tr', array(), $this->tableHeader() )
			.$this->tableRows())
		);
	}

	// seldom override, never call
	// see execute()
	protected 
	function title( $SubTitle = "" )
	{
		global $wgOut;

		$title = wfMsg( $this->mPageTitle );
		if ( !empty( $SubTitle ))
			$title .= ' ['.$SubTitle.']';
		$wgOut->setPageTitle( $title );
	}

	// seldom override, never call
	// see execute()
	protected 
	function description()
	{
		global $wgOut;

		$wgOut->addWikiText( wfMsg( $this->mPageText ));
	}

	// seldom override, always call
	// see form()
	// builds a html option list for namespaces
	protected
	function options( &$select )
	{
		global $wgCanonicalNamespaceNames; // for code and names

		$optList = $wgCanonicalNamespaceNames;
		$optList[ NS_MAIN ] = self::mcMain;
		$optList[ 'all' ] = 'ALL';
		if ( !array_key_exists( 'all', $optList ))
			die( __CLASS__."::options: no ALL key in list");
		asort( $optList );
		$option = '';
		foreach( $optList as $key => $value )
		{
			$option .= '<option ';
			if ( $key === $select )
				$option .= 'selected';
			$option .= ' value="'.$key.'">'.$value.'</option>
				';
		}
		return $option;
	}

	// never override, always call
	// see form()
	protected 
	function buttons()
	{
		$bttn = '';
		foreach( $this->mBttnTab[$this->mState] as $value )
		 $bttn .= "\n\t"
		 	.'<input type="submit" name="'.$value
		 		.'" value="'.$this->mSubmitAttr[$value].'"/>';
		return $bttn;
	}

	// override often, never call
	// returns the form action page
	protected
	function formAction()
	{
		return SpecialPage::getTitleFor( $this->mPageTitle )->escapeLocalURL();
	}

	// sometimes override, never call
	// displays a tabulated data capture form
	// see execute()
	protected 
	function form()
	{
		global $wgOut;

		$wgOut->addHTML( '
<form id="multipage" method="post" action="'
	.$this->formAction().'">' );
		$wgOut->addHTML( $this->input() );
		$wgOut->addHTML( $this->buttons().'
</form>');	
	}

}

?>