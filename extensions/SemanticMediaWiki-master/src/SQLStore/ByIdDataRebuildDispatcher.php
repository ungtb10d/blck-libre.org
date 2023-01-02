<?php

namespace SMW\SQLStore;

use SMW\SemanticData;
use SMM\DIWikiPage;
use SMW\DIProperty;
use SMW\Store;
use SMW\MediaWiki\Jobs\UpdateJob;
use SMW\MediaWiki\Jobs\JobBase;

use Title;
use Hooks;

/**
 * @license GNU GPL v2+
 * @since 2.3
 *
 * @author Markus Krötzsch
 * @author Jeroen De Dauw
 * @author Nischay Nahata
 * @author mwjames
 */
class ByIdDataRebuildDispatcher {

	/**
	 * @var Store
	 */
	private $store = null;

	/**
	 * @var boolean
	 */
	private $useUpdateJobParseMode = true;

	/**
	 * @var boolean
	 */
	private $useJobQueueScheduler = true;

	/**
	 * @var array|false
	 */
	private $namespaces = false;

	/**
	 * @var integer
	 */
	private $iterationLimit = 1;

	/**
	 * @var integer
	 */
	private $progress = 1;

	/**
	 * @since 2.3
	 *
	 * @param Store $store
	 */
	public function __construct( Store $store ) {
		$this->store = $store;
	}

	/**
	 * @since 2.3
	 *
	 * @param boolean $updateJobParseMode
	 */
	public function setUpdateJobToUseParseMode( $updateJobParseMode ) {
		$this->useUpdateJobParseMode = (bool)$updateJobParseMode;
	}

	/**
	 * @since 2.3
	 *
	 * @param boolean $useJobQueueScheduler
	 */
	public function setUpdateJobToUseJobQueueScheduler( $useJobQueueScheduler ) {
		$this->useJobQueueScheduler = (bool)$useJobQueueScheduler;
	}

	/**
	 * @since 2.3
	 *
	 * @param array|false $namespaces
	 */
	public function setNamespacesTo( $namespaces ) {
		$this->namespaces = $namespaces;
	}

	/**
	 * @since 2.3
	 *
	 * @param integer $iterationLimit
	 */
	public function setIterationLimit( $iterationLimit ) {
		$this->iterationLimit = (int)$iterationLimit;
	}

	/**
	 * @since 2.3
	 *
	 * @return integer
	 */
	public function getMaxId() {

		$db = $this->store->getConnection( 'mw.db' );

		$maxByPageId = (int)$db->selectField(
			'page',
			'MAX(page_id)',
			'',
			__METHOD__
		);

		$maxBySmwId = (int)$db->selectField(
			\SMWSql3SmwIds::tableName,
			'MAX(smw_id)',
			'',
			__METHOD__
		);

		return max( $maxByPageId, $maxBySmwId );
	}

	/**
	 * Decimal between 0 and 1 to indicate the overall progress of the rebuild
	 * process
	 *
	 * @since 2.3
	 *
	 * @return integer
	 */
	public function getEstimatedProgress() {
		return $this->progress;
	}

	/**
	 * Dispatching of a single or a chunk of ids in either online or batch mode
	 * using the JobQueueScheduler
	 *
	 * @since 2.3
	 *
	 * @param integer &$id
	 */
	public function dispatchRebuildFor( &$id ) {

		$updatejobs = array();

		// was nothing done in this run?
		$emptyrange = true;

		$this->createUpdateJobsForTitleIdRange( $id, $updatejobs );

		if ( $updatejobs !== array() ) {
			$emptyrange = false;
		}

		$this->createUpdateJobsForSmwIdRange( $id, $updatejobs, $emptyrange );

		// Deprecated since 2.3, use 'SMW::SQLStore::BeforeDataRebuildJobInsert'
		wfRunHooks('smwRefreshDataJobs', array( &$updatejobs ) );

		Hooks::run( 'SMW::SQLStore::BeforeDataRebuildJobInsert', array( $this->store, &$updatejobs ) );

		if ( $this->useJobQueueScheduler ) {
			JobBase::batchInsert( $updatejobs );
		} else {
			foreach ( $updatejobs as $job ) {
				$job->run();
			}
		}

		// -1 means that no next position is available
		$this->findNextIdPosition( $id, $emptyrange );

		return $this->progress = $id > 0 ? $id / $this->getMaxId() : 1;
	}

