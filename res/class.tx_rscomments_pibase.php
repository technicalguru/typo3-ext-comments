<?php

require_once(t3lib_extMgm::extPath('rsextbase').'res/class.tx_rsextbase_pibase.php');
require_once(t3lib_extMgm::extPath('rscomments').'res/class.tx_rscomments_database.php');

class tx_rscomments_pibase extends tx_rsextbase_pibase {

	var $extKey        = 'rscomments';	// The extension key.
	
	/**
	 * Always call this function before starting
	 * @param $conf configuration
	 */
	function init($config) {
		parent::init($config);
	
		$this->setConfiguration('storagePid');
	}

	/**
	 * Creates the database object
	 */
	function createDatabaseObject() {
		$this->db = t3lib_div::makeInstance('tx_rscomments_database');
		$this->db->init($this);
	}
}

?>