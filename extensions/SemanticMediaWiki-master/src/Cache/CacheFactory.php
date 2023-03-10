<?php

namespace SMW\Cache;

use Onoi\Cache\CacheFactory as OnoiCacheFactory;
use SMW\ApplicationFactory;
use ObjectCache;
use RuntimeException;

/**
 * @license GNU GPL v2+
 * @since 2.2
 *
 * @author mwjames
 */
class CacheFactory {

	/**
	 * @var string|integer
	 */
	private $mainCacheType;

	/**
	 * @var string|integer
	 */
	private $blobCacheType;

	/**
	 * @since 2.2
	 *
	 * @param string|integer $mainCacheType
	 * @param string|integer|null $blobCacheType
	 */
	public function __construct( $mainCacheType, $blobCacheType = null ) {
		$this->mainCacheType = $mainCacheType;
		$this->blobCacheType = $blobCacheType;

		if ( $this->blobCacheType === null ) {
			$this->blobCacheType = $GLOBALS['smwgBlobCacheType'];
		}

	}

	/**
	 * @since 2.2
	 *
	 * @return string|integer
	 */
	public function getMainCacheType() {
		return $this->mainCacheType;
	}

	/**
	 * @since 2.3
	 *
	 * @return string|integer
	 */
	public function getBlobCacheType() {
		return $this->blobCacheType;
	}

	/**
	 * @since 2.2
	 *
	 * @return string
	 */
	public function getCachePrefix() {
		return $GLOBALS['wgCachePrefix'] === false ? wfWikiID() : $GLOBALS['wgCachePrefix'];
	}

	/**
	 * @since 2.2
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function getFactboxCacheKey( $key ) {
		return $this->getCachePrefix() . ':smw:factbox-cache:' . md5( $key );
	}

	/**
	 * @since 2.2
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function getPurgeCacheKey( $key ) {
		return $this->getCachePrefix() . ':smw:autorefresh-cache:' . md5( $key );
	}

	/**
	 * @since 2.2
	 *
	 * @param array $cacheOptions
	 *
	 * @return stdClass
	 * @throws RuntimeException
	 */
	public function newCacheOptions( array $cacheOptions ) {

		if ( !isset( $cacheOptions['useCache'] ) || !isset( $cacheOptions['ttl'] ) ) {
			throw new RuntimeException( "Cache options is missing a useCache/ttl parameter" );
		}

		return (object)$cacheOptions;
	}

	/**
	 * @since 2.2
	 *
	 * @param integer $cacheSize
	 *
	 * @return Cache
	 */
	public function newFixedInMemoryCache( $cacheSize = 500 ) {
		return OnoiCacheFactory::getInstance()->newFixedInMemoryLruCache( $cacheSize );
	}

	/**
	 * @since 2.2
	 *
	 * @return Cache
	 */
	public function newNullCache() {
		return OnoiCacheFactory::getInstance()->newNullCache();
	}

	/**
	 * @since 2.2
	 *
	 * @param integer|string $mediaWikiCacheType
	 *
	 * @return Cache
	 */
	public function newMediaWikiCompositeCache( $mediaWikiCacheType = null ) {

		$mediaWikiCache = ObjectCache::getInstance(
			( $mediaWikiCacheType === null ? $this->getMainCacheType() : $mediaWikiCacheType )
		);

		$compositeCache = OnoiCacheFactory::getInstance()->newCompositeCache( array(
			$this->newFixedInMemoryCache( 500 ),
			OnoiCacheFactory::getInstance()->newMediaWikiCache( $mediaWikiCache )
		) );

		return $compositeCache;
	}

}
