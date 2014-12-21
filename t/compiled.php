<?php return function ($in, $debugopt = 1) {
    $cx = array(
        'flags' => array(
            'jstrue' => true,
            'jsobj' => true,
            'spvar' => true,
            'prop' => false,
            'method' => false,
            'mustlok' => false,
            'mustsec' => false,
            'echo' => true,
            'debug' => $debugopt,
        ),
        'constants' =>  array(
            'DEBUG_ERROR_LOG' => 1,
            'DEBUG_ERROR_EXCEPTION' => 2,
            'DEBUG_TAGS' => 4,
            'DEBUG_TAGS_ANSI' => 12,
            'DEBUG_TAGS_HTML' => 20,
        ),
        'helpers' => array(),
        'blockhelpers' => array(),
        'hbhelpers' => array(            'hard' => function($a, $b, $c,$options) {
            return $options['fn']();
        },
),
        'partials' => array('temp' => function ($cx, $in, $sp) { 
                $tempfunc = function () use($cx, $in) {
                    ob_start();echo ' hello
',$cx['funcs']['hbch']($cx, 'hard', array(array('abc','-','f'),array()), $in, function($cx, $in) {echo '    hello ',$cx['funcs']['encq']($cx, ((isset($in['stank']) && is_array($in)) ? $in['stank'] : null)),' done
';}),' thanks god.
';return ob_get_clean();
                };
                $str = $tempfunc();
                return implode("\n",
                    array_map(
                        function ($line) use($sp) {
                            ob_start();echo  $sp , $line ;return ob_get_clean();
                        },
                        explode("\n", $str)
                    )
                ); },),
        'scopes' => array($in),
        'sp_vars' => array('root' => $in),
'funcs' => array(
    'hbch' => function ($cx, $ch, $vars, $op, $cb = false, $inv = false) {
        $isBlock = (is_object($cb) && ($cb instanceof Closure));
        $args = $vars[0];
        $options = array(
            'name' => $ch,
            'hash' => $vars[1]
        );

        if ($isBlock) {
            $options['fn'] = function ($context = '_NO_INPUT_HERE_') use ($cx, $op, $cb) {
                if ($cx['flags']['echo']) {
                    ob_start();
                }
                if ($context === '_NO_INPUT_HERE_') {
                    $ret = $cb($cx, $op);
                } else {
                    $cx['scopes'][] = $op;
                    $ret = $cb($cx, $context);
                    array_pop($cx['scopes']);
                }
                return $cx['flags']['echo'] ? ob_get_clean() : $ret;
            };
        }

        if ($inv) {
            $options['inverse'] = function ($context = '_NO_INPUT_HERE_') use ($cx, $op, $inv) {
                if ($cx['flags']['echo']) {
                    ob_start();
                }
                if ($context === '_NO_INPUT_HERE_') {
                    $ret = $inv($cx, $op);
                } else {
                    $cx['scopes'][] = $op;
                    $ret = $inv($cx, $context);
                    array_pop($cx['scopes']);
                }
                return $cx['flags']['echo'] ? ob_get_clean() : $ret;
            };
        }

        // prepare $options['data']
        if ($cx['flags']['spvar']) {
            $options['data'] = $cx['sp_vars'];
            $options['data']['root'] = $cx['scopes'][0];
        }

        $args[] = $options;
        $e = null;
        $r = true;

        try {
            $r = call_user_func_array($cx['hbhelpers'][$ch], $args);
        } catch (Exception $E) {
            $e = "LCRun3: call custom helper '$ch' error: " . $E->getMessage();
        }

        if ($r === false) {
            if ($e === null) {
                $e = "LCRun3: call custom helper '$ch' error";
            }
        }

        if($e !== null) {
            if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_LOG']) {
                error_log($e);
            }
            if ($cx['flags']['debug'] & $cx['constants']['DEBUG_ERROR_EXCEPTION']) {
                throw new Exception($e);
            }
        }

        return $cx['funcs']['chret']($r, $isBlock ? 'raw' : $op);
    },
    'encq' => function ($cx, $var) {
        return preg_replace('/`/', '&#x60;', preg_replace('/&#039;/', '&#x27;', htmlentities($cx['funcs']['raw']($cx, $var), ENT_QUOTES, 'UTF-8')));
    },
    'p' => function ($cx, $p, $v, $sp = '') {
        return call_user_func($cx['partials'][$p], $cx, is_array($v[0][0]) ? array_merge($v[0][0], $v[1]) : $v[0][0], $sp);
    },
    'chret' => function ($ret, $op) {
        if (is_array($ret)) {
            if (isset($ret[1]) && $ret[1]) {
                $op = $ret[1];
            }
            $ret = $ret[0];
        }

        switch ($op) {
            case 'enc':
                return htmlentities($ret, ENT_QUOTES, 'UTF-8');
            case 'encq':
                return preg_replace('/&#039;/', '&#x27;', htmlentities($ret, ENT_QUOTES, 'UTF-8'));
        }
        return $ret;
    },
    'raw' => function ($cx, $v) {
        if ($v === true) {
            if ($cx['flags']['jstrue']) {
                return 'true';
            }
        }

        if (($v === false)) {
            if ($cx['flags']['jstrue']) {
                return 'false';
            }
        }

        if (is_array($v)) {
            if ($cx['flags']['jsobj']) {
                if (count(array_diff_key($v, array_keys(array_keys($v)))) > 0) {
                    return '[object Object]';
                } else {
                    $ret = array();
                    foreach ($v as $k => $vv) {
                        $ret[] = $cx['funcs']['raw']($cx, $vv);
                    }
                    return join(',', $ret);
                }
            }
        }

        return "$v";
    },
)

    );
    
    ob_start();echo '    ccf
',$cx['funcs']['p']($cx, 'temp', array(array($in),array()), '  '),'  herer..
   why you include me.
   hdd';return ob_get_clean();
}
?>