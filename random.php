<?php

if(!defined('IN_CAS')) {
	echo "This file cannot be initialized independently.";
	die();
}

class Random {

	public static function newRandom($length, $type) {
		$chars = '0';
		if ($type == "uid") {
			$chars = '0123456789abcdef';
		} elseif ($type == "rand") {
			$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		$charsLength = strlen($chars);
		$rand = '';
		for ($i = 0; $i < $length; $i++) {
			$rand .= $chars[rand(0, $charsLength - 1)];
		}
		return $rand;
	}

	public static function newCryptographicRandom($length) {
		return bin2hex(openssl_random_pseudo_bytes($length));
	}
}
?>
