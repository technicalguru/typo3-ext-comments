<?php

require_once(t3lib_extMgm::extPath('rsextbase').'res/class.tx_rsextbase_database.php');


class tx_rscomments_database extends tx_rsextbase_database {

	function getComments($def) {
		// Check if there is such a record and it is enabled/visible
		$origRecord = $this->getRecordByUid($def['table'], $def['uid']);
		if (isset($origRecord['deleted']) && $origRecord['deleted']) return array();
		if (isset($origRecord['hidden']) && $origRecord['hidden']) return array();
		
		// Now get the comments
		return $this->selectComments($def['storagePid'], $def['prefix'], $def['table'].'_'.$def['uid']);
	}
	
	function selectComments($storagePid, $prefix, $pid) {
		$where = "pid=$storagePid AND external_ref='$pid' AND external_prefix='$prefix' AND hidden=0 AND deleted=0";
		return $this->getRecords('tx_rscomments_comments', $where, 'crdate ASC');
	}
	
	function createComment($def, $record, $url) {
		$record['pid'] = $def['storagePid'];
		$record['external_ref'] = $def['table'].'_'.$def['uid'];
		$record['external_prefix'] = $def['prefix'];
		
		
		$rc = $this->createRecord('tx_rscomments_comments', $record);
		$this->ensureUrl($def['storagePid'], $record['external_ref'], $url);
		return $rc;
	}
	
	function ensureUrl($pid, $externalRef, $url) {
		$where = "pid=$pid AND external_ref='$externalRef'";
		$record = $this->getRecord('tx_rscomments_urllog', $where);
		
		if (!$record['uid']) {
			// Create the record
			$time = time();
			$create = array (
				'pid' => $pid,
				'crdate' => $time,
				'tstamp' => $time,
				'deleted' => 0,
				'external_ref' => $externalRef,
				'url' => $url,
			);
			$rc = $this->createRecord('tx_rscomments_urllog', $create);
		} else {
			if ($record['deleted'] || ($record['url'] != $url)) {
				$update = array(
					'deleted' => 0,
					'url' => $url,
				);
				$rc = $this->updateRecord('tx_rscomments_urllog', $record['uid'], $update);
			}
		}
		return $rc;
	}
}

?>