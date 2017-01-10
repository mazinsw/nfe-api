<?php
$cert_store = file_get_contents(dirname(dirname(__FILE__)).'/tests/cert/cert.pfx');
if ($cert_store === false) {
    echo "Error: Unable to read the cert file\n";
    exit;
}

if (openssl_pkcs12_read($cert_store, $cert_info, "associacao")) {
	$certinfo = openssl_x509_parse($cert_info['cert']);
	//print_r($certinfo);
	echo 'Expira em: '.date('d/m/Y', $certinfo['validTo_time_t']);
	file_put_contents('./public.pem', $cert_info['cert']);
	file_put_contents('./private.pem', $cert_info['pkey']);
} else {
    echo "Error: Unable to read the cert store.\n";
    exit;
}