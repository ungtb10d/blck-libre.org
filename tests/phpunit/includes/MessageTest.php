<?php

class MessageTest extends MediaWikiLangTestCase {

	protected function setUp() {
		parent::setUp();

		$this->setMwGlobals( array(
			'wgLang' => Language::factory( 'en' ),
			'wgForceUIMsgAsContentMsg' => array(),
		) );
	}

	/**
	 * @covers Message::__construct
	 * @dataProvider provideConstructor
	 */
	public function testConstructor( $expectedLang, $key, $params, $language ) {
		$message = new Message( $key, $params, $language );

		$this->assertEquals( $key, $message->getKey() );
		$this->assertEquals( $params, $message->getParams() );
		$this->assertEquals( $expectedLang, $message->getLanguage() );
	}

	public static function provideConstructor() {
		$langDe = Language::factory( 'de' );
		$langEn = Language::factory( 'en' );

		return array(
			array( $langDe, 'foo', array(), $langDe ),
			array( $langDe, 'foo', array( 'bar' ), $langDe ),
			array( $langEn, 'foo', array( 'bar' ), null )
		);
	}

	public static function provideConstructorParams() {
		return array(
			array(
				array(),
				array(),
			),
			array(
				array( 'foo' ),
				array( 'foo' ),
			),
			array(
				array( 'foo', 'bar' ),
				array( 'foo', 'bar' ),
			),
			array(
				array( 'baz' ),
				array( array( 'baz' ) ),
			),
			array(
				array( 'baz', 'foo' ),
				array( array( 'baz', 'foo' ) ),
			),
			array(
				array( 'baz', 'foo' ),
				array( array( 'baz', 'foo' ), 'hhh' ),
			),
			array(
				array( 'baz', 'foo' ),
				array( array( 'baz', 'foo' ), 'hhh', array( 'ahahahahha' ) ),
			),
			array(
				array( 'baz', 'foo' ),
				array( array( 'baz', 'foo' ), array( 'ahahahahha' ) ),
			),
			array(
				array( 'baz' ),
				array( array( 'baz' ), array( 'ahahahahha' ) ),
			),
		);
	}

	/**
	 * @covers Message::__construct
	 * @covers Message::getParams
	 * @dataProvider provideConstructorParams
	 */
	public function testConstructorParams( $expected, $args ) {
		$msg = new Message( 'imasomething' );

		$returned = call_user_func_array( array( $msg, 'params' ), $args );

		$this->assertSame( $msg, $returned );
		$this->assertEquals( $expected, $msg->getParams() );
	}

	public static function provideConstructorLanguage() {
		return array(
			array( 'foo', array( 'bar' ), 'en' ),
			array( 'foo', array( 'bar' ), 'de' )
		);
	}

	/**
	 * @covers Message::__construct
	 * @covers Message::getLanguage
	 * @dataProvider provideConstructorLanguage
	 */
	public function testConstructorLanguage( $key, $params, $languageCode ) {
		$language = Language::factory( $languageCode );
		$message = new Message( $key, $params, $language );

		$this->assertEquals( $language, $message->getLanguage() );
	}

	public static function provideKeys() {
		return array(
			'string' => array(
				'key' => 'mainpage',
				'expected' => array( 'mainpage' ),
			),
			'single' => array(
				'key' => array( 'mainpage' ),
				'expected' => array( 'mainpage' ),
			),
			'multi' => array(
				'key' => array( 'mainpage-foo', 'mainpage-bar', 'mainpage' ),
				'expected' => array( 'mainpage-foo', 'mainpage-bar', 'mainpage' ),
			),
			'empty' => array(
				'key' => array(),
				'expected' => null,
				'exception' => 'InvalidArgumentException',
			),
			'null' => array(
				'key' => null,
				'expected' => null,
				'exception' => 'InvalidArgumentException',
			),
			'bad type' => array(
				'key' => 123,
				'expected' => null,
				'exception' => 'InvalidArgumentException',
			),
		);
	}

	/**
	 * @covers Message::__construct
	 * @covers Message::getKey
	 * @covers Message::isMultiKey
	 * @covers Message::getKeysToTry
	 * @dataProvider provideKeys
	 */
	public function testKeys( $key, $expected, $exception = null ) {
		if ( $exception ) {
			$this->setExpectedException( $exception );
		}

		$msg = new Message( $key );
		$this->assertContains( $msg->getKey(), $expected );
		$this->assertEquals( $expected, $msg->getKeysToTry() );
		$this->assertEquals( count( $expected ) > 1, $msg->isMultiKey() );
	}

