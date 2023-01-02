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
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
##########################################################################
require_once( 'SpecialClass.php');
// Extended special page classe


// Move multiple pages matching a source page name to a target.
// Note: Both source and target pages must be in the same namespace.
// Pages in several namespaces are moved simultaneously within their
// namespace. The purpose is to prune and graft namespace trees.
// FIXME: redirects should be deleted because this is a structuring
//        tool and should not support linking to non-conforming pages.

class MultiMoveError extends SpecialClassError
{
	const mcETITLE      = 4;                    // input title error
	const mcEMPTY       = 5;                    // empty field error
	const mcEFORM       = 6;                    // input fields conflict

	protected
	function addMessages()
	{
		$this->mExtraStr = array(
		self::mcETITLE => 'Enter a title prefix.',
		self::mcEMPTY  => 'Empty fields: Match and New title.',
		self::mcEFORM  => 'Same fields: Match and New title.' );
	}

	public
	function eTitle()
	{
		parent::addError( self::mcETITLE );
	}

	public
	function eEmpty()
	{
		parent::addError( self::mcEMPTY );
	}

	public
	function eForm()
	{
		parent::addError( self::mcEFORM );
	}

};

class MultiMove extends SpecialClass
{
	const mcNS          = 'NS';            // for data key
	const mcFrom        = 'From';          // for data key
	const mcTo          = 'To';            // for data key
	const mcResult      = 'Result';        // for data key
	const mcHookMsg     = 'SpecialMovepageAfterMove'; // for hook

	private $wpSource   = '';              // for data capture
	private $wpTarget   = '';              // for data capture
	private $wpRoot     = '';              // for data capture
	private $wpNamespace= '';              // for data capture
	private $mMatchStr  = '';              // for string replacement
	private $mTargetStr = '';              // for table and caption
	private $mRootLen   = 0;               // for stripping title names
	private $mTargetLen = 0;               // for html emphasis

	function __construct()
	{
		parent::__construct( __CLASS__ );
	}

	protected
	function errorClass()
	{
		$this->mError = new MultiMoveError();
	}

	private
	function RootIfEmpty( $str )
	{
		return empty( $str ) ? $this->wpRoot : $str;
	}

	// always overrid, never call
	// Get and store request data
	protected 
	function requests()
	{
		global $wgRequest;

		$this->wpNamespace = $wgRequest->getText( 'wpNamespace' );
		$this->wpRoot   = trim( $wgRequest->getText( 'wpRoot' ), "/ " );
		$this->wpSource = trim( $wgRequest->getText( 'wpSource' ));
		$this->wpTarget = trim( $wgRequest->getText( 'wpTarget' ));

		if ( is_numeric( $this->wpNamespace ))
			$this->wpNamespace = intval( $this->wpNamespace );

		if ( !empty( $this->wpRoot ))
			if ( !( empty( $this->wpSource ) && empty( $this->wpTarget )))
				if ( $this->wpSource != $this->wpTarget )
					$this->mValidInput = true;
				else $this->mError->eForm();
			else $this->mError->eEmpty();
		else $this->mError->eTitle();

		if ( $this->mValidInput )
		{
			$this->mCaption = $this->wpRoot;
			$this->mRootLen = strlen( $this->wpRoot );
			$this->mMatchStr = $this->RootIfEmpty( $this->wpSource );
			$this->mTargetLen = strlen( $this->wpTarget );
			$this->mTargetStr = str_replace( $this->mMatchStr, $this->wpTarget, $this->wpRoot );
		}
	}

	// always override, never call
	// set the mPageTitle and mPageText messages
	protected 
	function msgtext()
	{
		$this->mPageTitle = 'multimove';
		$this->mPageText  = 'multimovetext';
	}

	// always override, never call
	// set mSqlSelect for tables, fields, conditions, and options
	protected 
	function sqlSelect()
	{
		$this->mSqlSelect = array(
			self::mcSqlTable => array( 'page' 
			),
			self::mcSqlField => array( 
				self::mcNS   => 'DISTINCT  on(page_title,page_namespace) page_namespace',
				self::mcFrom => 'page_title'
			),
			self::mcSqlCond  => array( 
				'page_namespace = \''.$this->wpNamespace.'\'',
				'page_title ~ \'^'.$this->wpRoot.'.*$\'',
				'page_title ~ \'^.*'.$this->mMatchStr.'.*$\'',
				'page_is_redirect = \'0\''
			),'',
			self::mcSqlOpt =>	array( 'ORDER BY' => 'page_title,page_namespace ASC' )
		);
		if ( !is_numeric( $this->wpNamespace ))
			unset( $this->mSqlSelect[ self::mcSqlCond ][ 0 ]);
	}

