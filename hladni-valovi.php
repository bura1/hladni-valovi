<?php

/*
Plugin Name: Hladni valovi DHMZ
Description: Prikazuje tablicu upozorenja na hladne valove sa DHMZ-a. Potrebno zaljepiti shortcode <strong>[hladni_valovi]</strong> u objavu ili stranicu.
Version: 1.0
Author: TB
*/

function add_hv_stylesheet() 
{
    wp_enqueue_style( 'style-hv', plugins_url( '/hladni-valovi/style-hv.css' ) );
}
add_action('wp_enqueue_scripts', 'add_hv_stylesheet');



function hladni_valovi_tablica() {

	$url = 'http://prognoza.hr/hladnival.xml';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$xml_raw = curl_exec($ch);
	curl_close($ch);
	$xml = simplexml_load_string($xml_raw);

	$date = strtotime($xml->metadata->creationtime);

	$date1 = new DateTime($xml->metadata->creationtime);
	date_modify($date1, '+1 day');
	$date2 = new DateTime($xml->metadata->creationtime);
	date_modify($date2, '+2 day');
	$date3 = new DateTime($xml->metadata->creationtime);
	date_modify($date3, '+3 day');
	$date4 = new DateTime($xml->metadata->creationtime);
	date_modify($date4, '+4 day');

	$day1 = new DateTime($xml->metadata->creationtime);
	date_modify($day1, '+1 day');
	$day2 = new DateTime($xml->metadata->creationtime);
	date_modify($day2, '+2 day');
	$day3 = new DateTime($xml->metadata->creationtime);
	date_modify($day3, '+3 day');
	$day4 = new DateTime($xml->metadata->creationtime);
	date_modify($day4, '+4 day');

	function dan($day) {
		switch ($day) {
			case 'Monday':
				return 'Ponedjeljak';
				break;
			case 'Tuesday':
				return 'Utorak';
				break;
			case 'Wednesday':
				return 'Srijeda';
				break;
			case 'Thursday':
				return 'Četvrtak';
				break;
			case 'Friday':
				return 'Petak';
				break;
			case 'Saturday':
				return 'Subota';
				break;
			case 'Sunday':
				return 'Nedjelja';
				break;
		}
	}

	foreach ($xml->section as $element) {
		if ($element->station) {
			$tablica = '<div class="overflow-table-hv"><table style="max-width:496px; margin:0 auto;">';

			$tablica.= '<tr>';
				$tablica.= '<td style="border:none;"></td><td style="border:none;">'.dan($day1->format("l")).'<br>'.$date1->format("j.n.Y.").'</td><td style="border:none;">'.dan($day2->format("l")).'<br>'.$date2->format("j.n.Y.").'</td><td style="border:none;">'.dan($day3->format("l")).'<br>'.$date3->format("j.n.Y.").'</td><td style="border:none;">'.dan($day4->format("l")).'<br>'.$date4->format("j.n.Y.").'</td>';
			$tablica.= '</tr>';

			foreach ($element->station as $gradovi) {
				$tablica.= '<tr>';
				$tablica.= '<td style="border:none;">';
				$tablica.= $gradovi->attributes()->name;
				$tablica.= '</td>';
				foreach ($gradovi->param as $podatci) {
					if ($podatci->attributes()->value == "W") {
						$tablica.= '<td style="background-color:#f2f2f2; width:120px; height:35px; border:2px solid #fff;"></td>';
					} elseif ($podatci->attributes()->value == "G") {
						$tablica.= '<td style="background-color:#a9e2f3; max-width:120px; height:35px; border:2px solid #fff;"></td>';
					} elseif ($podatci->attributes()->value == "O") {
						$tablica.= '<td style="background-color:#58acfa; max-width:120px; height:35px; border:2px solid #fff;"></td>';
					} elseif ($podatci->attributes()->value == "R") {
						$tablica.= '<td style="background-color:#08298a; max-width:120px; height:35px; border:2px solid #fff;"></td>';
					}
				}
				$tablica.= '</tr>';
			}
			$tablica.= '</table></div>';

			$tablica.= '<div style="max-width:620px; margin:30px auto; display:table;">
				<div style="font-size:14px;float:left;padding:0 15px;"><div style="width:30px;height:20px;background-color:#f2f2f2;margin:0 auto;"></div> nema opasnosti</div>
				<div style="font-size:14px;float:left;padding:0 15px;"><div style="width:30px;height:20px;background-color:#a9e2f3;margin:0 auto;"></div> umjerena opasnost</div>
				<div style="font-size:14px;float:left;padding:0 15px;"><div style="width:30px;height:20px;background-color:#58acfa;margin:0 auto;"></div> velika opasnost</div>
				<div style="font-size:14px;float:left;padding:0 15px;"><div style="width:30px;height:20px;background-color:#08298a;margin:0 auto;"></div> vrlo velika opasnost</div>
			</div>';

			$tablica.= '<p style="margin-top:40px;text-align:center;"><strong>U Zagrebu '.date("j.n.Y.", $date).', izradio dežurni prognostičar.</strong></p>';
		}
		return $tablica;

	}
}

add_shortcode('hladni_valovi', 'hladni_valovi_tablica');

?>