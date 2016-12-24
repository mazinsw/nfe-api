<?php
function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}

$source_folder = $argv[1];
$dest_folder = $argv[2];
date_default_timezone_set('America/Fortaleza');

$it = new DirectoryIterator($source_folder);
foreach ($it as $filename) {
	if($filename->isDot())
		continue;
	$csv = csv_to_array($source_folder.'/'.$filename, ';');
	preg_match('/TabelaIBPTax([A-Z]{2})(.*)\.csv/s', $filename, $info);
	$uf = $info[1];
	$first = current($csv);
	$vigenciainicio = date_create_from_format('d/m/Y', $first['vigenciainicio']);
	$vigenciafim = date_create_from_format('d/m/Y', $first['vigenciafim']);
	$info = array(
		'fonte' => $first['fonte'],
		'versao' => $first['versao'],
		'chave' => $first['chave'],
		'vigencia' => array(
			'inicio' => date_format($vigenciainicio, 'Y-m-d'),
			'fim' => date_format($vigenciafim, 'Y-m-d')
		)
	);
	$items = array();
	foreach ($csv as $row) {
		$o = array(
			'importado' => floatval($row['importadosfederal']),
			'nacional' => floatval($row['nacionalfederal']),
			'estadual' => floatval($row['estadual']),
			'municipal' => floatval($row['municipal']),
			'tipo' => $row['tipo']
		);
		$key = $row['codigo'].'.'.sprintf('%02s', $row['ex']);
		$items[$key] = $o;
	}
	$data = array(
		'info' => $info,
		'estados' => array($uf => $items)
	);
	$data = json_encode($data);
	$outfile = $dest_folder.'/'.$uf.'.json';
	echo 'Writing '.$outfile."\n";
	file_put_contents($outfile, $data);
}
