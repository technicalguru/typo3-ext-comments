<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

/**************************************************/
// Comments table
$TCA['tx_rscomments_comments'] = array(
        'ctrl' => array (
                'title' => 'LLL:EXT:rscomments/locallang_db.xml:tx_rscomments_comments',
                'label' => 'content',
                'tstamp' => 'tstamp',
                'crdate' => 'crdate',
                'sortby' => 'crdate',
                'default_sortby' => ' ORDER BY crdate DESC',
                'delete' => 'deleted',
                'enablecolumns' => array (
                        'disabled' => 'hidden',
                ),
                'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
                'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/icon_rscomments.gif',
                //'type' => 'approved',
                'typeicon_column' => 'approved',
                'typeicons' => array(
                        '0' => t3lib_extMgm::extRelPath($_EXTKEY) . 'res/icons/icon_rscomments_not_approved.gif',
                        '1' => t3lib_extMgm::extRelPath($_EXTKEY) . 'res/icons/icon_rscomments.gif',
                ),
        )
);
t3lib_extMgm::allowTableOnStandardPages('tx_rscomments_comments');
t3lib_extMgm::addToInsertRecords('tx_rscomments_comments');

// URL log table
$TCA['tx_rscomments_urllog'] = array(
        'ctrl' => array (
                'title' => 'LLL:EXT:rscomments/locallang_db.xml:tx_rscomments_urllog',
                'label' => 'external_ref',
                'tstamp' => 'tstamp',
                'crdate' => 'crdate',
                'sortby' => 'external_ref',
                'delete' => 'deleted',
                'hideTable' => true,
                'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
                'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'res/icons/icon_urllog.gif',
        )
);
t3lib_extMgm::allowTableOnStandardPages('tx_rscomments_urllog');
t3lib_extMgm::addToInsertRecords('tx_rscomments_urllog');

/**************************************************/


t3lib_div::loadTCA('tt_content');
// Installing all plugins
for ($i=1; $i<2; $i++) {
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi'.$i]='layout,select_key';
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi'.$i]='pi_flexform';
	t3lib_extMgm::addPlugin(array('LLL:EXT:'.$_EXTKEY.'/pi'.$i.'/locallang.xml:tt_content.list_type', $_EXTKEY.'_pi'.$i),'list_type');
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi'.$i, 'FILE:EXT:'.$_EXTKEY.'/pi'.$i.'/flexform.xml');
}

/**************************************************/

t3lib_extMgm::addStaticFile($_EXTKEY,'static/','RS Comments Template');



?>