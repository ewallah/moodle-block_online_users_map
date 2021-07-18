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

$string['pluginname'] = 'Besucherkarte';
$string['centrelat'] = 'Ursprüngliche Breite';
$string['centrelng'] = 'Ursprüngliche Länge';
$string['centreuser'] = 'Zentraler Besucherort';
$string['debug'] = 'Fehlernachrichten zeigen';
$string['googleapikey'] = 'Google Maps API Schlüssel';
$string['offline'] = 'Offline Teilnehmer zeigen';
$string['timetosee'] = 'Nach Inaktivität entfernen';
$string['updatelimit'] = 'Maximale Orte zum hochladen';
$string['zoomlevel'] = 'Anfängliche Zoom-Stufe';

$string['configcentrelat'] = 'Anfängliche zentrale Breite der Karte - in ganzem Dezimalformat (keine Grad/Minuten)';
$string['configcentrelng'] = 'Anfängliche zentrale Länge der Karte - in ganzem Dezimalformat (keine Grad/Minuten)';
$string['configcentreuser'] = 'Karte auf den augenblicklichen Besucherort hin zentrieren mit obiger Zoomstufe. Diese Einstellung hat Vorrang gegenüber obigen Breite/Länge Koordinaten, es sei denn der augenblickliche Besucher hat keinen gültigen Ort';
$string['configdebug'] = 'Während Cron läuft Fehlermeldungen anzeigen';
$string['configgoogleapikey'] = 'Google Maps API Schlüssel, enthält einen Schlüssel von $a';
$string['configoffline'] = 'Offline Teilnehmer auch anzeigen?';
$string['configtimetosee'] = 'Anzahl an Minuten, die eine Periode von Inaktivität bestimmen, nach der ein Teilnehmer nicht mehr länger als online angesehen wird.';
$string['configupdatelimit'] = 'Maximale Zahl an Orten für ein Hochladen bei jedem Cron damit es keine Auswirkung auf die Arbeitsleistung hat. Dies muss eine ganze Zahl größer oder gleich 0 sein. Beim Setzen von 0 werden alle Datensätze aktualisiert.';
$string['configzoomlevel'] = 'Anfängliche Zoomstufe der Karte.';

$string['periodnminutes'] = 'der letzten {$a} Minuten';
