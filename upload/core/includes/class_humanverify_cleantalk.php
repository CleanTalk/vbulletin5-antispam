<?php
if (!class_exists('vB_HumanVerify_Abstract')){
    include_once(dirname(__FILE__).'/class_humanverify.php');
}

class vB_HumanVerify_CleanTalk extends vB_HumanVerify_Abstract {

    function __construct(&$registry = null) {
        if ($registry)
	       parent::__construct($registry);
    }

    function get_data(){
	return "class humanverify_cleantalk get data";
    }

    function verify_token($input) {
	global $vbulletin;

        if ( isset($vbulletin->scriptpath) ) {
            $s2 = substr($vbulletin->scriptpath, 0, 26);
            if (strpos($vbulletin->scriptpath, '/registration/registration') === false) return true;
        } else {
            return true;
        }

	$result = true;

        $aUser = array();
        $aUser['type'] = 'register';
        $aUser['sender_email'] = isset($_POST['email']) ? $_POST['email'] : '';
        $aUser['sender_nickname'] = isset($_POST['username']) ? $_POST['username'] : '';

        if ( !class_exists('CleantalkAPI') ) {
            include_once(dirname(__FILE__).'/cleantalkapi.php');
        }
        $aResult = CleantalkAPI::CheckSpam($aUser, TRUE); // Send email too

        if (isset($aResult) && is_array($aResult)) {
            if ($aResult['errno'] == 0) {
                if ($aResult['allow'] == 0) {
                    // Spammer - fill errors
                    // Note: 'stop_queue' is ignored in user checking
                    if (preg_match('//u', $aResult['ct_result_comment'])) {
                                $comment_str = preg_replace('/^[^\*]*?\*\*\*|\*\*\*[^\*]*?$/iu', '', $aResult['ct_result_comment']);
                                $comment_str = preg_replace('/<[^<>]*>/iu', '', $comment_str);
                    } else {
                                $comment_str = preg_replace('/^[^\*]*?\*\*\*|\*\*\*[^\*]*?$/i', '', $aResult['ct_result_comment']);
                                $comment_str = preg_replace('/<[^<>]*>/i', '', $comment_str);
                    }

#                    if ($vbulletin->options['cleantalk_log_onoff']) {
#                	$log_str = 'Username: '.$vbulletin->GPC['username'].', email: '.$vbulletin->GPC['email'].'. '.$comment_str;
#                    	$vbulletin->db->query_write("INSERT INTO " . TABLE_PREFIX . "moderatorlog (dateline, action, threadtitle, product, ipaddress) VALUES ('".TIMENOW."', '".$vbulletin->db->escape_string($log_str)."', '', 'cleantalk', '".$vbulletin->db->escape_string(IPADDRESS)."')");
#                    }

		    $this->error = $comment_str;
#                    $this->error = 'spam!';
    		    $result = false;
                }
            }
        }
        unset($aUser);
        unset($aResult);
        return $result;
    }

}// class vB_HumanVerify_CleanTalk
