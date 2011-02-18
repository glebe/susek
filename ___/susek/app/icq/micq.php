<?php

/*
 *  mlCQ library v0.4
 *  created by Sergey Danyushin aka I)estym
 *  last-modified: 2006.23.10
 *  mailto: sergey [atatat] destym.ru
 *  ICQ #1991216
 */

class mlCQ {
	
	//================================================================================
	//   Class variables
	//================================================================================
	
	var $debug = 1;	//100		// debug level: 0 - no debug (outputs fatal errors only), 1 - errors + basic messages
								// 10 - all errors & messages + last FLAP dump, 100 - all errors, messages & FLAP dumps
	var $logging = false;		// if logging is enabled, this will contain the resourse pointer to log file
	var $identities = array();	// array that contains all used profiles ($name => $profile_array)
	var $id = null;				// pointer to the current identity
	var $id_name = '';			// current identity's name
	var $sock = null;			// pointer to the current identity's socket resource
	var $flaps = null;			// pointer to the current identity's FLAPs array
	var $snacs = null;			// pointer to the current identity's SNACs array
	var $tlvs = null;			// pointer to the current identity's TLVs array
	var $tbt = null;			// pointer to TLV-by-type array
	var $sock_errno = null;		// pointer to current socket's error number variable
	var $errmsg = array();		// array of errror messages ($code => $message)
	var $meta_seqnum = 0;		// meta-requests' sequence number
	var $icbm_req_id = 0;		// ICBM service SNACs' request number
	var $keep_alive_time = 0;	// last keep-alive packet sent timestamp
	var $no_output = false;		// if set to true, no packets will be sent; instead, they will be queued in the 'out_queue' array
	var $settings = array(
		'server' 		=> 'login.icq.com',	// ICQ login server, you can use IP address here
		//'server' 		=> 'ibucp-vip-m.blue.aol.com',
		'port'	 		=> 5190,			// server's port
		'max_flaps'		=> 10,				// how many FLAPs to keep in the $flaps array
		'max_snacs'		=> 10,				// how many SNACs to keep in the $flaps array
		'keep_alive'	=> 1				// send connection keep-alive packets?
	);
	var $const = array(
		// TLV-types
		'TYPE_UIN'					=> 1,		'TLV_1'	 => 'string',
		'TYPE_DATA'					=> 2,		'TLV_2'	 => 'array',
		'TYPE_CLIENT_ID_STRING'		=> 3,		'TLV_3'	 => 'string',
		'TYPE_ERROR_DESCR_URL'		=> 4,		'TLV_4'	 => 'string',
		'TYPE_BOS_SERVER'			=> 5,		'TLV_5'	 => 'string',
		'TYPE_AUTH_COOKIE'			=> 6,		'TLV_6'	 => 'string',
		'TYPE_AUTH_ERROR_CODE'		=> 8,		'TLV_8'	 => 'word',
		'TYPE_DC_INFO'				=> 12,		'TLV_12' => 'array',
		'TYPE_CLIENT_COUNTRY'		=> 14,		'TLV_14' => 'string',
		'TYPE_CLIENT_LANG'			=> 15,		'TLV_15' => 'string',
		'TYPE_DISTRIB_NUMBER'		=> 20,		'TLV_20' => 'dword',
		'TYPE_CLIENT_ID'			=> 22,		'TLV_22' => 'word',
		'TYPE_CLIENT_MAJOR_VER'		=> 23,		'TLV_23' => 'word',
		'TYPE_CLIENT_MINOR_VER' 	=> 24,		'TLV_24' => 'word',
		'TYPE_CLIENT_LESSER_VER'	=> 25,		'TLV_25' => 'word',
		'TYPE_CLIENT_BUILD_NUM'		=> 26,		'TLV_26' => 'word',
		'TYPE_MSG_TEXT'				=> 257,		'TLV_257' => 'string',
		'TYPE_MSG_CAPABILITIES'		=> 1281,	'TLV_1281' => 'array',
		// status constants & status flags
		'STATUS_ONLINE'				=> 0,		'ST0'	 => 'Online',
		'STATUS_AWAY'				=> 1,		'ST1'	 => 'Away',
		'STATUS_DND'				=> 2,		'ST2'	 => 'DND',
		'STATUS_NA'					=> 4,		'ST4'	 => 'N/A',
		'STATUS_OCCUPIED'			=> 16,		'ST16'	 => 'Occupied',
		'STATUS_FREE4CHAT'			=> 32,		'ST32'	 => 'Free For Chat',
		'STATUS_INVISIBLE'			=> 256,		'ST256'	 => 'Invisible',
		'STATUSFLAG_WEBAWARE'		=> 1,		'SF1'	 => 'Web-Aware',
		'STATUSFLAG_SHOWIP'			=> 2,		'SF2'	 => 'Show IP',
		'STATUSFLAG_BIRTHDAY'		=> 8,		'SF8'	 => 'Birthday',
		'STATUSFLAG_WEBFRONT'		=> 32,		'SF32'	 => 'Web-Front',
		'STATUSFLAG_DCDISABLED'		=> 256,		'SF256'	 => 'DC Disabled',
		'STATUSFLAG_DCAUTH'			=> 4096,	'SF4096' => 'DC upon authorization',
		'STATUSFLAG_DCCONT'			=> 8192,	'SF8192' => 'DC only with users from contact-list',
		// SNAC request-IDs
		'REQID_ICBMPARAMSSET'		=> 3,
		'REQID_ICBMPARAMS'			=> 4,
		'REQID_SERVERRATES'			=> 5,
		'REQID_SETLOCATIONINFO'		=> 6,
		'REQID_SETSTATUS'			=> 7,
		'REQID_MESSAGESEND'			=> 8,
		'REQID_CLIENTREADY'			=> 9,
		'REQID_ONLINECHECK'			=> 10,
		'REQID_GETOFFLINEMESSAGES'	=> 11,
		'REQID_DELOFFLINEMESSAGES'	=> 12,
		'REQID_AUTHREPLY'			=> 13,
		'REQID_FULLUSERINFO'		=> 14,
		'REQID_SHORTUSERINFO'		=> 15,
		'REQID_MESSAGESENDACK'		=> 16
	);
	
	function init() {
		//yet to be written =)
	}
	
	//================================================================================
	//   Profile handling functions
	//================================================================================
	
	function create_identity($name, $uin, $pass) {
		$this->identities[$name] = array(
			'uin' 			=> $uin,			// UIN for this identity
			'password'		=> $pass,			// password
			'out_seqnum' 	=> rand(1, 20000),	// randomly generated out sequence number
			'in_seqnum'		=> 0,				// this number is yet to be received from server
			'buddy_list'	=> array(),			// array of buddies ($uin => $buddy_data_array)
			'uinfo'			=> array(),			// array of requested "userinfos"
			'flaps'			=> array(),			// stack array of sent/received FLAPs
			'snacs'			=> array(),			// stack array of sent/received SNACs
			'tlvs'			=> array(),			// stack array of sent/received TLVs
			'tlvs_by_type'	=> array(),			// array of pointers ($type_id => &$element_of_this_type)
			'in_messages'	=> array(),			// this holds the incoming messages ($uin => $msg_array)
			'out_messages'	=> array(),			// this holds the sent messages ($to_uin => $msg_array)
			'general_notes'	=> array(),			// this array is for general script-generated messages
			'out_queue'		=> array(),			// this array is for general script-generated messages
			'auto_response'	=> '',				// if not empty, specifies the auto-respond message
			'state'			=> 0,				// connection state: 0 - disconnected, 1 - connected, 2 - error
			'no_conf_snac'	=> array(),			// this is not a permanent value. Contains UINs that don't need message-ack SNACs to be sent
			'socket'		=> array(
				'res'	 => null,				// socket's resource pointer
				'errno'	 => 0,					// error code
				'errstr' => ''					// error string
				)			
		);
		if ($this->id === null)
			$this->select_identity($name);
	}
	
	function select_identity($name) {
		if (array_key_exists($name, $this->identities))
			$this->id = &$this->identities[$name];
		else
			$this->fatal_error("The identity '{$name}' does not exist", __FILE__, __LINE__);
		// several shortcuts
		$this->sock = &$this->identities[$name]['socket']['res'];
		$this->flaps = &$this->identities[$name]['flaps'];
		$this->snacs = &$this->identities[$name]['snacs'];
		$this->tlvs = &$this->identities[$name]['tlvs'];
		$this->tbt = &$this->identities[$name]['tlvs_by_type'];
		$this->sock_errno = &$this->identities[$name]['socket']['errno'];
		$this->id_name = $name;
	}
	
	//================================================================================
	//   Connection handling functions
	//================================================================================
	
