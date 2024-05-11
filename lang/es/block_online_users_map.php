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
 * @copyright iplusacademy.org (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    Alex Little
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

$string['centrelat'] = 'Latitud inicial';
$string['centrelng'] = 'Longitud inicial';
$string['centreuser'] = 'Centro en la ubicación de los usuarios';
$string['configcentrelat'] = 'Latitud inicial del centro del mapa - en grados decimales (no grados/minutos)';
$string['configcentrelng'] = 'Longitud inicial del centro del mapa - en grados decimales (no grados/minutos)';
$string['configcentreuser'] = 'Centro del mapa para la localizacion actual de usuarios, con el nivel de zoom establecido arriba. Esta configuracion tiene prioridad sobre las coordenadas de Lat/Long de arriba, salvo que el usuario no posea una localizacion valida';
$string['configdebug'] = 'Muestre los mensajes de depuracion cunado se ejecute el cron';
$string['configgoogleapikey'] = 'Key de la API Google Maps';
$string['confignamesonmap'] = '¿Deben mostrarse en el mapa los nombres de usuario?  Si no está marcado este recuadro, la ciudad del usuario se mostrará como nombre.';
$string['configoffline'] = 'Mostrar los usuarios fuera de linea tambien?';
$string['configtimetosee'] = 'Numero de minutos que determinan el periodo de inactividad, luego del cual los usuarios son considerado fuera de linea.';
$string['configtype'] = 'Seleccione el proveedor del mapa que vaya a utilizar';
$string['configupdatelimit'] = 'Número máximo de ubicaciones que se actualizarán en cada cron; para que no afecte al rendimiento. Debe ser un entero mayor o igual a 0. Cuando se establezca en 0, se actualizarán todos los registros.';
$string['configzoomlevel'] = 'Zoom inicial del mapa';
$string['debug'] = 'Mostrar mensajes de depuración';
$string['googleapikey'] = 'Clave de la API de Google Maps';
$string['namesonmap'] = 'Mostrar nombres de usuario';
$string['offline'] = 'Mostrar usuarios fuera de línea';
$string['online_users_map:addinstance'] = 'Añadir un nuevo bloque del mapa de usuarios en línea';
$string['online_users_map:myaddinstance'] = 'Añadir un nuevo bloque del mapa de usuarios en línea a la página Mi Moodle';
$string['periodnminutes'] = 'Ultimos {$a} minutos';
$string['pluginname'] = 'Mapa de Usuarios en linea';
$string['timetosee'] = 'Eliminar después de estar inactivo';
$string['title'] = 'De dónde proceden nuestros estudiantes';
$string['type'] = 'Tipo de mapa que se va a utilizar';
$string['updatelimit'] = 'Máximas ubicaciones que se actualizarán';
$string['zoomlevel'] = 'Nivel de zoom inicial';
