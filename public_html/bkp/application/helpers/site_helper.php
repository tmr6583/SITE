<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function clean_string($string)
{
	$string = preg_replace('/[àáâãåäæ]/iu', 'a', $string);
	$string = preg_replace('/[èéêë]/iu', 'e', $string);
	$string = preg_replace('/[ìíîï]/iu', 'i', $string);
	$string = preg_replace('/[òóôõöø]/iu', 'o', $string);
	$string = preg_replace('/[ùúûü]/iu', 'u', $string);
	$string = preg_replace('/[ç]/iu', 'c', $string);
	$string = preg_replace('/\/ /iu', '', $string);
	$string = preg_replace('/[\/%*&$#@!,;?ºª|§=¬¢£³²¹\'\"\\\]/iu', '', $string);
	$string = preg_replace('/[-]/iu', ' ', $string);
	$string = urlencode(strtolower($string));
	return preg_replace('/[+]/iu', '-', $string);
}

function format_to_real($value)
{
	return number_format($value, 2, ",", ".");
}

function list_assuntos()
{
	$ci =& get_instance();
	return $ci->doctrine->em->createQuery('SELECT a.id, a.titulo FROM models\Assunto a WHERE a.ativo=true')
							->getResult();
}

function list_dicas()
{
	$ci =& get_instance();
	return $ci->doctrine->em->createQuery('SELECT d.id, d.titulo, d.dica FROM models\Dica d ORDER BY d.id DESC')
							->setMaxResults(5)->getResult();
}

function get_quantidade_cesta()
{
	$ci =& get_instance();
	$cesta = $ci->session->userdata('cesta');
	
	$quantidade = 0;
	
	if(is_array($cesta) && count($cesta) > 0)
		foreach($cesta as $item)
			$quantidade += $item['quantidade'];
	
	return $quantidade;
}
