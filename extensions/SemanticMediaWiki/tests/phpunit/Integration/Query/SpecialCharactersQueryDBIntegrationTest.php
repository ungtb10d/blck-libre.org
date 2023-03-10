<?php

namespace SMW\Tests\Integration\Query;

use SMW\Tests\MwDBaseUnitTestCase;
use SMW\Tests\Utils\UtilityFactory;

use SMW\Query\Language\ThingDescription;
use SMW\Query\Language\SomeProperty;

use SMW\DIWikiPage;
use SMW\DIProperty;
use SMW\DataValueFactory;
use SMW\Subobject;

use SMWDIBlob as DIBlob;
use SMWDINumber as DINumber;
use SMWQuery as Query;
use SMWQueryResult as QueryResult;
use SMWDataValue as DataValue;
use SMWDataItem as DataItem;
use SMW\Query\PrintRequest as PrintRequest;
use SMWPropertyValue as PropertyValue;

/**
 * @group SMW
 * @group SMWExtension
 *
 * @group semantic-mediawiki-integration
 * @group semantic-mediawiki-query
 *
 * @group mediawiki-database
 * @group medium
 *
 * @license GNU GPL v2+
 * @since 2.0
 *
 * @author mwjames
 */
class SpecialCharactersQueryDBIntegrationTest extends MwDBaseUnitTestCase {

	private $subjectsToBeCleared = array();
	private $semanticDataFactory;

	private $dataValueFactory;
	private $queryResultValidator;

	protected function setUp() {
		parent::setUp();

		$this->dataValueFactory = DataValueFactory::getInstance();
		$this->queryResultValidator = UtilityFactory::getInstance()->newValidatorFactory()->newQueryResultValidator();
		$this->semanticDataFactory = UtilityFactory::getInstance()->newSemanticDataFactory();
	}

	protected function tearDown() {

		foreach ( $this->subjectsToBeCleared as $subject ) {

			if ( $subject->getTitle() === null ) {
				continue;
			}

			$this->getStore()->deleteSubject( $subject->getTitle() );
		}

		parent::tearDown();
	}

	/**
	 * @dataProvider specialCharactersNameProvider
	 */
	public function testSpecialCharactersInQuery( $subject, $subobjectId, $property, $dataItem ) {

		$dataValue = $this->dataValueFactory->newDataItemValue(
			$dataItem,
			$property
		);

		$semanticData = $this->semanticDataFactory->newEmptySemanticData( $subject );
		$semanticData->addDataValue( $dataValue );

		$subobject = new Subobject( $semanticData->getSubject()->getTitle() );
		$subobject->setEmptyContainerForId( $subobjectId );

		$subobject->addDataValue( $dataValue );
		$semanticData->addSubobject( $subobject );

		$this->getStore()->updateData( $semanticData );

		$propertyValue = new PropertyValue( '__pro' );
		$propertyValue->setDataItem( $property );

		$description = new SomeProperty(
			$property,
			new ThingDescription()
		);

		$description->addPrintRequest(
			new PrintRequest( PrintRequest::PRINT_PROP, null, $propertyValue )
		);

		$query = new Query(
			$description,
			false,
			false
		);

		$query->querymode = Query::MODE_INSTANCES;

		$this->queryResultValidator->assertThatQueryResultHasSubjects(
			array(
				$semanticData->getSubject(),
				$subobject->getSubject() ),
			$this->getStore()->getQueryResult( $query )
		);

		$this->queryResultValidator->assertThatQueryResultContains(
			$dataValue,
			$this->getStore()->getQueryResult( $query )
		);

		$this->subjectsToBeCleared = array(
			$semanticData->getSubject(),
			$subobject->getSubject(),
			$property->getDIWikiPage()
		);
	}

	public function specialCharactersNameProvider() {

		$provider[] = array(
			'????????????',
			'Nu??ez',
			DIProperty::newFromUserLabel( '????????????' )->setPropertyTypeId( '_txt' ),
			new DIBlob( 'Nu??ez' )
		);

		$provider[] = array(
			'????????????',
			'^[0-9]*$',
			DIProperty::newFromUserLabel( '????????????' )->setPropertyTypeId( '_txt' ),
			new DIBlob( '^[0-9]*$' )
		);

		$provider[] = array(
			'Caract??res sp??ciaux',
			'Caract??res sp??ciaux',
			DIProperty::newFromUserLabel( 'Caract??res sp??ciaux' )->setPropertyTypeId( '_wpg' ),
			new DIWikiPage( '??????????????????', NS_MAIN )
		);

		$provider[] = array(
			'????????????????????',
			'????????????????????',
			DIProperty::newFromUserLabel( '????????????????????' )->setPropertyTypeId( '_num' ),
			new DINumber( 8888 )
		);

		$provider[] = array(
			'Foo',
			'{({[[&,,;-]]})}',
			DIProperty::newFromUserLabel( '{({[[&,,;-]]})}' )->setPropertyTypeId( '_wpg' ),
			new DIWikiPage( '{({[[&,,;-]]})}', NS_MAIN )
		);

		return $provider;
	}

}
