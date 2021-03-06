<?php
/********
 ** 加密
 *
***/

class Encryption {
	//加密解密
 	function encrypt($message){
		//converting a string to binary
		$enc_message = $this->asc2bin($message);
		$enc_message = $this -> xor_string($enc_message);
		$enc_message = $this -> urlsafe_b64encode($enc_message);
		return $enc_message;
	}
 	function decrypt($message){
		$dec_message = $this -> urlsafe_b64decode($message);
		$dec_message = $this -> xor_string($dec_message);
		$dec_message = $this->bin2asc($dec_message);
		return $dec_message;
	}
	//二进制和ASCII互转
 	function asc2bin($inputString, $byteLength=8){
		$binaryOutput = '';
		$strSize = strlen($inputString);
		for($x=0; $x<$strSize; $x++){
			//取得ascii码转换为二进制并补足8位
			$charBin = decbin(ord($inputString{$x}));
			$charBin = str_pad($charBin, $byteLength, '0', STR_PAD_LEFT);
			$binaryOutput .= $charBin;
		}
		return $binaryOutput;
	}
	function bin2asc($binaryInput, $byteLength=8){
		if (strlen($binaryInput) % $byteLength){
			return false;
		}
		$strSize = strlen($binaryInput);
		$origStr = '';
		// jump between bytes.
		for($x=0; $x<$strSize; $x += $byteLength){
			// extract character's binary code
			$charBinary = substr($binaryInput, $x, $byteLength);
			$origStr .= chr(bindec($charBinary)); // conversion to ASCII.
		}
		return $origStr;
	}
	
	function xor_string($string){
		 $buf = '';
		 $size = strlen($string);
		 for ($i=0; $i<$size; $i++)
			 $buf .= chr(ord($string[$i]) ^ 255);
		 return $buf;
	 }
	 
	 //安全编码和解码
	function urlsafe_b64encode($string){
		$data = base64_encode($string);
		$data = str_replace(array('+','/','='),array('-','_','.'),$data);
		return $data;
	}
	function urlsafe_b64decode($string) {
		$data = str_replace(array('-','_'),array('+','/'),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}
	
	function x_Encrypt($string, $key){
		for($i=0; $i<strlen($string); $i++){
			for($j=0; $j<strlen($key); $j++){
				$string[$i] = $string[$i]^$key[$j];
			}
		}
		return $string;
	}
	function x_Decrypt($string, $key){
		for($i=0; $i<strlen($string); $i++){
			for($j=0; $j<strlen($key); $j++){
				$string[$i] = $key[$j]^$string[$i];
			}
		}
		return $string;
	}
}

