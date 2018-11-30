<?php
	if(!isset($_SESSION)){ 
		session_start(); 
		require_once '../utils/Autoload.php';
		require_once './userController.class.php';
	}
	
	if(!isset($_SESSION['current_user'])){
	  $_SESSION['message_user_login_fail']= "Session timeout";
      header('Location: ../login.php');
    }

	try {
		if (!empty($_POST["option"]) || !empty($_POST["ip"])) {
				$begin = "{\"gateway_conf\": {\"gateway_ID\": \"B827EBFFFE3E082A\",\"server_address\":\"";
				$end = "\",\"serv_port_up\": 1700,\"serv_port_down\": 1700,\"keepalive_interval\": 10,\"stat_interval\": 30,\"push_timeout_ms\": 100,\"forward_crc_valid\": true,\"forward_crc_error\": false,\"forward_crc_disabled\": false}}";
				$option = "";
				if(isset($_POST["option"])){
					if($_POST["option"]=="ttn"){
						$option = "router.eu.thethings.network";
					}elseif($_POST["option"]=="isolgate"){
						$option = "localhost";
					}elseif($_POST["option"]=="lcn"){
						if(isset($_POST["ip"])){
							if(preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $_POST["ip"])){
								$option = $_POST["ip"];
							}else{
								$_SESSION['message']= "Error! the IP address format is not correct";
								header('Location: ../index.php');
							}
						}
					}
				
				$string = $begin.$option.$end;
				// write on the file
				$file = '/opt/edge-gateway/packet_forwarder/lora_pkt_fwd/local_conf.json';
				$f = fopen($file, 'w+');
				fwrite($f, $string);
				fclose($f);
				$log = json_decode(file_get_contents("$file"));
				if($_POST["option"]=="ttn"){
					if($log->{"gateway_conf"}->{"server_address"}=="router.eu.thethings.network"){
						$_SESSION['message']= "updated successfully";
					}else{
						$_SESSION['message']= "no update";
					}
				}elseif($_POST["option"]=="isolgate"){
					if($log->{"gateway_conf"}->{"server_address"}=="localhost"){
						$_SESSION['message']= "updated successfully";
					}else{
						$_SESSION['message']= "no update";
					}
				}elseif($_POST["option"]=="lcn"){
					if(preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/',$log->{"gateway_conf"}->{"server_address"})){
						$_SESSION['message']= "updated successfully";
					}else{
						$_SESSION['message']= "no update";
					}
				}else{
					$_SESSION['message']= "no update";
				}
				
				header('Location: ../index.php');
			} else{
				$_SESSION['message']= "Not updated";
				header('Location: ../index.php');
			}
		}else{
			$_SESSION['message']= "select one option";
			header('Location: ../index.php');
		}
	} catch (Exception $exc) {
		$_SESSION['message']= "echec catched";
		header('Location: ../index.php');
	}