	// always override, never call
	// set mSqlUpdate query to save changes
	// Note: This can be an array of queries, to satisfy multiple updates
	protected
	function sqlUpdate()
	{
	}

	// Alwaya override, never call. Store data in $mData[$row]
	// data is a fetched object
	protected 
	function db_row( $row, $data )
	{
		$text_title = str_replace( ' ','_', $data->page_title );
    if ( empty( $this->wpSource ))
			$target = $this->wpTarget .'/'.$text_title;
		else
			$target = trim( str_replace( 
				$this->mMatchStr, $this->wpTarget, $text_title ));
		if ( !empty( $target ))
			$this->mData[ $this->mDataCount++ ] = array(
				// note: ignore ns error, e.g. NS_MAIN
				self::mcFrom   => $text_title,
				self::mcNS     => @Namespace::getCanonicalName( $data->page_namespace ),
				self::mcTo     => $target,
				self::mcResult => false 
			);
	}

	// often override, seldom call
	// return a formatted $value for column $name
	// default returns $value
	// see resultTable()
	protected
	function td_col( $record, $name = NULL, $value )
	{
		switch ( $name )
		{
			case self::mcFrom:
				$url = $record[self::mcNS].':';
				return $value;

			case self::mcTo:
				if ( 0 < $this->mTargetLen )
				{
					$start = strpos( $value, $this->wpTarget );
					if ( false !== $start )
						return substr( $value, 0, $start ).'<b>'
							.substr( $value, $start, $this->mTargetLen ).'</b>'
							.substr( $value, $start + $this->mTargetLen );
					else return 'error: '.$value;
				} else break;

			case self::mcNS:
				return '<div style="text-align:center">'
					.( empty( $value ) ? self::mcMain : $value ).'</div>';
			default:
		}
		return $value;
	}

	// Always override, seldom call.
	// Sort data before displaying
	// see resultTable()
	protected
	function sortData()
	{
	}

	// seldom override, never call	
	// an empty function usually does what you expect.
	protected 
	function submitPage()
	{
	}
	
	// alwyas override, never call
	// Does the special page action
	protected 
	function savePage()
	{
		global $wgUser, $wgOut;

		$userDelete = 2 * $wgUser->isAllowed( 'delete' );
		if ( !$wgUser->pingLimiter( 'move' ))
		{
			if ( $this->db_select() )
			{
				foreach( $this->mData as &$row )
				{
					$ns = $row[ self::mcNS ].':';
					$ot = Title::newFromText( $ns.$row[ self::mcFrom ] );
					$nt = Title::newFromText( $ns.$row[ self::mcTo ] );
					if ( $nt && $nt->getfragment() == '' )
					{
						$DeleteMode = $nt->exists() + $userDelete;
						if ( 1 != $DeleteMode ) // anything but nt exists and !delete
						{
							$row[self::mcResult] = wfMsg( $ot->moveTo( $nt, true, 'multi-move' ));
							wfRunHooks( self::mcHookMsg, array( &$this , &$ot , &$nt ));
						} else $row[self::mcResult] = 'Delete denied';
					} else $row[self::mcResult] = '#command';
				}
				$this->resultTable();
			}
		} else $wgOut->rateLimited();
	}

	// always override, never call
	// A html table of labels and input tags.
	// see form()
	protected 
	function input()
	{
		return ( self::mcSave != $this->mState ) 
			?	'
	<table>
		<tr><td style="text-align:right">
			<label for="wpNamespace">Namespace</label></td>
			<td><select name="wpNamespace" id="wpNamespace">'
		.$this->options( $this->wpNamespace ).'"</select></td>
		</tr>
		<tr><td style="text-align:right">
			<label for="wpRoot">Title prefix</label></td>
			<td><input type="text" size="40" name="wpRoot" id="wpRoot" value="'
		.htmlSpecialChars( $this->wpRoot ).'"/></td>
		</tr>
		<tr><td style="text-align:right">
		<label for="wpSource">Match Title</label></td>
		<td><input type="text" size="20" name="wpSource" id="wpSource" value="'
		.htmlSpecialChars( $this->wpSource ).'"/></td>
		</tr>
		<tr><td style="text-align:right">
		<label for="wpTarget">New Title</label></td>
		<td><input type="text" size="20" name="wpTarget" id="wpTarget" value="'
		.htmlSpecialChars( $this->wpTarget ).'"/></td></tr>
	</table>'
		: '';
	}

}

?>