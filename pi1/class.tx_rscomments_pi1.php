<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Administrator <jrt@ralph-schuster.eu>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('rscomments').'res/class.tx_rscomments_pibase.php');

/**
 * Plugin 'JRT Team Member List' for the 'rscomments' extension.
 *
 * @author	Administrator <typo3@ralph-schuster.eu>
 * @package	TYPO3
 * @subpackage	tx_rscomments
 */
class tx_rscomments_pi1 extends tx_rscomments_pibase {
	
	var $relPath       = 'pi1';
	var $prefixId      = 'tx_rscomments_pi1';
	var $scriptRelPath = 'pi1/class.tx_rscomments_pi1.php';
	
	/**
	 * Returns the HTML content.
	 */
	function getPluginContent() {
		$this->setConfiguration('externalPrefix');
		$this->setConfiguration('uidFallbackEnabled');
		
		if ($this->config['mode'] == 'DEFAULT') {
			$content = $this->getDefaultView();
		}	
		return $this->pi_wrapInBaseClass($content);
	}

	function getDefaultView() {
		$template = $this->getSubTemplate('VIEW_COMMENTS');
		$rc = '';
		
		$comments = $this->getComments();

		if (is_array($comments)) {
			if ($this->getGPvar('form', 'submit')) $this->handleNewComment();
			
			$record = array(
				'comment_count' => count($comments),
				'comments' => $comments,
			);
		
			$rc = $this->fillTemplate($template, 'list', $record);
			
		}
		return $rc;
	}
	
	function getComments() {		
		$def = $this->getExternalPrefixDefinition();
		
		if ($def) {
			return $this->db->getComments($def);
		}
		
		return false;
	}
	
	function getCommentsMarkers(&$caller, $template, &$singleMarkers, &$subpartMarkers, $wrapped, $mode, $info) {
		$rc = '';

		if ($info['comment_count'] > 0) {
			$idx = 0;
			foreach ($info['comments'] AS $comment) {
				$idx++; $comment['index'] = $idx;
				$rc .= $caller->fillTemplate($template, 'comment', $comment);
			}
		}
		$subpartMarkers['###COMMENTS###'] = $rc;
	}
	
	function getExternalPrefixDefinition() {
		$prefix = 'cms';
		$table  = 'pages';
		$uid    = $this->id;
		$storagePid = $this->config['storagePid'];
		
		if ($this->config['externalPrefix']) {
			$prefix = $this->config['externalPrefix'];
			if ($this->config['config.']['prefixToTableMap.'][$prefix]) {
				$table  = $this->config['config.']['prefixToTableMap.'][$prefix];
			}
			
			$uidParam  = 'showUid';
			if ($this->config['config.']['showUidMap.'][$prefix]) {
				$uidParam  = $this->config['config.']['showUidMap.'][$prefix];
			}
			
			$ar = t3lib_div::_GP($prefix);
			$uid = is_array($ar) ? intval($ar[$uidParam]) : false;
			
			// Fallback to UID param
			if (!$uid) {
				$uid = is_array($ar) ? intval($ar['uid']) : false;
			}
			
			// fallback to global UID if not available yet
			if (!$uid && $this->config['uidFallbackEnabled']) {
				$uid = intval(t3lib_div::_GP('uid'));
			}
		}
		
		if ($uid) return array(
			'prefix' => $prefix,
			'table'  => $table,
			'uid'    => $uid,
			'storagePid' => $storagePid,
		);
		
		return false;
	}
	
	function getWriteCommentsMarkers(&$caller, $template, &$singleMarkers, &$subpartMarkers, $wrapped, $mode, $info) {
		// TODO: url-log must contain a marker to switch off comments for this entry
		$rc = '';
		if (true) {
			$url = $GLOBALS['_SERVER']['REQUEST_URI'];
			if (strpos($url, '#')) $url = substr($url, 0, strpos($url, '#'));
			$record = $this->db->getUser();
			$def = $this->getExternalPrefixDefinition();
			$record['prefix_uid'] = $def['uid'];
			$record['original_url'] = $url;
			$rc = $this->fillTemplate($template, 'form', $record);
		}
		$subpartMarkers['###WRITE_COMMENTS###'] = $rc;
	}
	
	function handleNewComment() {
		$def = $this->getExternalPrefixDefinition();
		$uid  = $this->getGPvar('form', 'prefix_uid');
		$name = $this->getGPvar('form', 'name');
		$content = $this->getGPvar('form', 'content');
		$url  = $this->getGPvar('form', 'original_url');
		if (strpos($url, '#')) $url = substr($url, 0, strpos($url, '#'));
		
		$feuser = $this->db->getUser();
		
		if (!$content) return;
		
		$time = time();
		$record = array(
			'crdate' => $time,
			'tstamp' => $time,
			'name' => $name,
			'content' => $content,
			'deleted' => 0,
			'hidden' => 0,
			'approved' => 1,
			'remote_addr' => $GLOBALS['_SERVER']['REMOTE_ADDR'],
			'feuser' => $feuser['uid'] ? $feuser['uid'] : 0,
		);
		
		$def['uid'] = $uid;
		$cuid = $this->db->createComment($def, $record, $url);
		$this->redirect($url.'#comment-'.$cuid);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rscomments/pi1/class.tx_rscomments_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rscomments/pi1/class.tx_rscomments_pi1.php']);
}

?>
