<?php
require('src/lightncandy.php');
$dir = dirname(__FILE__);
$tmpl = <<<ABC
    ccf
  {{>temp}}
  herer..
   why you include me.
   hdd
ABC;
$phpStr = LightnCandy::compile($tmpl, [
    'flags' => LightnCandy::FLAG_ECHO |LightnCandy::FLAG_RUNTIMEPARTIAL | LightnCandy::FLAG_HANDLEBARSJS |/* LightnCandy::FLAG_ERROR_EXCEPTION |*/ LightnCandy::FLAG_STANDALONE ,
    'helpers' => [
        'abc' => function($args, $named){
                echo "heljo";
            }
    ],
    'hbhelpers' => Array(
        'mywith' => function ($a, $b, $c, $options) {
                return $options['fn']();
            },
        'hard' => function ($a, $b, $c,$options) {
            return $options['fn']();
        }
    ),
    'basedir' => [
        $dir
    ],
    'fileext' => [
        '.hbs'
    ]
]);  // compiled PHP code in $phpStr
$php_inc = $dir . "/compiled.php";
file_put_contents($php_inc, $phpStr);
$renderer = include($php_inc);

// Step 3. run native PHP render function any time
echo $renderer([
    'a' => [
        'name' => 'jack'
    ]
]);
//echo LightnCandy::getPHPCode("LightnCandy::getPHPCode");

