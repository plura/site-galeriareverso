<?php

$pagename = 'exposicoes';

$path1 = "$pagename/2017/";

$regex1 = '/^' . $pagename . '\/([0-9]+)\/?$/';


$path2 = "$pagename/2021/?lang=pt-pt&test=1";

$regex2 = '/^' . $pagename . '\/([0-9]+)\/?(\?(.+))?$/';

$regex3 = '/^' . $pagename . '\/([0-9]+)\/\?(.+)$/';

preg_match($regex1, $path1, $matches1);

preg_match($regex2, $path2, $matches2);

preg_match($regex2, $path1, $matches3);

preg_match($regex2, $path2, $matches4);

/*
print_r( $matches1 );

echo "\n\n\n\n";

print_r( $matches2 );

echo "\n\n\n\n";

print_r( $matches3 );

echo "\n\n\n\n";

print_r( $matches4 );*/


$a = ['en' => 1, 'pt-pt' => 3];

foreach( $a as $v ) echo "$v<p/>";