	/**
	 * @covers ::wfMessage
	 */
	public function testWfMessage() {
		$this->assertInstanceOf( 'Message', wfMessage( 'mainpage' ) );
		$this->assertInstanceOf( 'Message', wfMessage( 'i-dont-exist-evar' ) );
	}

	/**
	 * @covers Message::newFromKey
	 */
	public function testNewFromKey() {
		$this->assertInstanceOf( 'Message', Message::newFromKey( 'mainpage' ) );
		$this->assertInstanceOf( 'Message', Message::newFromKey( 'i-dont-exist-evar' ) );
	}

	/**
	 * @covers ::wfMessage
	 * @covers Message::__construct
	 */
	public function testWfMessageParams() {
		$this->assertEquals( 'Return to $1.', wfMessage( 'returnto' )->text() );
		$this->assertEquals( 'Return to $1.', wfMessage( 'returnto', array() )->text() );
		$this->assertEquals(
			'You have foo (bar).',
			wfMessage( 'youhavenewmessages', 'foo', 'bar' )->text()
		);
		$this->assertEquals(
			'You have foo (bar).',
			wfMessage( 'youhavenewmessages', array( 'foo', 'bar' ) )->text()
		);
	}

	/**
	 * @covers Message::exists
	 */
	public function testExists() {
		$this->assertTrue( wfMessage( 'mainpage' )->exists() );
		$this->assertTrue( wfMessage( 'mainpage' )->params( array() )->exists() );
		$this->assertTrue( wfMessage( 'mainpage' )->rawParams( 'foo', 123 )->exists() );
		$this->assertFalse( wfMessage( 'i-dont-exist-evar' )->exists() );
		$this->assertFalse( wfMessage( 'i-dont-exist-evar' )->params( array() )->exists() );
		$this->assertFalse( wfMessage( 'i-dont-exist-evar' )->rawParams( 'foo', 123 )->exists() );
	}

	/**
	 * @covers Message::__construct
	 * @covers Message::text
	 * @covers Message::plain
	 * @covers Message::escaped
	 * @covers Message::toString
	 */
	public function testToStringKey() {
		$this->assertEquals( 'Main Page', wfMessage( 'mainpage' )->text() );
		$this->assertEquals( '<i-dont-exist-evar>', wfMessage( 'i-dont-exist-evar' )->text() );
		$this->assertEquals( '<i<dont>exist-evar>', wfMessage( 'i<dont>exist-evar' )->text() );
		$this->assertEquals( '<i-dont-exist-evar>', wfMessage( 'i-dont-exist-evar' )->plain() );
		$this->assertEquals( '<i<dont>exist-evar>', wfMessage( 'i<dont>exist-evar' )->plain() );
		$this->assertEquals( '&lt;i-dont-exist-evar&gt;', wfMessage( 'i-dont-exist-evar' )->escaped() );
		$this->assertEquals(
			'&lt;i&lt;dont&gt;exist-evar&gt;',
			wfMessage( 'i<dont>exist-evar' )->escaped()
		);
	}

	public static function provideToString() {
		return array(
			array( 'mainpage', 'Main Page' ),
			array( 'i-dont-exist-evar', '<i-dont-exist-evar>' ),
			array( 'i-dont-exist-evar', '&lt;i-dont-exist-evar&gt;', 'escaped' ),
		);
	}

	/**
	 * @covers Message::toString
	 * @covers Message::__toString
	 * @dataProvider provideToString
	 */
	public function testToString( $key, $expect, $format = 'plain' ) {
		$msg = new Message( $key );
		$msg->$format();
		$this->assertEquals( $expect, $msg->toString() );
		$this->assertEquals( $expect, $msg->__toString() );
	}

	/**
	 * @covers Message::inLanguage
	 */
	public function testInLanguage() {
		$this->assertEquals( 'Main Page', wfMessage( 'mainpage' )->inLanguage( 'en' )->text() );
		$this->assertEquals( '?????????????????? ????????????????',
			wfMessage( 'mainpage' )->inLanguage( 'ru' )->text() );

		// NOTE: make sure internal caching of the message text is reset appropriately
		$msg = wfMessage( 'mainpage' );
		$this->assertEquals( 'Main Page', $msg->inLanguage( Language::factory( 'en' ) )->text() );
		$this->assertEquals(
			'?????????????????? ????????????????',
			$msg->inLanguage( Language::factory( 'ru' ) )->text()
		);
	}