	function connect($server = '', $port = 0) {
		if (!$server)
			$server = $this->settings['server'];
		if (!$port)
			$port = $this->settings['port'];
		if (!$this->sock = @fsockopen($server, $port, $this->sock_errno, $this->id['socket']['errstr'])) {
			$this->error("Could not open socket, error code: {$this->sock_errno}, error string : {{$this->id['socket']['errstr']}}", __FILE__, __LINE__);
			return false;
		} else {
			stream_set_timeout($this->sock, 3);
			return 1;
		}
	}
	
	function close() {
		if (is_resource($this->sock)) 
			fclose($this->sock);
	}
	
	//================================================================================
	//   This is what we are here for
	//================================================================================
	
	function login($status = 0) {
		if(!$this->sock) return false;
			$this->debug_msg('Login sequence started', 1);
		if(!$this->read_FLAP()) {
			$this->debug_msg('SRV_HELLO not received. Try reconnecting in a few minutes.', 1);
			return false;
		}
		$this->clear_TLVs();
		$this->push_TLV('UIN', $this->id['uin']);
		$this->push_TLV('DATA', $this->roast_pass($this->id['password']));
		$this->push_TLV('CLIENT_ID_STRING', 'ICQBasic');
		$this->push_TLV('CLIENT_ID', 266);
		$this->push_TLV('CLIENT_MAJOR_VER', 20);
		$this->push_TLV('CLIENT_MINOR_VER', 34);
		$this->push_TLV('CLIENT_LESSER_VER', 0);
		$this->push_TLV('CLIENT_BUILD_NUM', 2321);
		$this->push_TLV('DISTRIB_NUMBER', 1085);
		$this->push_TLV('CLIENT_LANG', 'en');
		$this->push_TLV('CLIENT_COUNTRY', 'us');
		/*$this->push_TLV('CLIENT_ID_STRING', 'ICQ Inc. - Product of ICQ .(TM).2003b.5.56.1.3916.85');
		$this->push_TLV('CLIENT_ID', 266);
		$this->push_TLV('CLIENT_MAJOR_VER', 5);
		$this->push_TLV('CLIENT_MINOR_VER', 37);
		$this->push_TLV('CLIENT_LESSER_VER', 1);
		$this->push_TLV('CLIENT_BUILD_NUM', 3728);
		$this->push_TLV('DISTRIB_NUMBER', 85);
		$this->push_TLV('CLIENT_LANG', 'en');
		$this->push_TLV('CLIENT_COUNTRY', 'us');*/
			$this->debug_msg('Sending CLI_IDENT...');
		$this->send_FLAP(1, pack('N', 1) . $this->pack_all_TLVs());
			$this->debug_msg('Retrieving server response...');
		if(!$this->read_FLAP(false, 30)) {
			$this->debug_msg('Server-response invalid. Try reconnecting in a few minutes.', 1);
			return false;
		}
		fwrite($this->sock, pack('ccnn', 0x2A, 1, $this->outseq(), 0));
			$this->debug_msg('Closing connection...');
		$this->close();
		$this->unpack_all_TLVs($this->flaps[0]['raw']['body']);
		if (isset($this->tbt[8])) {
			$this->error('Authorization failed, error code: ' . ord($this->tbt[8]['value']) . ". For more information try visiting <a href=\"{$this->tbt[4]['value']}\">this page</a>", __FILE__, __LINE__);
			return false;
		}
		if (isset($this->tbt[5]) && isset($this->tbt[6])) {
			$this->debug_msg('BOS server address & cookie received, proceeding...');
		} else {
			$this->error('BOS server address & cookie NOT received, connection aborted', __FILE__, __LINE__);
			return false;
		}
		$bos = explode(':', $this->tbt[5]['value']);
		$cookie = $this->tbt[6]['value'];
		$this->clear_TLVs();
			$this->debug_msg('Connecting to BOS server...');
		if (!$this->connect($bos[0], $bos[1]))
			return false;
		if(!$this->read_FLAP()) {
			$this->debug_msg('SRV_HELLO not received. Try reconnecting in a few minutes.', 1);
			return false;
		}
		$this->push_TLV('AUTH_COOKIE', $cookie);
			$this->debug_msg('Sending CLI_COOKIE...');
		$this->send_FLAP(1, pack('N', 1) . $this->pack_all_TLVs());
			$this->debug_msg('Retrieving server response...');
		if(!$this->read_FLAP()) {
			$this->debug_msg('Server response not received. Try reconnecting in a few minutes.', 1);
			return false;
		}
			$this->debug_msg('Sending server rates request...');
		$this->send_FLAP(2, $this->make_SNAC(1, 6));
		if(!$this->read_FLAP()) {
			$this->debug_msg('Server rates response not received in a timely manner.', 1);
		} else {
			$this->push_from_FLAP($this->flaps[0]);
			$num_classes = ord($this->snacs[0]['raw']['body']{1}) + (256 * ord($this->snacs[0]['raw']['body']{0}));
			$tmp = substr($this->snacs[0]['raw']['body'], 2 + (30 * $num_classes)); 
			$i = 0; $group_ids = array();
			while ($i<strlen($tmp)) {
				$group_ids[] = $this->word_val($tmp{$i} . $tmp{$i+1});
				$i += 2;
				$num_pairs = $this->word_val($tmp{$i} . $tmp{$i+1});
				$i += 2 + ($num_pairs * 4);
			}
			$this->debug_msg('Sending rates acknowledgement to make server happy...');
			$this->send_FLAP(2, $this->make_SNAC(1, 8, array('group_ids' => $group_ids)));
		}
			$this->debug_msg('Requesting ICBM service parameters...');
		$this->send_FLAP(2, $this->make_SNAC(4, 4));
		$this->read_FLAP(true);
			$this->debug_msg('Setting ICBM service parameters for channel #1...');
		$this->send_FLAP(2, $this->make_SNAC(4, 2, array('channel' => 1, 'msize' => 8000)));
			$this->debug_msg('Setting ICBM service parameters for channel #2...');
		$this->send_FLAP(2, $this->make_SNAC(4, 2, array('channel' => 2, 'msize' => 8000)));
			$this->debug_msg('Setting ICBM service parameters for channel #4...');
		$this->send_FLAP(2, $this->make_SNAC(4, 2, array('channel' => 4, 'msize' => 8000)));
		$this->read_FLAP(true);
			$this->debug_msg('Sending client capabilities list...');		
		$this->send_FLAP(2, $this->make_SNAC(2, 4));
		if (!is_integer($status))
			if (isset($this->const['STATUS_' . strtoupper($status)])) {
				$status = $this->const['STATUS_' . strtoupper($status)];
			} else {
				$this->error('Unknown status: ' . $status, __FILE__, __LINE__);
				$status = 0;
			}
			$this->debug_msg('Sending DC_INFO & STATUS SNAC...');
		$this->send_FLAP(2, $this->make_SNAC(1, 30, array('status' => $status, 'statusflags' => 0, 'dc_type' => 0)));
		$this->read_FLAP(true);
			$this->debug_msg('Sending CLI_READY SNAC...');
		$this->send_FLAP(2, $this->make_SNAC(1, 2));
		$this->read_FLAP(true);
			$this->debug_msg('You are now logged in', 1);
		$this->keep_alive_time = time();
		return true;
	}
	
	function migrate($mg_snac) {
		$num = $this->word_val($mg_snac['raw']['body']);
		$this->clear_TLVs();
		$this->unpack_all_TLVs(substr($mg_snac['raw']['body'], 2 + (2 * $num)));
		if (!isset($this->tbt[5]) || !isset($this->tbt[6])) {
			$this->error('New BOS server address & cookie NOT received, migration failed', __FILE__, __LINE__);
			return 0;
		}
		$this->close();
		$this->no_output = false;
		$bos = explode(':', $this->tbt[5]['value']);
		$cookie = $this->tbt[6]['value'];
		$this->clear_TLVs();
			$this->debug_msg('Connecting to new BOS server...');
		if (!$this->connect($bos[0], $bos[1])) {
			$this->error('Could not connect to new BOS server. Connection lost.', __FILE__, __LINE__);
			return false;
		}
		if (!$this->read_FLAP(true)) {
			sleep(1);
			$this->read_FLAP();
		}
		$this->push_TLV('AUTH_COOKIE', $cookie);
			$this->debug_msg('Sending CLI_COOKIE...');
		$this->send_FLAP(1, pack('N', 1) . $this->pack_all_TLVs());
			$this->debug_msg('The migration sequence has finished.', 1);
		return true;		
	}
	
