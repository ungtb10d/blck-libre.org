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
require_once( 'SpecialClass.php');
// Extended special page classes

/*
// Delete multiple pages matching a source page.
// Pages in several namespaces can be deleted simultaneously. Thus a
// school page can delete pages in School, Project, and Topic namespaces.
// The purpose is to prune namespaces.
*/
class MultiDeleteError extends SpecialClassError
{
	const mcEMPTY         = 4;             // for empty input field

	function addMessages()
	{
		$this->mExtraStr = array( self::mcEMPTY => 'Empty field not allowed.');
	}

	function eEmpty()
	{
		$this->addError( self::mcEMPTY );
	}
}

class MultiDelete extends SpecialClass
{
	// CONSTANTS: DATA
	const mcFrom          = 'From';
	const mcNS            = 'NS';
	const mcResult        = 'Result';
	// CONSTANTS: SQL
	const mcFieldPage     = 'title';
	const mcFieldNS       = 'namespace';
	const mcFieldRedirect = 'redirect';
	// CONSTANTS: MISC
//	const mcMain          = 'Article';
	const mcHookMsg       = 'SpecialDeletepageAfterDelete';
//	const mcUrlPrefix     = '../index.php/';
	// CONSTANTS: HTML
	const mcCheck         = 'on';
	const mcActive        = 'checked';

	private $wpNamespace  = '';
	private $wpRoot       = '';            // for data capture
	private $wpRedirect   = false;         // for sql select
	private $mCheck       = '';            // for html checkbox
	private $root_len     = 0;             // for stripping title names

	function __construct()
	{
		parent::__construct( __CLASS__ );
	}

	function errorClass()
	{
		$this->mError = new MultiDeleteError();
	}

	// always overrid, never call
	// Get and store request data
	protected 
	function requests()
	{
		global $wgRequest;

		$this->wpNamespace = $wgRequest->getText( 'wpNamespace' );
		$this->wpRoot = trim( $wgRequest->getText( 'wpRoot' ), "/ " );
		$this->wpRedirect = $wgRequest->getText( 'wpRedirect' );

		if ( is_numeric( $this->wpNamespace ))
			$this->wpNamespace = intval( $this->wpNamespace );

		if ( !empty( $this->wpRoot ))
			$this->mValidInput = true;
		else  $this->mError->eEmpty();

		if ( $this->mValidInput )
		{
			$this->mCheck = ( self::mcCheck == $this->wpRedirect ) ? self::mcActive : '';
			$this->root_len = strlen( $this->wpRoot );
			$this->mCaption = $this->wpRoot;
		}
	}

	// always override, never call
	// set the mPageTitle and mPageText messages
	protected 
	function msgtext()
	{
		$this->mPageTitle = 'multidelete';
		$this->mPageText = 'multidelete-desc';
	}

	// always override, never call
	// set mSqlSelect for tables, fields, conditions, and options
	protected 
	function sqlSelect()
	{
		$this->mSqlSelect = array(
			self::mcSqlTable =>	array( 'page' ),
			self::mcSqlField =>	array( 
				self::mcNS => 'DISTINCT  on(page_title,page_namespace) page_namespace',
				self::mcFrom => 'page_title'
			),
			self::mcSqlCond =>	array( 
				'page_title ~ \'^'.$this->wpRoot.'.*$\'',
				'page_namespace = \''.intval($this->wpNamespace).'\'',
				'page_is_redirect = \''.( !empty($this->wpRedirect) ? 1 : 0).'\''
			),'',
			self::mcSqlOpt =>	array( 'ORDER BY' => 'page_title,page_namespace ASC' )
		);
		if ( !is_numeric( $this->wpNamespace ))
			unset( $this->mSqlSelect[self::mcSqlCond][ 1 ]);
	}

	// always override, never call
	// set mSqlUpdate query to mcSave changes
	// Note: This can be an array of queries, to satisfy multiple updates
	protected
	function sqlUpdate(){}

	// Alwaya override, never call. Store data in $mData[$row]
	// data is a fetched object
	protected 
	function db_row( $row, $data )
	{		
		$this->mData[ $this->mDataCount++ ] = array(
			self::mcNS     => @Namespace::getCanonicalName( $data->page_namespace ),
			self::mcFrom   => str_replace( ' ','/', $data->page_title ),
			self::mcResult => false 
		);
	}

	// Always override, seldom call.
	// Sort mData before displaying
	// see resultTable()
	protected
	function sortData(){}

	// seldom override, never call	
	// an empty function usually does what you expect.
	protected 
	function submitPage(){}
	
	// alwyas override, never call
	// Does the special page action
	protected 
	function savePage()
	{
		global $wgUser;

		$userDelete = 2 * $wgUser->isAllowed( 'delete' );
		if ( !$wgUser->pingLimiter( 'move' ))
		{
			if ( $this->db_select() )
			{
				foreach( $this->mData as &$row )
				{
					$ot = Title::newFromText( $row[self::mcNS].':'.$row[self::mcFrom] );
					if ( $ot && $ot->getfragment() == '' )
					{
						$DeleteMode = $ot->exists() + $userDelete;
						if ( 3 == $DeleteMode )
						{
							$article = new Article( $ot );
							$row[self::mcResult] = wfMsg( $article->doDeleteArticle( 
								wfMsgForContent( 'delete_and_move_reason' )));
							wfRunHooks( self::mcHookMsg, array( &$this, &$ot ));
						} else $row[self::mcResult] = 'Delete denied';
					} else $row[self::mcResult] = $ot->getfragment();
				}
				$this->resultTable();
			}
		} else $wgOut->rateLimited();
	}
/*	
	// always override, never call
	protected 
	function tableHeader()
	{
		return '<th>'
			.implode( "</th><th>", array_keys( $this->mData[0] )).'</td>';
	}
*/
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
				return $value;
/*
				$url= $record[self::mcNS].':'.$value;
				return '<a href="'.htmlSpecialChars( self::mcUrlPrefix.$url
				.(!empty( $this->mCheck ) ? '?redirect=no': '' ))
				.'" title="'.$url.'">'
					.( empty( $value ) ? $this->wpRoot : $value ).'</a>';
*/
			case self::mcNS:
				if ( empty( $value )) 
					return self::mcMain;
			default:
		}
		return $value;
	}
/*
	private
	function options()
	{
		global $wgCanonicalNamespaceNames; // for code and names

		$optList = $wgCanonicalNamespaceNames;
		$optList[ NS_MAIN ] = self::mcMain;
		$optList[ 'all' ] = 'ALL';
		asort( $optList );
		$option = '';
		foreach( $optList as $key => $value )
		{
			$option .= '<option ';
			if ( $key === $this->wpNamespace )
				$option .= 'selected';
			$option .= ' value="'.$key.'">'.$value.'</option>
				';
		}
		return $option;
	}
*/
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
			<td><select name="wpNamespace" id="wpNamespace">
				'.$this->options( $this->wpNamespace ).'
			</select></td>
		</tr>
		<tr><td style="text-align:right">
			<label for="wpRoot">Page prefix</label></td>
			<td><input type="text" size="40" name="wpRoot" id="wpRoot" value="'
			.htmlSpecialChars( $this->wpRoot ).'"/></td></tr>
		<tr><td style="text-align:right">
			<label for="wpRedirect">Redirect</label></td>
			<td><input type="checkbox" size="40" name="wpRedirect" id="wpRedirect" '
			.htmlSpecialChars( $this->mCheck ).' '
			.( (1== $this->wpRedirect)  ? 'value="'.self::mcCheck.'"' : '').'/></td></tr>
	</table>'
		: '';
	}

}

?>