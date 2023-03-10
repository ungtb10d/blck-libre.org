<?php

namespace SMW\Tests\Integration\MediaWiki\Hooks;

use SMW\Tests\Utils\UtilityFactory;
use SMW\Tests\MwDBaseUnitTestCase;

use SMW\Query\Language\SomeProperty;
use SMW\Query\Language\ValueDescription;
use SMW\Query\Language\ThingDescription;

use SMW\ApplicationFactory;
use SMW\DIProperty;
use SMW\DIWikiPage;

use SMWQuery as Query;

use Title;
use WikiPage;

/**
 * @group SMW
 * @group SMWExtension
 * @group semantic-mediawiki-integration
 * @group medium
 *
 * @license GNU GPL v2+
 * @since   2.1
 *
 * @author mwjames
 */
class TitleMoveCompleteIntegrationTest extends MwDBaseUnitTestCase {

	private $mwHooksHandler;
	private $queryResultValidator;
	private $applicationFactory;
	private $toBeDeleted = array();
	private $pageCreator;

	protected function setUp() {
		parent::setUp();

		$utilityFactory = UtilityFactory::getInstance();

		$this->applicationFactory = ApplicationFactory::getInstance();
		$this->queryResultValidator = $utilityFactory->newValidatorFactory()->newQueryResultValidator();

		$this->mwHooksHandler = $utilityFactory->newMwHooksHandler();
		$this->mwHooksHandler->deregisterListedHooks();

		$this->mwHooksHandler->register(
			'TitleMoveComplete',
			$this->mwHooksHandler->getHookRegistry()->getDefinition( 'TitleMoveComplete' )
		);

		$this->pageCreator = $utilityFactory->newPageCreator();
	}

	protected function tearDown() {

		$this->mwHooksHandler->restoreListedHooks();
		$this->applicationFactory->clear();

		$pageDeleter = UtilityFactory::getInstance()->newPageDeleter();
		$pageDeleter->doDeletePoolOfPages( $this->toBeDeleted );

		parent::tearDown();
	}

	public function testPageMoveWithCreationOfRedirectTarget() {

		$oldTitle = Title::newFromText( __METHOD__ . '-old' );
		$expectedNewTitle = Title::newFromText( __METHOD__ . '-new' );

		$this->assertNull(
			WikiPage::factory( $expectedNewTitle )->getRevision()
		);

		$this->pageCreator
			->createPage( $oldTitle );

		$result = $this->pageCreator
			->getPage()
			->getTitle()
			->moveTo( $expectedNewTitle, false, 'test', true );
		$this->assertTrue( $result );

		$this->assertNotNull(
			WikiPage::factory( $oldTitle )->getRevision()
		);

		$this->assertNotNull(
			WikiPage::factory( $expectedNewTitle )->getRevision()
		);

		$this->toBeDeleted = array(
			$oldTitle,
			$expectedNewTitle
		);
	}

	public function testPageMoveWithRemovalOfOldPage() {

		// PHPUnit query issue
		$this->skipTestForDatabase( array( 'postgres' ) );

		// Revison showed an issue on 1.19 not being null after the move
		$this->skipTestForMediaWikiVersionLowerThan( '1.21' );

		// Further hooks required to ensure in-text annotations can be used for queries
		$this->mwHooksHandler->register(
			'InternalParseBeforeLinks',
			$this->mwHooksHandler->getHookRegistry()->getDefinition( 'InternalParseBeforeLinks' )
		);

		$this->mwHooksHandler->register(
			'LinksUpdateConstructed',
			$this->mwHooksHandler->getHookRegistry()->getDefinition( 'LinksUpdateConstructed' )
		);

		$title = Title::newFromText( __METHOD__ . '-old' );
		$expectedNewTitle = Title::newFromText( __METHOD__ . '-new' );

		$this->assertNull(
			WikiPage::factory( $expectedNewTitle )->getRevision()
		);

		$this->pageCreator
			->createPage( $title )
			->doEdit( '[[Has function hook test::PageCompleteMove]]' );

		$this->pageCreator
			->getPage()
			->getTitle()
			->moveTo( $expectedNewTitle, false, 'test', false );

		$this->assertNull(
			WikiPage::factory( $title )->getRevision()
		);

		$this->assertNotNull(
			WikiPage::factory( $expectedNewTitle )->getRevision()
		);

		/**
		 * @query {{#ask: [[Has function hook test::PageCompleteMove]] }}
		 */
		$description = new SomeProperty(
			DIProperty::newFromUserLabel( 'Has function hook test' ),
			new ValueDescription( new DIWikiPage( 'PageCompleteMove', 0 ), null, SMW_CMP_EQ )
		);

		$query = new Query(
			$description,
			false,
			true
		);

		$query->querymode = Query::MODE_INSTANCES;

		$queryResult = $this->getStore()->getQueryResult( $query );

		// #566
		$this->assertCount(
			2,
			$queryResult->getResults()
		);

		$this->queryResultValidator->assertThatQueryResultHasSubjects(
			array( DIWikiPage::newFromTitle( $expectedNewTitle ), DIWikiPage::newFromTitle( $title ) ),
			$queryResult
		);

		$this->toBeDeleted = array(
			$title,
			$expectedNewTitle
		);
	}

	public function testPredefinedPropertyPageIsNotMovable() {

		$this->mwHooksHandler->register(
			'TitleIsMovable',
			$this->mwHooksHandler->getHookRegistry()->getDefinition( 'TitleIsMovable' )
		);

		$title = Title::newFromText( 'Modification date', SMW_NS_PROPERTY );
		$expectedNewTitle = Title::newFromText( __METHOD__, SMW_NS_PROPERTY );

		$this->pageCreator
			->createPage( $title );

		$this->pageCreator
			->getPage()
			->getTitle()
			->moveTo( $expectedNewTitle, false, 'test', true );

		$this->assertNotNull(
			WikiPage::factory( $title )->getRevision()
		);

		$this->assertNull(
			WikiPage::factory( $expectedNewTitle )->getRevision()
		);

		$this->toBeDeleted = array(
			$title,
			$expectedNewTitle
		);
	}

}
