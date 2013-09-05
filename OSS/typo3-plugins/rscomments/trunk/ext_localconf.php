<?php

if (!defined ('TYPO3_MODE')) {
 	//die ('Access denied.');
}

// Installing the plugins
for ($i=1; $i<2; $i++) {
	t3lib_extMgm::addPItoST43($_EXTKEY, 'pi'.$i.'/class.tx_'.$_EXTKEY.'_pi'.$i.'.php', '_pi'.$i, 'list_type', 0);
}

?>