	/**
	 * @param integer $id
	 * @param UpdateJob[] &$updatejobs
	 */
	private function createUpdateJobsForTitleIdRange( $id, &$updatejobs ) {

		// Update by MediaWiki page id --> make sure we get all pages.
		$tids = array();

		// Array of ids
		for ( $i = $id; $i < $id + $this->iterationLimit; $i++ ) {
			$tids[] = $i;
		}

		$titles = Title::newFromIDs( $tids );

		foreach ( $titles as $title ) {
			if ( ( $this->namespaces == false ) || ( in_array( $title->getNamespace(), $this->namespaces ) ) ) {
				$updatejobs[] = $this->newUpdateJob( $title );
			}
		}
	}

	/**
	 * @param integer $id
	 * @param UpdateJob[] &$updatejobs
	 */
	private function createUpdateJobsForSmwIdRange( $id, &$updatejobs, &$emptyrange ) {

		// update by internal SMW id --> make sure we get all objects in SMW
		$db = $this->store->getConnection( 'mw.db' );

		$res = $db->select(
			\SMWSql3SmwIds::tableName,
			array( 'smw_id', 'smw_title', 'smw_namespace', 'smw_iw', 'smw_subobject' ),
			array(
				"smw_id >= $id ",
				" smw_id < " . $db->addQuotes( $id + $this->iterationLimit )
			),
			__METHOD__
		);

		foreach ( $res as $row ) {
			$emptyrange = false; // note this even if no jobs were created

			if ( $this->namespaces && !in_array( $row->smw_namespace, $this->namespaces ) ) {
				continue;
			}

			// Find page to refresh, even for special properties:
			if ( $row->smw_title != '' && $row->smw_title{0} != '_' ) {
				$titleKey = $row->smw_title;
			} elseif ( $row->smw_namespace == SMW_NS_PROPERTY && $row->smw_iw == '' && $row->smw_subobject == '' ) {
				$titleKey = str_replace( ' ', '_', DIProperty::findPropertyLabel( $row->smw_title ) );
			} else {
				$titleKey = '';
			}

			if ( $row->smw_subobject !== '' ) {
				// leave subobjects alone; they ought to be changed with their pages
			} elseif ( ( $row->smw_iw === '' || $row->smw_iw == SMW_SQL3_SMWREDIIW ) &&
				$titleKey != '' ) {
				// objects representing pages
				// TODO: special treatment of redirects needed, since the store will
				// not act on redirects that did not change according to its records
				$title = Title::makeTitleSafe( $row->smw_namespace, $titleKey );

				if ( $title !== null && !$title->exists() ) {
					$updatejobs[] = $this->newUpdateJob( $title );
				}
			} elseif ( $row->smw_iw == SMW_SQL3_SMWIW_OUTDATED ) { // remove outdated internal object references

				foreach ( $this->store->getPropertyTables() as $proptable ) {
					if ( $proptable->usesIdSubject() ) {
						$db->delete( $proptable->getName(), array( 's_id' => $row->smw_id ), __METHOD__ );
					}
				}

				$db->delete( \SMWSql3SmwIds::tableName, array( 'smw_id' => $row->smw_id ), __METHOD__ );
			} elseif ( $titleKey != '' ) { // "normal" interwiki pages or outdated internal objects -- delete
				$diWikiPage = new DIWikiPage( $titleKey, $row->smw_namespace, $row->smw_iw );
				$emptySemanticData = new SemanticData( $diWikiPage );
				$this->store->updateData( $emptySemanticData );
			}
		}

		$db->freeResult( $res );
	}

	private function findNextIdPosition( &$id, $emptyrange ) {

		$nextpos = $id + $this->iterationLimit;
		$db = $this->store->getConnection( 'mw.db' );

		// nothing found, check if there will be more pages later on
		if ( $emptyrange && $nextpos > \SMWSql3SmwIds::PPBORDERID ) {

			$nextByPageId = (int)$db->selectField(
				'page',
				'page_id',
				"page_id >= $nextpos",
				__METHOD__,
				array( 'ORDER BY' => "page_id ASC" )
			);

			$nextBySmwId = (int)$db->selectField(
				\SMWSql3SmwIds::tableName,
				'smw_id',
				"smw_id >= $nextpos",
				__METHOD__,
				array( 'ORDER BY' => "smw_id ASC" )
			);

			// Next position is determined by the pool with the maxId
			$nextpos = $nextBySmwId != 0 && $nextBySmwId > $nextByPageId ? $nextBySmwId : $nextByPageId;
		}

		$id = $nextpos ? $nextpos : -1;
	}

	private function newUpdateJob( $title ) {
		return new UpdateJob( $title, array( 'pm' => $this->useUpdateJobParseMode ) );
	}

}
