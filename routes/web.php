<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    function obtenerNumeroDesdeColumna($columna)
    {
        $columna = Str::upper($columna);
        $columnaLongitud = strlen($columna);
        $numero = 0;
        $alfabeto = range('A', 'Z');
        /*  DD(count($alfabeto)); */
        for ($i = 0; $i < $columnaLongitud; $i++) {
            /* DD($columna[$i]); */
            $posicionEnAlfabeto = array_search($columna[$i], $alfabeto);
            $potenciaDe26 = 1;

            for ($j = 0; $j < $columnaLongitud - $i - 1; $j++) {
                $potenciaDe26 *= 26;
            }
           /*  $numero += ($posicionEnAlfabeto + 1) * pow($columnaLongitud, 26, -$i - 1); */
            $numero += ($posicionEnAlfabeto + 1) * $potenciaDe26;
        }

        return $numero;
    }

    echo "Columna A Numero: " . obtenerNumeroDesdeColumna('A') . '<br>';
    echo "Columna B Numero: " . obtenerNumeroDesdeColumna('B') . '<br>';
    echo "Columna Z Numero: " . obtenerNumeroDesdeColumna('Z') . '<br>';
    echo "Columna AA Numero: " . obtenerNumeroDesdeColumna('AA') . '<br>';
    echo "Columna CA Numero: " . obtenerNumeroDesdeColumna('CA') . '<br>';
    echo "Columna XFD Numero: " . obtenerNumeroDesdeColumna('XFD') . '<br>';
    echo "Columna ZZZZ Numero: " . obtenerNumeroDesdeColumna('ZZZZ') . '<br>';

    /* return view('welcome'); */
});