	/**
	 * @covers Message::rawParam
	 * @covers Message::rawParams
	 */
	public function testRawParams() {
		$this->assertEquals(
			'(?????????????????? ????????????????)',
			wfMessage( 'parentheses', '?????????????????? ????????????????' )->plain()
		);
		$this->assertEquals(
			'(?????????????????? ???????????????? $1)',
			wfMessage( 'parentheses', '?????????????????? ???????????????? $1' )->plain()
		);
		$this->assertEquals(
			'(?????????????????? ????????????????)',
			wfMessage( 'parentheses' )->rawParams( '?????????????????? ????????????????' )->plain()
		);
		$this->assertEquals(
			'(?????????????????? ???????????????? $1)',
			wfMessage( 'parentheses' )->rawParams( '?????????????????? ???????????????? $1' )->plain()
		);
	}

	/**
	 * @covers RawMessage::__construct
	 * @covers RawMessage::fetchMessage
	 */
	public function testRawMessage() {
		$msg = new RawMessage( 'example &' );
		$this->assertEquals( 'example &', $msg->plain() );
		$this->assertEquals( 'example &amp;', $msg->escaped() );
	}

	/**
	 * @covers Message::params
	 * @covers Message::toString
	 * @covers Message::replaceParameters
	 */
	public function testReplaceManyParams() {
		$msg = new RawMessage( '$1$2$3$4$5$6$7$8$9$10$11$12' );
		// One less than above has placeholders
		$params = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k' );
		$this->assertEquals(
			'abcdefghijka2',
			$msg->params( $params )->plain(),
			'Params > 9 are replaced correctly'
		);

		$msg = new RawMessage( 'Params$*' );
		$params = array( 'ab', 'bc', 'cd' );
		$this->assertEquals(
			'Params: ab, bc, cd',
			$msg->params( $params )->text()
		);
	}

	/**
	 * @covers Message::numParam
	 * @covers Message::numParams
	 */
	public function testNumParams() {
		$lang = Language::factory( 'en' );
		$msg = new RawMessage( '$1' );

		$this->assertEquals(
			$lang->formatNum( 123456.789 ),
			$msg->inLanguage( $lang )->numParams( 123456.789 )->plain(),
			'numParams is handled correctly'
		);
	}

	/**
	 * @covers Message::durationParam
	 * @covers Message::durationParams
	 */
	public function testDurationParams() {
		$lang = Language::factory( 'en' );
		$msg = new RawMessage( '$1' );

		$this->assertEquals(
			$lang->formatDuration( 1234 ),
			$msg->inLanguage( $lang )->durationParams( 1234 )->plain(),
			'durationParams is handled correctly'
		);
	}

	/**
	 * FIXME: This should not need database, but Language#formatExpiry does (bug 55912)
	 * @group Database
	 * @covers Message::expiryParam
	 * @covers Message::expiryParams
	 */
	public function testExpiryParams() {
		$lang = Language::factory( 'en' );
		$msg = new RawMessage( '$1' );

		$this->assertEquals(
			$lang->formatExpiry( wfTimestampNow() ),
			$msg->inLanguage( $lang )->expiryParams( wfTimestampNow() )->plain(),
			'expiryParams is handled correctly'
		);
	}

	/**
	 * @covers Message::timeperiodParam
	 * @covers Message::timeperiodParams
	 */
	public function testTimeperiodParams() {
		$lang = Language::factory( 'en' );
		$msg = new RawMessage( '$1' );

		$this->assertEquals(
			$lang->formatTimePeriod( 1234 ),
			$msg->inLanguage( $lang )->timeperiodParams( 1234 )->plain(),
			'timeperiodParams is handled correctly'
		);
	}

	/**
	 * @covers Message::sizeParam
	 * @covers Message::sizeParams
	 */
	public function testSizeParams() {
		$lang = Language::factory( 'en' );
		$msg = new RawMessage( '$1' );

		$this->assertEquals(
			$lang->formatSize( 123456 ),
			$msg->inLanguage( $lang )->sizeParams( 123456 )->plain(),
			'sizeParams is handled correctly'
		);
	}

	/**
	 * @covers Message::bitrateParam
	 * @covers Message::bitrateParams
	 */
	public function testBitrateParams() {
		$lang = Language::factory( 'en' );
		$msg = new RawMessage( '$1' );

		$this->assertEquals(
			$lang->formatBitrate( 123456 ),
			$msg->inLanguage( $lang )->bitrateParams( 123456 )->plain(),
			'bitrateParams is handled correctly'
		);
	}