	function set_status($status, $statusflags = 0) {
		if (is_array($status)) {
			$tmp = 0;
			foreach ($status as $flag) {
				if (isset($this->const['STATUS_' . strtoupper($flag)])) {
					$tmp += $this->const['STATUS_' . strtoupper($flag)];
				} else {
					$this->error('Unknown status: ' . $flag, __FILE__, __LINE__);
				}
			}
			$status = $tmp;
		} elseif (!is_integer($status)) {
			if (isset($this->const['STATUS_' . strtoupper($status)])) {
				$status = $this->const['STATUS_' . strtoupper($status)];
			} else {
				$this->error('Unknown status: ' . $status, __FILE__, __LINE__);
			}
		}
		if ($statusflags) {
			if (is_array($statusflags)) {
				$tmp = 0;
				foreach ($statusflags as $flag) {
					if (isset($this->const['STATUSFLAG_' . strtoupper($flag)])) {
						$tmp += $this->const['STATUSFLAG_' . strtoupper($flag)];
					} else {
						$this->error('Unknown status flag: ' . $flag, __FILE__, __LINE__);
					}
				}
				$statusflags = $tmp;
			} elseif (!is_integer($statusflags)) {
				if (isset($this->const['STATUSFLAG_' . strtoupper($statusflags)])) {
					$statusflags = $this->const['STATUSFLAG_' . strtoupper($statusflags)];
				} else {
					$this->error('Unknown status flag: ' . $statusflags, __FILE__, __LINE__);
				}
			}
		}
		$this->send_FLAP(2, $this->make_SNAC(1, 30, array('status' => $status, 'statusflags' => $statusflags)));
	}
	
	function query_status($uin) {
		$this->send_FLAP(2, $this->make_SNAC(2, 21, array('uin' => $uin, 'req_flags' => 5)));
		if (!array_key_exists($uin, $this->id['buddy_list']))
			$this->id['buddy_list'][$uin] = array();
	}
	
	function request_full_userinfo($uin) {
		$this->send_FLAP(2, $this->make_SNAC(21, 2, array('type' => 'user_fullinfo','uin' => $uin)));
	}
	
	function request_short_userinfo($uin) {
		$this->send_FLAP(2, $this->make_SNAC(21, 2, array('type' => 'user_shortinfo','uin' => $uin)));
	}
	
	function request_offline_messages() {
		$this->debug_msg('Sending offline messages request...');
		$this->send_FLAP(2, $this->make_SNAC(21, 2, array('type' => 'om_req')));
	}
	
	function delete_offline_messages() {
		$this->debug_msg('Sending delete offline messages request...');
		$this->send_FLAP(2, $this->make_SNAC(21, 2, array('type' => 'om_del')));
	}
	
	function dance_for($identity) {
		if (!$this->sock) {
			$this->log('Why is the socket closed? ~,~', __FUNCTION__, __LINE__);
			return  false;
		}
		if ($this->id_name != $identity)
			$this->select_identity($identity);
		$read_state = $this->read_FLAP(true);
		if ($read_state == 1) {
			if ($this->flaps[0]['header']['channel'] == 4) {
				$this->log('Error-message FLAP received from server: ' . $this->str_hex_dump($this->flaps[0]['raw']['header'] . $this->flaps[0]['raw']['body']), __FUNCTION__, __LINE__);
				$this->close();
				return 100;
			}
			$this->push_from_FLAP($this->flaps[0]);
			$this->trim_arrays();
			switch ($this->snacs[0]['header']['family']) {
				case 1:
					switch ($this->snacs[0]['header']['id']) {
						case 1:
							$this->debug_msg('Server replies with an error-notification SNAC. Errcode dump follows: ' . $this->str_hex_dump($this->snacs[0]['raw']['body']), 1);
							return 60; 
							break;
						case 10:
							$this->debug_msg('Server sends rate-limit warning. Pausing script execution for 3 seconds.', 1);
							sleep(3);
							return 60;
							break;	
						case 11:
							$this->debug_msg('Server initiated the client migration sequence. No packets are being sent.', 1);
							$this->debug_msg('Migration SNAC dump: ' . $this->str_hex_dump($this->snacs[0]['raw']['body']));
							$this->send_FLAP(2, $this->make_SNAC(1, 12, array('req_id' => $this->snacs[0]['header']['req_id'])));
							$this->no_output = true;
							return 60;
							break;
						case 14:
							$this->debug_msg('Received server-resume command. Continuing with the old BOS...', 1);
							$this->no_output = false;
							return 60;
							break;
						case 15:
							$this->debug_msg('Status-change acknowledgement received.');
							return 13;
							break;
						case 18:
							$this->debug_msg('BOS-redirect SNAC received. Hex dump: ' . $this->str_hex_dump($this->snacs[0]['raw']['body']));
							$this->migrate($this->snacs[0]);
							break;
						default:
							$this->log('Unknown SNAC, dump follows: ' . $this->str_hex_dump($this->flaps[0]['raw']['body']), __FUNCTION__, __LINE__);
							return 0;
					}
					break;
				case 2:
					switch ($this->snacs[0]['header']['id']) {
						case 1:
							//if ($this->snacs[0]['header']['req_id'] != $this->const['REQID_ONLINECHECK']) {
							if (!array_key_exists($this->snacs[0]['header']['req_id'], $this->id['buddy_list'])) {
								$this->debug_msg('Server replies with an error-notification SNAC. Errcode dump follows: ' . $this->str_hex_dump($this->snacs[0]['raw']['body']), 1);
								return 60; 
							} else {
								$this->debug_msg("Online check result: recipient {$this->snacs[0]['header']['req_id']} offline.");
								$mytmp = &$this->id['buddy_list'][$this->snacs[0]['header']['req_id']];
								$mytmp['online'] = 0;
								return 71;
							}
							break;
						case 6:
							if (!array_key_exists($this->snacs[0]['header']['req_id'], $this->id['buddy_list'])) {
								return 51;
							} else {
								array_unshift($this->id['uinfo'], $this->parse_user_info($this->snacs[0]));
								$mytmp = &$this->id['buddy_list'][$this->snacs[0]['header']['req_id']];
								$mytmp += $this->parse_user_info($this->snacs[0]);
								$mytmp['online'] = 1;
								$this->debug_msg("Online check result: recipient {$this->snacs[0]['header']['req_id']} online.");
								return 72;
							}
							break;
						default:
							$this->log('Unknown SNAC, dump follows: ' . $this->str_hex_dump($this->flaps[0]['raw']['body']), __FUNCTION__, __LINE__);
							return 0;
					}
					break;
				case 4:
					switch ($this->snacs[0]['header']['id']) {
						case 7:
							$this->debug_msg('Incoming message...');
							$msg = $this->parse_incoming_message($this->snacs[0]);
							$this->debug_msg("Channel: {$msg['channel']}; From: {$msg['uin']}; Text: {$msg['text']}");
							$this->id['in_messages'][$msg['uin']][] = $msg;
							$this->id['in_messages']['last'] = $msg;
							if ($msg['channel'] != 4) {
								$tmpdata = array('channel' => $msg['channel'], 'msg_id1' => $msg['msg_id1'], 'msg_id2' => $msg['msg_id2'], 'uin' => $msg['uin']);
								if (!in_array($msg['uin'], $this->id['no_conf_snac'])) {
									if ($this->id['auto_response']) {
										$tmpdata['text'] = $this->id['auto_response'];
										$tmpdata['mflags'] = 3;
										$tmpdata['channel'] = 2;
									}
									$this->send_FLAP(2, $this->make_SNAC(4, 11, $tmpdata));
								}
							}
							return 7;
							break;
						case 10:
							$this->debug_msg('Missed message warning received.', 1);
							$tmpdata = $this->parse_missed_message_ack($this->snacs[0]);
							array_unshift($this->id['general_notes'], "Missed {$tmpdata['num_messages']} message(s) from {$tmpdata['uin']} on channel {$tmpdata['channel']}. Reason: {$tmpdata['reason']}");
							return 10;
						case 12:
							$this->debug_msg('Message-sent acknowledgement received.');
							return 12;
						default:
							$this->log('Unknown SNAC, dump follows: ' . $this->str_hex_dump($this->flaps[0]['raw']['body']), __FUNCTION__, __LINE__);
							return 0;
					}
					break;
				case 21:
					switch ($this->snacs[0]['header']['id']) {
						case 3:
							if ($this->snacs[0]['header']['req_id'] == $this->const['REQID_GETOFFLINEMESSAGES']) {
								$this->debug_msg('Incoming offline message SNAC...');
								$msg = $this->parse_offline_message($this->snacs[0]);
								if (!$msg) {
									$this->debug_msg('End of offline messages SNAC received.');
									return 12;
								} else {
									$this->debug_msg("From: {$msg['uin']}; Date: " . strftime("%d-%m-%y %H:%M", $msg['time']) . " Text: {$msg['text']}");
									$this->id['in_messages'][$msg['uin']][] = $msg;
									$this->id['in_messages']['last'] = $msg;
									return 8;
								}
							} elseif ($this->snacs[0]['header']['req_id'] == $this->const['REQID_SHORTUSERINFO']) {
								$this->debug_msg('Incoming short user info SNAC...');
								$arr = $this->parse_short_metauserinfo($this->snacs[0]);
								$this->id['uinfo'][] = $arr;
								$this->id['uinfo']['last'] = $arr;
								return 9;
							} else {
								$this->debug_msg('Unsuppoted meta-information response SNAC, dump follows: ' . $this->str_hex_dump($this->snacs[0]['raw']['body']), 1);
								return 60; 								
							}
							break;
						default:
							$this->log('Unknown SNAC, dump follows: ' . $this->str_hex_dump($this->flaps[0]['raw']['body']), __FUNCTION__, __LINE__);
							return 0;
					}
					break;
				default:
					$this->error('Unknown SNAC family: ' . $this->snacs[0]['header']['family'], __FILE__, __LINE__);
					$this->log('Unknown SNAC, dump follows: ' . $this->str_hex_dump($this->flaps[0]['raw']['body']), __FUNCTION__, __LINE__);
					return 0;
					break;
			}
		} elseif ($read_state == 0) {
			// send packets in queue
			if (count($this->id['out_queue']) && !$this->no_output) {
				$flap = array_pop($this->id['out_queue']);
				fwrite($this->sock, $flap['raw']['header'].$flap['raw']['body']);
			} elseif ($this->settings['keep_alive'])
			// or a keep-alive packet
				if (time() - $this->keep_alive_time > 40) {
					$this->keep_alive();
					$this->keep_alive_time = time();
				}
			return false;
		} else {
			return 100;
		}
	}
	
