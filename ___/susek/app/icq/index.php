<?

require("micq.php");

set_time_limit(0);
ignore_user_abort(true);

$mlCQ = new mlCQ();
$mlCQ->start_logging('huge.log');

//$mlCQ->create_identity("me", '123456', 'superduperpassword');
$mlCQ->create_identity("me", '463196649', '123456');
$mlCQ->connect();
$mlCQ->login();

$mlCQ->set_status('ONLINE', 'DCDISABLED');
$mlCQ->request_offline_messages();
$mlCQ->delete_offline_messages();

function send_multiple(){
	global $icq_nums;
	
}

$mess = 'Session started at '.date('Y/m/d H:i:s').' from ip '.$_SERVER['REMOTE_ADDR'];
$icq_nums = array(2687473);
// sample send message

foreach ($icq_nums as $icq_num_instance)
	$mlCQ->send_message($icq_num_instance, $mess);
	
//$mlCQ->send_message(194247639, $mess);
//$mlCQ->send_message(67856465, $mess);
//$mlCQ->send_message(9591111, $mess);
//$mlCQ->send_message(6118945, $mess);



$still_there = true; $code = '';
while ($still_there) {
	
	$out_queue 	= "send.queue";
	$out_log	= "send.log";
	if(file_exists($out_queue)){
		
		$tosend = file_get_contents($out_queue);
		unlink($out_queue);

		$out_fp = fopen($out_log,'a+');
		fwrite($out_fp, $tosend);
		fclose($out_fp);

		$msg = explode('|',$tosend."\n");
		$mlCQ->send_message($msg[0], $msg[1]);
		
	}
    
	if (!is_resource($mlCQ->sock) || feof($mlCQ->sock))
        $still_there = false;
    if (!$code) sleep(1);    // delay if no packet received last time
    $code = $mlCQ->dance_for('me');
    switch ($code) {
        case 7:
        $msg = $mlCQ->id['in_messages']['last'];
        // new message received
        // more code here
            break;
        case 8:
        $msg = $mlCQ->id['in_messages']['last'];
        // offline message received
        // more code here
            break;
        default:
        // ...
            break;
    }
}
?>