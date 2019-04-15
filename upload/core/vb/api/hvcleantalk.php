<?php
class vB_Api_Hvcleantalk extends vB_Api
{
    public function fetchHvcleantalk(){
	global $vbulletin;
        
	$human_verify = '';

        if ( isset($vbulletin->scriptpath) ) {
            $s2 = substr($vbulletin->scriptpath, 0, 9);
            if ($s2 != '/register') return $human_verify;
        } else {
            return $human_verify;
        }

	$vboptions = vB::getDatastore()->getValue('options');
	if (!empty($vbulletin->options['cleantalk_key'])) {
                if ( !class_exists('CleantalkAPI') ) {
                    include_once(dirname(__FILE__).'/../../includes/cleantalkapi.php');
                }
                if(class_exists('CleantalkAPI')){
                    $human_verify .= CleantalkAPI::FormAddon('autodetect');
                }
	}
	return $human_verify;
    }
}