	function send_message($uin, $text) {
		// TODO: auto-message sending
		$this->send_FLAP(2, $this->make_SNAC(4, 6, array('uin' => $uin, 'text' => $text)));
		$this->debug_msg('Message for ' . $uin . ' has been sent. Text: '. $text);
		$this->id['out_messages'][$uin][] = array (
			'uin'	=> $uin,
			'time'	=> time(),
			'text'	=> $text
		);
	}
	
	function auth_reply($uin, $result, $reason = '') {
		$this->send_FLAP(2, $this->make_SNAC(19, 26, array('uin' => $uin, 'auth_given' => $result, 'reason' => $reason)));
	}
	
	//================================================================================
	//   FLAP processing routines
	//================================================================================
	
	function read_FLAP($silent = false, $timeout = 1) {
		if (!feof($this->sock)) {
			$tmp = time();
			while (!($flap['raw']['header'] = fread($this->sock, 6)) && time() - $tmp < $timeout) {
				sleep(1);
			}
			if (strlen($flap['raw']['header']) <> 6) {
				if (!$silent) {
					$this->error("Invalid FLAP header length", __FILE__, __LINE__);
				    $this->hex_dump($flap['raw']['header']);
				}
				return 0;
			}
            $flap['header'] = unpack('c1star/c1channel/n1seqnum/n1size', $flap['raw']['header']);
            if ($flap['header']['star'] != 0x2A) {
				if (!$silent) {
					$this->error("Invalid first FLAP byte.", __FILE__, __LINE__);
					$this->id['state'] = 2;
				    $this->hex_dump($flap['raw']['header']);
				}
				return false;
			}
            $flap['raw']['body'] = fread($this->sock, $flap['header']['size']); 
            $flap['dir'] = 'in';
            array_unshift($this->flaps, $flap);
            if ($this->debug > 10)
            	$this->view_FLAP($flap);
            return 1;
		} else {
			$this->error("Could not read from closed socket", __FILE__, __LINE__);
			return false;
		}
	}
	
	function make_FLAP($channel, $data) {
		$flap = array();
		$flap['header'] = array (
			'star'		=> '*',
			'channel'	=> $channel,
			'seqnum'	=> $this->outseq(),
			'size'		=> strlen($data)
		);
		$flap['dir'] = 'out';
		$flap['raw']['header'] = pack('ccnn', 0x2A, $flap['header']['channel'], $flap['header']['seqnum'], $flap['header']['size']);
		$flap['raw']['body'] = $data;
		return $flap;
	}
	
	function send_FLAP($channel, $data) {
		$flap = $this->make_FLAP($channel, $data);
		if ($this->no_output) {
			$this->debug_msg('Output locked, packet sent to queue.');
			array_unshift($this->id['out_queue'], $flap);
		} else {
			fwrite($this->sock, $flap['raw']['header'].$flap['raw']['body']);
		}
		if ($this->debug > 10)
			$this->view_FLAP($flap);
	}
	
	//================================================================================
	//   SNAC processing routines
	//================================================================================
	
	function unpack_SNAC($raw) {
		$snac = array(
			'raw'	 => array(
				'header' => substr($raw, 0, 10),
				'body'	 => substr($raw, 10)
			),
			'header' => array()
		);
		$snac['header'] = unpack('n1family/n1id/n1flags/N1req_id', $snac['raw']['header']);
		return $snac;
	}
	
	function push_SNAC($snac) {
		array_unshift($this->snacs, $snac);
	}
	
	function push_from_FLAP($flap) {
		$this->push_SNAC($this->unpack_SNAC($flap['raw']['body']));
	}
	