	public static function providePlaintextParams() {
		return array(
			array(
				'one $2 <div>foo</div> [[Bar]] {{Baz}} &lt;',
				'plain',
			),

			array(
				// expect
				'one $2 <div>foo</div> [[Bar]] {{Baz}} &lt;',
				// format
				'text',
			),
			array(
				'one $2 &lt;div&gt;foo&lt;/div&gt; [[Bar]] {{Baz}} &amp;lt;',
				'escaped',
			),

			array(
				'one $2 &lt;div&gt;foo&lt;/div&gt; [[Bar]] {{Baz}} &amp;lt;',
				'parse',
			),

			array(
				"<p>one $2 &lt;div&gt;foo&lt;/div&gt; [[Bar]] {{Baz}} &amp;lt;\n</p>",
				'parseAsBlock',
			),
		);
	}

	/**
	 * @covers Message::plaintextParam
	 * @covers Message::plaintextParams
	 * @covers Message::formatPlaintext
	 * @covers Message::toString
	 * @covers Message::parse
	 * @covers Message::parseAsBlock
	 * @dataProvider providePlaintextParams
	 */
	public function testPlaintextParams( $expect, $format ) {
		$lang = Language::factory( 'en' );

		$msg = new RawMessage( '$1 $2' );
		$params = array(
			'one $2',
			'<div>foo</div> [[Bar]] {{Baz}} &lt;',
		);
		$this->assertEquals(
			$expect,
			$msg->inLanguage( $lang )->plaintextParams( $params )->$format(),
			"Fail formatting for $format"
		);
	}

	public static function provideParser() {
		return array(
			array(
				"''&'' <x><!-- x -->",
				'plain',
			),

			array(
				"''&'' <x><!-- x -->",
				'text',
			),
			array(
				'<i>&amp;</i> &lt;x&gt;',
				'parse',
			),

			array(
				"<p><i>&amp;</i> &lt;x&gt;\n</p>",
				'parseAsBlock',
			),
		);
	}

	/**
	 * @covers Message::text
	 * @covers Message::parse
	 * @covers Message::parseAsBlock
	 * @covers Message::toString
	 * @covers Message::transformText
	 * @covers Message::parseText
	 * @dataProvider provideParser
	 */
	public function testParser( $expect, $format ) {
		$msg = new RawMessage( "''&'' <x><!-- x -->" );
		$this->assertEquals(
			$expect,
			$msg->inLanguage( 'en' )->$format()
		);
	}

	/**
	 * @covers Message::inContentLanguage
	 */
	public function testInContentLanguage() {
		$this->setMwGlobals( 'wgLang', Language::factory( 'fr' ) );

		// NOTE: make sure internal caching of the message text is reset appropriately
		$msg = wfMessage( 'mainpage' );
		$this->assertEquals( 'Hauptseite', $msg->inLanguage( 'de' )->plain(), "inLanguage( 'de' )" );
		$this->assertEquals( 'Main Page', $msg->inContentLanguage()->plain(), "inContentLanguage()" );
		$this->assertEquals( 'Accueil', $msg->inLanguage( 'fr' )->plain(), "inLanguage( 'fr' )" );
	}

	/**
	 * @covers Message::inContentLanguage
	 */
	public function testInContentLanguageOverride() {
		$this->setMwGlobals( array(
			'wgLang' => Language::factory( 'fr' ),
			'wgForceUIMsgAsContentMsg' => array( 'mainpage' ),
		) );

		// NOTE: make sure internal caching of the message text is reset appropriately.
		// NOTE: wgForceUIMsgAsContentMsg forces the messages *current* language to be used.
		$msg = wfMessage( 'mainpage' );
		$this->assertEquals(
			'Accueil',
			$msg->inContentLanguage()->plain(),
			'inContentLanguage() with ForceUIMsg override enabled'
		);
		$this->assertEquals( 'Main Page', $msg->inLanguage( 'en' )->plain(), "inLanguage( 'en' )" );
		$this->assertEquals(
			'Main Page',
			$msg->inContentLanguage()->plain(),
			'inContentLanguage() with ForceUIMsg override enabled'
		);
		$this->assertEquals( 'Hauptseite', $msg->inLanguage( 'de' )->plain(), "inLanguage( 'de' )" );
	}

	/**
	 * @expectedException MWException
	 * @covers Message::inLanguage
	 */
	public function testInLanguageThrows() {
		wfMessage( 'foo' )->inLanguage( 123 );
	}
}
