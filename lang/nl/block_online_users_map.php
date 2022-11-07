<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Online Users Map block - Customised online users map based on work of Alex Little
 *
 * @package   block_online_users_map
 * @copyright 2018 onwards iplusacademy  {@link https://www.iplusacademy.org}
 * @author    Renaat Debleu (www.ewallah.net)
 * @author    Alex Little
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

$string['pluginname'] = 'Online Gebruikers';

$string['centrelat'] = 'Start breedtegraad';
$string['centrelng'] = 'Start lengtegraad';
$string['centreuser'] = 'Centreer op gebruikers locatie';
$string['debug'] = 'Toon debug meldingen';
$string['googleapikey'] = 'Google Maps API sleutel';
$string['namesonmap'] = 'Toon gebruikersnamen';
$string['offline'] = 'Toon offline gebruikers';
$string['timetosee'] = 'Verwijder na inactiviteit';
$string['type'] = 'Type kaart';
$string['updatelimit'] = 'Maximum locaties om te updaten';
$string['zoomlevel'] = 'Begin zoom niveau';

$string['configcentrelat'] = 'Start lengtegraad voor het centrum van de kaart - in decimaal formaat (geen graden/minuten)';
$string['configcentrelng'] = 'Start breedtegraad voor het centrum van de kaart - in decimaal formaat (geen graden/minuten)';
$string['configcentreuser'] = 'Centreer de kaart op de locatie van de huidige gebruiker, met het zoom percentage dat hierboven vermeld wordt. Deze instelling is prioritair t.o.v. de lengte/breedte coordinaten van hierboven.';
$string['configdebug'] = 'Toon debug berichten bij cron';
$string['configgoogleapikey'] = 'Google Maps API sleutel, verkrijg een sleutel van {$a}';
$string['confignamesonmap'] = 'Moeten gebruikersnamen getoond worden op de kaart?  Anders worden de woonplaatsen van de gebruikers getoond.';
$string['configoffline'] = 'Toon ook de offline gebruikers?';
$string['configtimetosee'] = 'Aantal minuten inactiviteit die bepalen wanneer een gebruiker als niet meer aangelogd wordt aanzien.';
$string['configtype'] = 'Selecteer de kaart provider';
$string['configupdatelimit'] = 'Maximum aantal locaties die worden geupdated in elke cron - zonder performantieverlies. Dit moet een integer getal zijn groter dan of gelijk aan 0. Bij 0 worden alle velden geupdated.';
$string['configzoomlevel'] = 'Start zoomniveau van de kaart';
$string['periodnminutes'] = 'laatste {$a} minuten';