    function make_SNAC($family, $id, $data = array()) {
    	$out = '';
    	switch ($family) {
    		case 1:
    			switch ($id) {
    				case 2:
    				// "CLI_READY" SNAC
    					$out = pack('nnn', 1, 2, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_CLIENTREADY']); // SNAC request ID
    					// list of families, each containing: family #, version, tool ID, tool version
				        $out .= pack('n*', 1, 3, 272, 650, 2, 1, 272, 650, 3, 1, 272, 650, 21, 1, 272, 650, 4, 1, 272, 650, 6, 1, 272, 650, 9, 1, 272, 650, 10, 1, 272, 650);
    					break;
    				case 6:
    				// "CLI_RATES_REQUEST" SNAC
    					$out = pack('nnn', 1, 6, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_SERVERRATES']); // SNAC request ID
    					break;
    				case 8:
    				// "CLI_RATES_ACK" SNAC
    					$out = pack('nnn', 1, 8, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_SERVERRATES']); // SNAC request ID
    					foreach ($data['group_ids'] as $id) {
    						$out .= pack('n', $id);
    					}
    					break;
    				case 12:
    				// "CLI_MIGRATE_ACK" SNAC
    					$out = pack('nnn', 1, 12, 0); // SNAC header & flags
    					$out .= pack('N', $this->data['req_id']); // SNAC request ID, NOT taken from a constant
    					$out .= pack('n*', 1, 2, 3, 21, 4, 9, 10);
    					break;
    				case 30:
    				// "CLI_SETSTATUS" SNAC
    					$out = pack('nnn', 1, 30, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_SETSTATUS']); // SNAC request ID
    					$tmp = $this->make_TLV(6, pack('nn', $data['statusflags'], $data['status']));
    					$out .= $tmp['raw'];
    					if (isset($data['dc_type'])) {
    						$tmp = $this->make_TLV(12, pack('NNcnNNNNNNn', 0, 0, $data['dc_type'], 8, 0, 0, 1, 0, 0, 0, 0));
    						$out .= $tmp['raw'];
    					}
    					break;
    			}
    			break;
    		case 2:
    			switch ($id) {
    				// "CLI_SETLOCATIONINFO" SNAC
    				case 4:
    					$out = pack('nnn', 2, 4, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_SETLOCATIONINFO']); // SNAC request ID
    					$tmp = "09 46 13 44 4C 7F 11 D1 82 22 44 45 53 54 00 00 "; // this-is-an-ICQ-client capability
    					$tmp .= "09 46 13 49 4C 7F 11 D1 82 22 44 45 53 54 00 00 "; // client supports channel-2 messages
    					$tmp .= "09 46 13 4E 4C 7F 11 D1 82 22 44 45 53 54 00 00 "; // client supports UTF-8 messages
    					$tmp .= "6D 6C 43 51 20 6C 69 62 72 61 72 79 20 30 2E 34 "; // mlCQ signature
    					$tmp .= "56 3F C8 09 0B 6F 41 51 49 50 20 32 30 30 35 61 "; // Identify as QIP
    					//$tmp .= "3F B0 BD 36 AF 3B 4A 60 9E EF CF 19 0F 6A 5A 7F "; // xStatus 'Thinking'
    					//$tmp .= "12 D0 7E 3E F8 85 48 9E 8E 97 A7 2A 65 51 E5 8D "; // xStatus '@'
    					//$tmp .= "F2 E7 C7 F4 FE AD 4D FB B2 35 36 79 8B DF 00 00 "; // Trillian SecureIM
    					//$tmp .= "17 8C 2D 9B DA A5 45 BB 8D DB F3 BD BD 53 A1 0A "; // IM is ICQLite
    					//$tmp .= "09 46 13 4C 4C 7F 11 D1 82 22 44 45 53 54 00 00 "; // ICQ DirectConnect
    					//$tmp .= "97 B1 27 51 24 3C 43 34 AD 22 D6 AB F7 3F 14 92 "; // RTF Messages
    					//$tmp .= "1A 09 3C 6C D7 FD 4E C5 9D 51 A6 47 4E 34 F5 A0 "; // ICQ Xtraz Support
    					//$tmp .= "56 3F C8 09 0B 6F 41 BD 9F 79 42 26 09 DF A2 F3 "; // Typing notifications
    					$tmp = $this->make_TLV(5, $this->dump2bin($tmp));
    					$out .= $tmp['raw'];
    					break;
    				case 21:
    				// "CLI_GETONLINEUSERINFO" SNAC
    					$out = pack('nnn', 2, 21, 0); // SNAC header & flags
    					//$out .= pack('N', $this->const['REQID_ONLINECHECK']); // the old-fashion request ID
    					$out .= pack('N', $data['uin']); // using UIN as SNAC request ID
    					$out .= pack('N', $data['req_flags']); // SNAC request flags
				        $out .= chr(strlen($data['uin']));
				        $out .= $data['uin'];
    					break;
    			}
    			break;
    		case 4:
    			switch ($id) {
    				case 2:
    				// "CLI_ICBM_PARAMS_SET" SNAC
    					$out = pack('nnn', 4, 2, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_ICBMPARAMSSET']); // SNAC request ID
    					if (!isset($data['mflags']))
    						$data['mflags'] = 3;
    					if (!isset($data['msize']))
    						$data['msize'] = 2048;
    					if (!isset($data['interval']))
    						$data['interval'] = 0;
    					$out .= pack("nNnnnnn", $data['channel'], $data['mflags'], $data['msize'], 999, 999, $data['interval'], 0);
    					break;
    				case 4:
    				// "CLI_ICBM_PARAMS_REQUEST" SNAC
    					$out = pack('nnn', 4, 4, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_ICBMPARAMS']); // SNAC request ID
    					break;
    				case 6:
    				// message send SNAC
    					$out = pack('nnn', 4, 6, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_MESSAGESEND']); // SNAC request ID
				        $out .= pack('n*', 0, 0, 0, 0, 1); // message-id & channel
				        $out .= chr(strlen($data['uin']));
				        $out .= $data['uin'];
				        $this->clear_TLVs();
						$this->push_TLV('MSG_CAPABILITIES', chr(1) . chr(1)); // fragments are packed as TLVs
						$this->push_TLV('MSG_TEXT', pack('nn', 0, 0) . $data['text']);
						$tmp = $this->pack_all_TLVs();
						$this->clear_TLVs();
						$this->push_TLV('DATA', $tmp);
						$this->push_TLV(6, '');
				        $out .= $this->pack_all_TLVs();
    					break;
    				case 11:
    				// message ack & auto-message send SNAC
    					$out = pack('nnn', 4, 11, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_MESSAGESENDACK']); // SNAC request ID
    					$out .= pack('NNn', $data['msg_id1'], $data['msg_id2'], $data['channel']); // message-id & channel
    					$out .= chr(strlen($data['uin']));
				        $out .= $data['uin'];
				        $out .= pack('n', 3);
				        if ($data['channel'] == 1) {
				        	if (!isset($data['text']))
				        		$data['text'] = '';
				        	$this->clear_TLVs();
							$this->push_TLV('MSG_CAPABILITIES', chr(1) . chr(1)); // fragments are packed as TLVs
							$this->push_TLV('MSG_TEXT', pack('nn', 0, 0) . $data['text']);
							$out .= $this->pack_all_TLVs();
				        } else {
				        	$out .= pack('vvNNNNvVcv', 27, 8, 0, 0, 0, 0, 0, 3, 0, $this->icbm_req_id);
				        	$out .= pack('vvNNN', 14, $this->icbm_req_id, 0, 0, 0);
				        	if (!isset($data['mtype']))
				        		$data['mtype'] = 1;
				        	if ($data['mtype'] >= 232)
				        		$data['mflags'] = 3; 
				        	if (!isset($data['mflags']))
				        		$data['mflags'] = 0;
				        	if (!isset($data['text']))
				        		$data['text'] = '';
				        	$out .= pack('ccvvva*', $data['mtype'], $data['mflags'], 0, 0, strlen($data['text']) + 1, $data['text']) . chr(0);
				        }
    					break;
    			}
    			break;
			case 19:
    			switch ($id) {
    				case 26:
    				// auth reply SNAC
    					$out = pack('nnn', 19, 26, 0); // SNAC header & flags
    					$out .= pack('N', $this->const['REQID_AUTHREPLY']); // SNAC request ID
    					$out .= chr(strlen($data['uin']));
				        $out .= $data['uin'];
				        $out .= $data['auth_given'] ? chr(1) : chr(0);
				        $out .= pack('na*', strlen($data['reason']), $data['reason']);
    					break;
    			}
    			break;
    		case 21:
    			switch ($id) {
    				case 2:
    				// meta information request SNAC
    					$out = pack('nnn', 21, 2, 0); // SNAC header & flags
    					switch ($data['type']) {
    						case 'om_req':
    							$out .= pack('N', $this->const['REQID_GETOFFLINEMESSAGES']); // SNAC request ID
    							$tmp = pack('vVvv', 8, $this->id['uin'], 60, $this->meta_seqnum++);
    							$tmp = $this->make_TLV(1, $tmp);
    							$out .= $tmp['raw'];
    							break;
    						case 'om_del':
    							$out .= pack('N', $this->const['REQID_DELOFFLINEMESSAGES']); // SNAC request ID
    							$tmp = pack('vVvv', 8, $this->id['uin'], 62, $this->meta_seqnum++);
    							$tmp = $this->make_TLV(1, $tmp);
    							$out .= $tmp['raw'];
    							break;
    						case 'user_fullinfo':
    							$out .= pack('N', $this->const['REQID_FULLUSERINFO']); // SNAC request ID
    							$tmp = pack('vVvvvV', 14, $this->id['uin'], 2000, $this->meta_seqnum++, 1232, $data['uin']);
    							$tmp = $this->make_TLV(1, $tmp);
    							$out .= $tmp['raw'];
    							break;
    						case 'user_shortinfo':
    							$out .= pack('N', $this->const['REQID_SHORTUSERINFO']); // SNAC request ID
    							$tmp = pack('vVvvvV', 14, $this->id['uin'], 2000, $this->meta_seqnum++, 1210, $data['uin']);
    							$tmp = $this->make_TLV(1, $tmp);
    							$out .= $tmp['raw'];
    							break;
    					}
    					break;
    			}
    			break;
    	}
    	return $out;
    }
    
    function parse_incoming_message($snac) {
    	$headers = array();
    	$headers = unpack('N2msg_id/n1channel/c1uin_length', substr($snac['raw']['body'], 0, 11));
    	$headers += unpack('a*uin', substr($snac['raw']['body'], 11, $headers['uin_length']));
    	$headers += unpack('n1warn_level/n1fixed_tlvs', substr($snac['raw']['body'], (11 + $headers['uin_length']), 4));
    	$msg = array(
    		'uin'		 => $headers['uin'],
    		'channel'	 => $headers['channel'],
    		'time'		 => time(),
    		'warn_level' => $headers['warn_level'],
    		'msg_id1'	 => $headers['msg_id1'],
    		'msg_id2'	 => $headers['msg_id2']
    	);
    	$this->clear_TLVs();
    	$this->unpack_all_TLVs(substr($snac['raw']['body'], (15 + $headers['uin_length'])));
    	if (isset($this->tbt[6])) {
    		$msg += unpack('n1status_flags/n1status', $this->tbt[6]['value']);
    		$msg['status'] = $this->split_to_flags($msg['status']);
    		$msg['status_flags'] = $this->split_to_flags($msg['status_flags']);
    	}
    	if (isset($this->tbt[15]))
    		$msg += unpack('N1online_time', $this->tbt[15]['value']);
    	switch ($headers['channel']) {
    		case 1:
    			if (isset($this->tbt[4]))
    				$msg['auto'] = 1;
    			$msg += $this->parse_ch1_text($this->tbt[2]['value']);
    			break;
    		case 2:
    			$msg += $this->parse_ch2_text($this->tbt[5]['value']);
    			break;
    		case 4:
    			$msg += $this->parse_ch4_text($this->tbt[5]['value']);
    			break;
    		default:
    			$this->error('Unsupported incoming message channel: ' . $headers['channel'], __FILE__, __LINE__);
    			break;
    	}
    	return $msg;
    }
    
    function parse_offline_message($snac) {
    	if (strlen($snac['raw']['body']) <= 15)
    		return false;
    	$data = array();
    	$this->clear_TLVs();
    	$this->unpack_all_TLVs($snac['raw']['body']);
    	if (isset($this->tbt[1])) {
    		$data = unpack('v1size/V1to_uin/v1data_type/v1req_seqnum/V1from_uin/v1year/c1month/c1day/c1hour/c1minute/c1type/c1flags/v1length/a*text', $this->tbt[1]['value']);
    		if ($data['type'] == 12)
    			$data['text'] = 'You were added [server notification]';
    		if (ord($data['text']{0}) < 32)	// a try to guess encoding
    			$data['text'] = $this->decode_text($data['text'], 2, 0);
    		$msg = array(
    			'uin'		=> $data['from_uin'],
    			'time'		=> mktime($data['hour'], $data['minute'], 0, $data['month'], $data['day'], $data['year']),
    			'text'		=> $data['text'],
    			'offline'	=> 1
    		);
    		return $msg;
    	} else {
    		$this->error('Unknown offline message format.', __FILE__, __LINE__);
			return false;
    	}
    }
    
    function parse_user_info($snac) {
    	$headers = array(
    		'uin_length'	=> ord($snac['raw']['body']{0})
    	);
    	$headers += unpack('a*uin', substr($snac['raw']['body'], 1, $headers['uin_length']));
    	$headers += unpack('n1warn_level/n1fixed_tlvs', substr($snac['raw']['body'], (1 + $headers['uin_length']), 4));
    	$this->clear_TLVs();
    	$this->unpack_all_TLVs(substr($snac['raw']['body'], (5 + $headers['uin_length'])));
    	$info = array(
    		'uin'			=> $headers['uin'],
    		'warn_level'	=> $headers['warn_level']
    	);
    	if (isset($this->tbt[2])) {
    		$info += unpack('n1profile_len/a*profile', $this->tbt[2]['value']);
    	}
    	if (isset($this->tbt[3])) {
    		if (strlen($this->tbt[3]['value']) == 4)
    			$info += unpack('N1signon_time', $this->tbt[3]['value']);
    	}
    	if (isset($this->tbt[4])) {
    		$info += unpack('n1awaymsg_len/a*awaymsg', $this->tbt[4]['value']);
    	}
    	// TODO: client capabilities & DC info parsing
    	if (isset($this->tbt[6])) {
    		$info += unpack('n1status_flags/n1status', $this->tbt[6]['value']);
    		$info['status'] = $this->split_to_flags($info['status']);
    		$info['status_flags'] = $this->split_to_flags($info['status_flags']);
    	}
    	if (isset($this->tbt[10])) {
    		$tmp = unpack('c4num', $this->tbt[10]['value']);
    		$info['ext_ip'] = implode('.', $tmp);
    	}
    	if (isset($this->tbt[15])) {
    		$info += unpack('N1idle_time', $this->tbt[15]['value']);
    	}
    	return $info;
    }
    
    function parse_short_metauserinfo($snac) {
    	$arr = $this->plain_unpack_word_le("nickname/first_name/last_name/email", substr($snac['raw']['body'], 17));
    	$arr['auth_flag'] = $snac['raw']['body']{$arr['count']++} ? 1 : 0;
    	return $arr;
    }
    
    function parse_missed_message_ack($snac) {
    	$headers = unpack('n1channel/c1uin_length', substr($snac['raw']['body'], 0, 3));
    	$headers += unpack('n1num_messages/n1err_code', substr($snac['raw']['body'], strlen($snac['raw']['body']) - 4));
    	$errcodes = array(
    		0 => 'The message was invalid',
			1 => 'The message was too large',
			2 => 'Message rate exceeded',
			3 => 'Sender too evil',
			4 => 'Recipient too evil'
    	);
    	if (array_key_exists($headers['err_code'], $errcodes)) {
    		$reason = $errcodes[$headers['err_code']];
    	} else {
    		$reason = "Error code " . $headers['err_code'];
    	}
    	$arr = array(
    		'channel'		=> $headers['channel'],
    		'uin'			=> substr($snac['raw']['body'], 3, $headers['uin_length']),
    		'num_messages'	=> $headers['num_messages'] -1,
    		'reason'		=> $reason
    	);
    	return $arr;
    }

	//================================================================================
	//   Message text processing routines
	//================================================================================    
	
	function parse_ch1_text($tlv) {
		$this->unpack_all_TLVs($tlv);
		if (isset($this->tbt[257])) {
			$arr = unpack('n1charset/n1subset', substr($this->tbt[257]['value'], 0, 4));
			$arr['text'] = $this->decode_text(substr($this->tbt[257]['value'], 4), $arr['charset'], $arr['subset']);
			return $arr;
		} else {
			$this->error('Unknown incoming message format', __FILE__, __LINE__);
			return array('text' => '');
		}
	}
	
	function parse_ch2_text($tlv) {
		$headers = unpack('n1mtype/N2cookie/A*capability', substr($tlv, 0, 26));
		if ($headers['capability'] == $this->dump2bin('09 46 13 49 4C 7F 11 D1 82 22 44 45 53 54 00 00')) {
			$this->unpack_all_TLVs(substr($tlv, 26));
			if (!isset($this->tbt[10001])) {
				$this->error('Wrong CH2 message format: TLV 0x2711 not found', __FILE__, __LINE__);
				return array('text' => '');
			}
			$data = unpack('c1mtype/c1mflags/v1mstatus/v1mpriority/v1mlength', substr($this->tbt[10001]['value'], 45, 8));
			$data['text'] = substr($this->tbt[10001]['value'], 53, $data['mlength'] - 1);
			if (strlen($data['text']) > 1 && ord($data['text']{0}) < 32)
				$data['text'] = $this->decode_text($data['text'], 2, 0);
			elseif (strlen($data['text']) > 1 && $this->win_or_uni($data['text']) == 2)
				$data['text'] = $this->decode_text($data['text'], 8, 0);
			$this->append_mtype($data['mtype'], $data['text']);
			$this->icbm_req_id = ord($this->tbt[10001]['value']{27}) + (256 * ord($this->tbt[10001]['value']{28}));
			$ret = array(
				'text' => $data['text']
			);
			if (strlen($this->tbt[10001]['value']) >= 61 + $data['mlength']) {
				$data += unpack('V1fg_color/V1bg_color', substr($this->tbt[10001]['value'], 53 + $data['mlength'], 8));
				$ret['fg_color'] = dechex($data['fg_color']);
				$ret['bg_color'] = dechex($data['bg_color']);
			}
			if ($data['mflags'] == 3)
				$ret['auto'] = 1;
			return $ret;
		} else {
			$this->error('Unknown capability in CH2 message. TLV 0x05 dump follows: ' . $this->str_hex_dump($tlv), __FILE__, __LINE__);
			return array('text' => '');
		}
	}
	
	function parse_ch4_text($tlv) {
		$data = unpack('V1sender/c1mtype/c1mflags/v1mlength', substr($tlv, 0, 8));
		$data['text'] = substr($tlv, 8, $data['mlength'] - 1);
		if (strpos($data['text'], $this->dump2bin('FE FE FE FE 30 FE')) === 0)
			$data['text'] = substr($data['text'], 6);
		if (strlen($data['text']) > 1 && ord($data['text']{0}) < 32)
			$data['text'] = $this->decode_text($data['text'], 2, 0);
		elseif (strlen($data['text']) > 1 && $this->win_or_uni($data['text']) == 2)
			$data['text'] = $this->decode_text($data['text'], 8, 0);
		$this->append_mtype($data['mtype'], $data['text']);
		$ret = array('text' => $data['text']);
		if ($data['mflags'] == 3)
			$ret['auto'] = 1;
		return $ret;
	}
	
	function decode_text($text, $from_charset, $from_subset) {
		switch ($from_charset) {
			case 0:
				return $text;
			case 2:
				switch ($from_subset) {
					case 0:
						$out = '';
						for ($i =0; $i < strlen($text); $i += 2) {
							$sym = unpack('n1code', $text{$i} . $text{$i+1});
							if ($sym['code'] < 128) {
								$out .= chr($sym['code']);
							} elseif ($sym['code'] >= 1040 && $sym['code'] <= 1103) {
								$out .= chr($sym['code'] - 848);
							} elseif ($sym['code'] == 1105) {
								$out .= chr(184);
							} elseif ($sym['code'] == 1025) {
								$out .= chr(168);
							} elseif ($sym['code'] == 8470) {
								$out .= chr(185);
							} 
						}
						return $out;
					default: return $text;
						break;
				}
			case 8:	// UTF-8 to Win-1251 convert
				$out = '';
				for ($i=0; $i < strlen($text); $i++) {
					$c = ord($text{$i});
					if ($c == 208) {
						if (isset($text{$i+1})) {
							$i++; $c2 = ord($text{$i});
							if ($c2 == 129) {
								$out .= chr(168);
							} else {
								$out .= chr($c2 + 48);
							}
						}
					} elseif ($c == 209) {
						if (isset($text{$i+1})) {
							$i++; $c2 = ord($text{$i});
							if ($c2 == 145) {
								$out .= chr(184);
							} else {
								$out .= chr($c2 + 112);
							}
						}
					} else {
						$out .= chr($c);
					}
				}
				return $out;
			default: 
				return $text;
		}
	}
    
	//================================================================================
	//   TLV processing routines
	//================================================================================
	
	function clear_TLVs() {
		$this->tlvs = array(); 
		$this->tbt = array();
	}
	function push_TLV($type, $value) {
		$tlv = $this->make_TLV($type, $value);
		$num = array_push($this->tlvs, $tlv);
		$this->tbt[$tlv['type']] = &$this->tlvs[$num-1];
	}
	
	function make_TLV($type, $value) {
		if (is_integer($type)) {
			$tl = $this->const['TLV_' . $type];
		} else {
			if (!array_key_exists('TYPE_' . strtoupper($type), $this->const)) {
				$this->error("TLV-type not found", __FILE__, __LINE__);
				return false;
			}
			$type = $this->const['TYPE_' . strtoupper($type)];
			$tl = $this->const['TLV_' . $type];
		}
		if (is_array($value))
			$value = implode('', $value);
		switch ($tl) {
			case 'array': $ft = 'a*';
				$len = strlen($value);
				break;
			case 'byte': $ft = 'c';
				$len = 1;
				break;
			case 'dword': $ft = 'N';
				$len = 4;
				break;
			case 'string': $ft = 'a*';
				$len = strlen($value);
				break;
			case 'word': $ft = 'n';
				$len = 2;
				break;
			default: $this->error("TLV-type invalid", __FILE__, __LINE__);
				return false;
		}
		$raw = pack('nn' . $ft, $type, $len, $value);
		$tlv = array (
			'raw'		=> $raw,
			'type'		=> $type,
			'length'	=> $len,
			'value'		=> $value
		);
		return $tlv;
	}
	
	function pack_TLV(&$tlv) {
		$tl = $this->const['TLV_' . $tlv['type']];
		switch ($tl) {
			case 'array': $ft = 'a*';
				break;
			case 'byte': $ft = 'c';
				break;
			case 'dword': $ft = 'N';
				break;
			case 'string': $ft = 'a*';
				break;
			case 'word': $ft = 'n';
				break;
			default: $this->error("TLV-type invalid", __FILE__, __LINE__);
				return false;
		}
		$tlv['raw'] = pack('nn' . $ft, $tlv['type'], $tlv['length'], $tlv['value']);
		return 1;
	}
	
	function unpack_TLV(&$tlv) {
		$tmp = unpack('n1type/n1length', substr($tlv['raw'], 0, 4)); 
        $tlv['type'] = $tmp['type'];
        $tlv['length'] = $tmp['length'];
        $tlv['value'] = substr($tlv['raw'], 4, $tmp['length']); 
	}
	
	function unpack_all_TLVs($raw) {
		$this->tlvs = array(); $this->tbt = array();
		$i = 0;
		while ($i < strlen($raw)) {
			$tlv = array();
			$header = substr($raw, $i, 4);
			$i += 4;
			$header = unpack('n1type/n1length', $header);
			$tlv['type'] = $header['type'];
			$tlv['length'] = $header['length'];
			$tlv['value'] = substr($raw, $i, $tlv['length']);
			$i += $tlv['length'];
			$num = array_push($this->tlvs, $tlv);
			$this->tbt[$tlv['type']] = &$this->tlvs[$num-1];
		}
	}
	
	function pack_all_TLVs() {
		$set = "";
		foreach ($this->tlvs as $tlv) {
			if (!isset($tlv['raw']) || !$tlv['raw']) {
				$this->pack_TLV($tlv);
			}
			$set .= $tlv['raw'];
		}
		return $set;
	}
	
	//================================================================================
	//   Misc functions
	//================================================================================
	
	function roast_pass($password) 
    { 
        $roast_arr = array(0xF3, 0x26, 0x81, 0xC4, 0x39, 0x86, 0xDB, 0x92, 0x71, 0xA3, 0xB9, 0xE6, 0x53, 0x7A, 0x95, 0x7c); 
        $roasted_pass = ''; 
        for ($i = 0; $i < strlen($password); $i++) { 
            $roasted_pass .= chr($roast_arr[$i] ^ ord($password{$i})); 
        } 
        return $roasted_pass; 
    } 
    
    function split_to_flags($val) {
    	$d = 8192;
    	$arr = array();
    	while ($d >= 1) {
    		if ($val - $d >= 0) {
    			$val -= $d;
    			$arr[] = $d;
    		}
    		$d = $d / 2;
    	}
    	return $arr;
    }
    
    function win_or_uni($str) {
		$neutral = ord($str{0}) < 128;
		$is_uni = true;
		$is_rs = false;
		for ($i = 0; $i < strlen($str); $i++) {
			if ($neutral) 
				if (ord($str{$i}) > 127)
					$neutral = false;
			if ($is_uni) 
				if (ord($str{$i}) == 208 || ord($str{$i}) == 209) {
					$is_uni = $this->check_sym_pair($str, $i);
					if (!$is_rs) 
						$is_rs = true;
				}
		}
		if ($neutral)
			return 0;
		elseif ($is_uni)
			return $is_rs ? 2 : 1;
		return 1;
	}
	
	function plain_unpack_word_le($format, $data) {
		$format = explode("/", $format);
		$arr = array(); $sym = 0;
		foreach ($format as $f) {
			$len = ord($data{$sym++}) + 256 * ord($data{$sym++});
			$arr[$f] = substr($data, $sym, $len - 1);
			$sym += $len;
		}
		$arr['count'] = $sym;
		return $arr;
	}
	
	function check_sym_pair($str, $offset) {
		if (!isset($str{$offset+1}))
			return false;
		else
			$tmp = ord($str{$offset+1});
		if (ord($str{$offset}) == 208) {
			if ($tmp == 129)
				return true;
			return ($tmp > 143 && $tmp < 192);
		} elseif (ord($str{$offset}) == 209) {
			return ($tmp > 127 && $tmp < 146);
		}
	}
	
	function word_val($str) {
		return ord($str{1}) + (256 * ord($str{0}));
	}
    
    function append_mtype($type, &$text) {
    	switch ($type) {
				case 1:
					break;
				case 2:	$text .= ' [Chat request]';
					break;
				case 3:	$text .= ' [File transfer request]';
					break;
				case 6:	$text .= ' [Authorization request]';
					break;
				case 7:	$text .= ' [Authorization-denied message]';
					break;
				case 8:	$text .= ' [Authorization-given message]';
					break;
				case 9:	$text .= ' [Message from server]';
					break;
				case 12: $text .= ' [You were added]';
					break;
				case 232: $text .= ' [Auto-away message]';
					break;
				case 233: $text .= ' [Auto-occupied message]';
					break;
				case 234: $text .= ' [Auto-N/A message]';
					break;
				case 235: $text .= ' [Auto-DND message]';
					break;
				case 236: $text .= ' [Auto-free4chat message]';
					break;
			}
    }
    
    function str_status($st, $fl = '') {
    	$fl = $fl ? 'F' : 'T';
    	if(!is_array($st))
    		$st = $this->split_to_flags($st);
    	$out = '';
    	foreach ($st as $flag) {
    		$out .= $this->const['S' . $fl . $flag] . '; ';
    	}
    	return $out;
    }
    
    function outseq() {
    	$this->id['out_seqnum']++; 
    	if ($this->id['out_seqnum'] >= 65536)
    		$this->id['out_seqnum'] %= 32768;
    	return $this->id['out_seqnum'];
    }
    
    function trim_arrays() {
    	if ($this->settings['max_flaps'])
    		array_splice($this->flaps, $this->settings['max_flaps']);
    	if ($this->settings['max_snacs'])
    		array_splice($this->snacs, $this->settings['max_snacs']);
    }
    
    function keep_alive() {
    	$this->send_FLAP(5, '');
    	//$this->log('Keep-alive packet sent', __FUNCTION__, __LINE__);
    }
	
	//================================================================================
	//   Error handling & debug functions
	//================================================================================
	
	function error($err, $file = "", $line = "") {
		$this->log('Error: ' .$err, __FUNCTION__, __LINE__);
		if ($this->debug >= 1) {
			if (!$err)
				$err = "unknown";
			if (is_integer($err))	// an error code supplied?
				if (array_key_exists($err, $this->errmsg)) {
					$err = $this->errmsg[$err];
				} else {
					$err = "unknown";
				}
			$err = "\"". $err . "\"";
			$err .= $file ? " <b>in file</b> {$file}" : "";
			$err .= $line ? " <b>at line</b> {$line}" : "";
			$out = "<TABLE style=\"BORDER: #FF8888 2px solid; FONT-SIZE: 12px; MARGIN: 10px 60px 10px; FONT-FAMILY: Courier\" cellSpacing=2 cellPadding=10 align=default border=0>
  				<TBODY>
  				<TR><TD bgColor=#FFC0C0><strong>Error</strong>: {$err} </TD></TR>
	  			</TBODY></TABLE><br>\r\n";
			print $out;
		}
	}
	
	function fatal_error($err, $file = "", $line = "") {
		$this->log('Fatal error: ' . $err, __FUNCTION__, __LINE__);
		if (!$err)
			$err = "unknown";
		if (is_integer($err))	// an error code supplied?
			if (array_key_exists($err, $this->errmsg)) {
				$err = $this->errmsg[$err];
			} else {
				$err = "unknown";
			}
		$err = "\"". $err . "\"";
		$err .= $file ? " <b>in file</b> {$file}" : "";
		$err .= $line ? " <b>at line</b> {$line}" : "";
		$out = "<TABLE style=\"BORDER: #FF8888 2px solid; FONT-SIZE: 12px; MARGIN: 10px 60px 10px; FONT-FAMILY: Courier\" cellSpacing=2 cellPadding=10 align=default border=0>
  			<TBODY>
  			<TR><TD bgColor=#FFA0A0><strong>Fatal Error</strong>: {$err} </TD></TR>
  			</TBODY></TABLE><br>\r\n";
		print $out;
		die();
	}
	
	
	function view_FLAP($flap = null, $return = false) {
		if ($flap === null) {
			$flap = $this->flaps[0];
		}
		$codes = "";
		$headers = unpack('c1star/c1channel/n1seqnum/n1size', $flap['raw']['header']);
		for ($i = 0; $i < strlen($flap['raw']['header']); $i++) {
			$code = strtoupper(dechex(ord($flap['raw']['header']{$i})));
			$code = (strlen($code) < 2 ? "0" : "") . $code . " ";
			$codes .= $code;
		}
		$stamp = strftime("%d-%m-%y %H:%M:%S");
		$out = "<table border=\"0\" align=\"default\" cellpadding=\"10\" cellspacing=\"2\" style=\"margin: 5 60 5 60; border: #CCCCCC 2px solid; font-family:Courier; font-size:12px;\">
  <tr><td colspan=\"2\" bgcolor=\"#dddddd\"><div align=\"right\"><em>FLAP Dump (<b>{$flap['dir']} @ {$stamp}</b>)</em> </div></td></tr>
  <tr><td bgcolor=\"#dddddd\"><div align=\"center\">{$codes}</div></td>
    <td bgcolor=\"#dddddd\"><div align=\"center\"><strong>Channel: </strong>{$headers['channel']}<br />
        <strong>Seq. num.</strong>: {$headers['seqnum']}<br />
            <strong>Data size</strong>: {$headers['size']}</div></td></tr>";
		$codes = ""; $chars = "";
		for ($i = 0; $i < strlen($flap['raw']['body']); $i++) {
			$code = strtoupper(dechex(ord($flap['raw']['body']{$i})));
			$code = (strlen($code) < 2 ? "0" : "") . $code . " ";
			$codes .= $code;
			$chars .= ord($flap['raw']['body']{$i}) < 32 ? "." : htmlspecialchars($flap['raw']['body']{$i});
			if (is_integer(($i+1)/16)) {
				$codes .= "<br>\r\n";
				$chars .= "<br>\r\n";
			}
		}
		$out .= "<tr>
    <td bgcolor=\"#dddddd\"><div align=\"center\">{$codes}</div></td>
    <td bgcolor=\"#dddddd\"><div align=\"center\">{$chars}</div></td></tr></table><br>\r\n";
		if (!$return)
			print $out;
		else
			return $out;	
	}
	
	function view_TLV($tlv = null) {
		if ($tlv === null) {
			$tlv = $this->tlvs[0];
		}
		$codes_h = ""; $codes = ""; $chars = "";
		if (isset($tlv['raw'])) {
			$headers = unpack('n1type/n1size', substr($tlv['raw'], 0, 4));
			$type = $headers['type'];
			$size = $headers['size'];
			for ($i = 0; $i < 4; $i++) {
				$code = strtoupper(dechex(ord($tlv['raw']{$i})));
				$code = (strlen($code) < 2 ? "0" : "") . $code . " ";
				$codes_h .= $code;
			}
			$tlv['value'] = substr($tlv['raw'], 4);
		} else {
			$type = $tlv['type'];
			$size = $tlv['length'];
			$codes_h = '<i>No raw data</i>';
		}
		for ($i = 0; $i < strlen($tlv['value']); $i++) {
				$code = strtoupper(dechex(ord($tlv['value']{$i})));
				$code = (strlen($code) < 2 ? "0" : "") . $code . " ";
				$codes .= $code;
				$chars .= ord($tlv['value']{$i}) < 32 ? "." : htmlspecialchars($tlv['value']{$i});
				if (is_integer(($i+1)/16)) {
					$codes .= "<br>\r\n";
					$chars .= "<br>\r\n";
				}
			}
		if (strlen($tlv['value']) < 5) {
			switch (strlen($tlv['value'])) {
				case 1:
					$dec = unpack('c1dec', $tlv['value']);
					break;
				case 2:
					$dec = unpack('n1dec', $tlv['value']);
					break;
				case 4:
					$dec = unpack('N1dec', $tlv['value']);
					break;
				default: $dec = '';
					break;
			}
			if ($dec) $dec = " <i>(decimal: " . $dec['dec'] . ")</i> ";
		} else $dec = '';
		$out = "<table border=\"0\" align=\"default\" cellpadding=\"10\" cellspacing=\"2\" style=\"margin: 5 60 5 60; border: #CCCCCC 2px solid; font-family:Courier; font-size:12px;\">
  <tr><td colspan=\"2\" bgcolor=\"#dddddd\"><div align=\"right\"><em>TLV Dump</em> </div></td></tr>
  <tr><td bgcolor=\"#dddddd\"><div align=\"center\">{$codes_h}</div></td>
    <td bgcolor=\"#dddddd\"><div align=\"center\"><strong>Type ID: </strong>{$type}<br />
        <strong>Length</strong>: {$size}</div></td></tr>";
		$out .= "<tr>
    <td bgcolor=\"#dddddd\"><div align=\"center\">{$codes}</div></td>
    <td bgcolor=\"#dddddd\"><div align=\"center\">{$chars}{$dec}</div></td></tr></table><br>\r\n";
		print $out;
	}
	
	function hex_dump($data) {
		$codes = ""; $chars = "";
		for ($i = 0; $i < strlen($data); $i++) {
			$code = strtoupper(dechex(ord($data{$i})));
			$code = (strlen($code) < 2 ? "0" : "") . $code . " ";
			$codes .= $code;
			$chars .= ord($data{$i}) < 32 ? "." : htmlspecialchars($data{$i});
			if (is_integer(($i+1)/16)) {
				$codes .= "<br>\r\n";
				$chars .= "<br>\r\n";
			}
		}
		print "<table border=\"0\" align=\"default\" cellpadding=\"10\" cellspacing=\"2\" style=\"margin: 5 60 5 60; border: #CCCCCC 2px solid; font-family:Courier; font-size:12px;\">
  <tr><td colspan=\"2\" bgcolor=\"#dddddd\"><div align=\"right\"><em>Hex Dump</em> </div></td></tr><tr>
    <td bgcolor=\"#dddddd\"><div align=\"center\">{$codes}</div></td>
    <td bgcolor=\"#dddddd\"><div align=\"center\">{$chars}</div></td></tr></table><br>\r\n";
	}
	
	function str_hex_dump($bin) {
		$codes = "";
		for ($i = 0; $i < strlen($bin); $i++) {
			$code = strtoupper(dechex(ord($bin{$i})));
			$code = (strlen($code) < 2 ? "0" : "") . $code . " ";
			$codes .= $code;
		}
		return $codes;
	}
	
	function dump2bin($str) {
		$str = explode(" ", $str);
		$out = "";
		foreach ($str as $val) {
			if (strlen(trim($val)) == 2)	
				$out .= chr(hexdec(trim($val)));
			elseif (strlen(trim($val)) == 5) {
				$tmp = explode('-', trim($val));
				if(isset($tmp[1])) {
					$out .= chr(hexdec($tmp[0]));
					$out .= chr(hexdec($tmp[1]));
				}
			}
		}
		return $out;
	}
	
	function start_logging($filename) {
		return $this->logging = @fopen($filename, "a");
	}
	
	function stop_logging() {
		@fclose($this->logging);
		$this->logging = false;
	}
	
	function log($msg, $function = '', $line = '') {
		if (is_resource($this->logging))
			fwrite($this->logging, strftime("%d-%m-%y %H:%M:%S") . " - " . $msg . ' (function "' . $function . '" @ line "' . $line . "\")\r\n");
	}
	
	function debug_msg($msg, $priority = 0) {
		$this->log($msg, __FUNCTION__, __LINE__);
		$msg .= ' <b>@'.strftime("%d-%m %H:%M:%S").'</b>';
		if ($this->debug == 1) {
			if ($priority)
				print '<div style="margin-left: 40px; font-family: Tahoma; font-size: 12px">'.$msg.'</div><br>';
		} elseif ($this->debug > 1) {
			print '<div style="margin-left: 40px; font-family: Tahoma; font-size: 12px">'.$msg.'</div><br>';
		}
		flush();
	}
}

?>