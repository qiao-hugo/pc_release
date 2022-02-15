<?php
/**
 * php5的防跨站库
 */
/**
 * 是否开启验证
 */
$GLOBALS['csrf']['defer'] = false;
/**
 * 令牌默认有效期两小时
 */
$GLOBALS['csrf']['expires'] = 7200;
/**
 * 验证失败的回调函数 输出提示信息
 */
$GLOBALS['csrf']['callback'] = 'vtResponseForIllegalAccess'; //'csrf_callback'
/**
 * 是否加载重写AJAX的JS库支持Jqueru及其他
 */
$GLOBALS['csrf']['rewrite-js'] = 'libraries/csrf-magic/csrf-magic.js';
/**
 * 随机密钥 生成
 */
$GLOBALS['csrf']['secret'] = '82e94c21e280c809731217fdd13f5fefd913f1c1';
// nota bene: library code should use csrf_get_secret() and not access this global directly
/**
 * 控制输出
 */
$GLOBALS['csrf']['rewrite'] = true;

/**
 *令牌绑定用户IP
 */
$GLOBALS['csrf']['allow-ip'] = true;
/**
 *令牌的名称
 */
$GLOBALS['csrf']['cookie'] = '__vtrfck'; // __csrf_cookie
/**
 * 密钥绑定用户
 */
$GLOBALS['csrf']['user'] = false;
/**
 * 匿名会话
 */
$GLOBALS['csrf']['key'] = false;
/**
 * 表单令牌
 */
$GLOBALS['csrf']['input-name'] = '__vtrftk'; // __csrf_magic
/**
 * 开启iframe保护
 */
$GLOBALS['csrf']['frame-breaker'] = true;
/**
 * 是否session保存key
 */
$GLOBALS['csrf']['auto-session'] = true;
/**
 * 创建网页标签
 */
$GLOBALS['csrf']['xhtml'] = true;

// Don't edit this!
$GLOBALS['csrf']['version'] = '1.0.4';

/**
 * 添加表单令牌
 */
function csrf_ob_handler($buffer, $flags) {
    // 判断是否网页才写入
    static $is_html = false;
    static $is_partial = false;
    if (!$is_html) {
        // not HTML until proven otherwise
        if (stripos($buffer, '<html') !== false) {
            $is_html = true;
        } else {
			// Customized to take the partial HTML with form
			$is_html = true;
			$is_partial = true;
			// Determine based on content type.
			$headers = headers_list();
			foreach ($headers as $header) {
				if ($is_html) break;
				else if (stripos('Content-type', $header) !== false && stripos('/html', $header) === false) {
					$is_html = false;
				}
			}
			if (!$is_html) return $buffer;
        }
    }
    $count=1;
    $tokens = csrf_get_tokens();
    $name = $GLOBALS['csrf']['input-name'];
    $endslash = $GLOBALS['csrf']['xhtml'] ? ' /' : '';
    $input = "<input type='hidden' name='$name' value=\"$tokens\"$endslash>";
    $buffer = preg_replace('#(<form[^>]*method\s*=\s*["\']post["\'][^>]*>)#i', '$1' . $input, $buffer);
    if ($GLOBALS['csrf']['frame-breaker'] && !$is_partial) {
        $buffer = preg_replace('/<\/head>/', '<script type="text/javascript">if (top != self) {top.location.href = self.location.href;}</script></head>', $buffer,$count);
    }
    if (($js = $GLOBALS['csrf']['rewrite-js']) && !$is_partial) {
        $buffer = preg_replace(
            '/<\/head>/',
            '<script type="text/javascript">'.
                'var csrfMagicToken = "'.$tokens.'";'.
                'var csrfMagicName = "'.$name.'";</script>'.
            '<script src="'.$js.'" type="text/javascript"></script></head>',
            $buffer,$count
        );
        $script = '<script type="text/javascript">CsrfMagic.end();</script>';
        
        $buffer = preg_replace('/<\/body>/', $script . '</body>', $buffer, $count);
        if (!$count) {
            $buffer .= $script;
        }
    }
    return $buffer;
}

/**
 * 检查POST请求
 * @param bool $fatal Whether or not to fatally error out if there is a problem.
 * @return True if check passes or is not necessary, false if failure.
 */
function csrf_check($fatal = true) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return true;
	if(isset($_REQUEST['view']) && $_REQUEST['view']=='List') return true;
    csrf_start();
    $name = $GLOBALS['csrf']['input-name'];
    $ok = false;
    $tokens = '';
    do {
        if (!isset($_POST[$name])) break;
        // we don't regenerate a token and check it because some token creation
        // schemes are volatile.
        $tokens = $_POST[$name];
        if (!csrf_check_tokens($tokens)) break;
        $ok = true;
    } while (false);
    if ($fatal && !$ok) {
        $callback = $GLOBALS['csrf']['callback'];
        if (trim($tokens, 'A..Za..z0..9:;,') !== '') $tokens = 'hidden';
        $callback($tokens);
        exit;
    }
    return $ok;
}

/**
 * 创建令牌
 */
function csrf_get_tokens() {
    $has_cookies = !empty($_COOKIE);
    //依赖用户的cookie
    $secret = csrf_get_secret();
    if (!$has_cookies && $secret) {
        // :TODO: Harden this against proxy-spoofing attacks
        $ip = ';ip:' . csrf_hash($_SERVER['IP_ADDRESS']);
    } else {
        $ip = '';
    }
    csrf_start();

    // These are "strong" algorithms that don't require per se a secret
    if (session_id()) return 'sid:' . csrf_hash(session_id()) . $ip;
    if ($GLOBALS['csrf']['cookie']) {
        $val = csrf_generate_secret();
        setcookie($GLOBALS['csrf']['cookie'], $val);
        return 'cookie:' . csrf_hash($val) . $ip;
    }
    if ($GLOBALS['csrf']['key']) return 'key:' . csrf_hash($GLOBALS['csrf']['key']) . $ip;
    // These further algorithms require a server-side secret
    if (!$secret) return 'invalid';
    if ($GLOBALS['csrf']['user'] !== false) {
        return 'user:' . csrf_hash($GLOBALS['csrf']['user']);
    }
    if ($GLOBALS['csrf']['allow-ip']) {
        return ltrim($ip, ';');
    }
    return 'invalid';
}

function csrf_flattenpost($data) {
    $ret = array();
    foreach($data as $n => $v) {  $ret = array_merge($ret, csrf_flattenpost2(1, $n, $v));  }
    return $ret;
}
function csrf_flattenpost2($level, $key, $data) {
    if(!is_array($data)) return array($key => $data);
    $ret = array();
    foreach($data as $n => $v) { $nk = $level >= 1 ? $key."[$n]" : "[$n]";  $ret = array_merge($ret, csrf_flattenpost2($level+1, $nk, $v));}
    return $ret;
}

/**
 * @param $tokens is safe for HTML consumption
 */
function csrf_callback($tokens) {
    // (yes, $tokens is safe to echo without escaping)
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
    $data = '';
    foreach (csrf_flattenpost($_POST) as $key => $value) {
        if ($key == $GLOBALS['csrf']['input-name']) continue;
        $data .= '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'" />';
    }
    echo "<html><head><title>验证失败</title></head> <body><p>CSRF check failed. Your form session may have expired, or you may not have cookies enabled.</p><form method='post' action=''>$data<input type='submit' value='Try again' /></form><p>Debug: $tokens</p></body></html>";
}

/**
 * 失败响应
 */
function vtResponseForIllegalAccess() {  echo '错误的请求！<script>settimeout("window.location.href=index.php",1000)</script>';}

/**
 * 混合型验证
 */
function csrf_check_tokens($tokens) {
    if (is_string($tokens)) $tokens = explode(';', $tokens);
    foreach ($tokens as $token) { if (csrf_check_token($token)) return true;}
    return false;
}

/**
 * 令牌有效性检查
 */
function csrf_check_token($token) {
    if (strpos($token, ':') === false) return false;
    list($type, $value) = explode(':', $token, 2);
    if (strpos($value, ',') === false) return false;
    list($x, $time) = explode(',', $token, 2);
    if ($GLOBALS['csrf']['expires']) {
		//令牌有效期
        if (time() > $time + $GLOBALS['csrf']['expires']) return false;
    }
    switch ($type) {
        case 'sid':
            return $value === csrf_hash(session_id(), $time);
        case 'cookie':
            $n = $GLOBALS['csrf']['cookie'];
            if (!$n) return false;
            if (!isset($_COOKIE[$n])) return false;
            return $value === csrf_hash($_COOKIE[$n], $time);
        case 'key':
            if (!$GLOBALS['csrf']['key']) return false;
            return $value === csrf_hash($GLOBALS['csrf']['key'], $time);
        case 'user':
            if (!csrf_get_secret()) return false;
            if ($GLOBALS['csrf']['user'] === false) return false;
            return $value === csrf_hash($GLOBALS['csrf']['user'], $time);
        case 'ip':
            if (!csrf_get_secret()) return false;
            if ($GLOBALS['csrf']['user'] !== false) return false;
            //if (!empty($_COOKIE)) return false;
            if (!$GLOBALS['csrf']['allow-ip']) return false;
            return $value === csrf_hash($_SERVER['IP_ADDRESS'], $time);
    }
    return false;
}

/**
 * 配置
 */
function csrf_conf($key, $val) {
    if (!isset($GLOBALS['csrf'][$key])) { trigger_error('No such configuration ' . $key, E_USER_WARNING);  return; }
    $GLOBALS['csrf'][$key] = $val;
}

/**
 * 开启session
 */
function csrf_start() { if ($GLOBALS['csrf']['auto-session'] && !session_id()) {  session_start(); }}

/**
 * 获取密钥 没有自动生成
 */
function csrf_get_secret() {
    if ($GLOBALS['csrf']['secret']) return $GLOBALS['csrf']['secret'];
    $dir = dirname(__FILE__); $file = $dir . '/../../config.csrf-secret.php'; $secret = '';
    if (file_exists($file)) { include $file;  return $secret;}
    if (is_writable($dir)) { $secret = csrf_generate_secret(); $fh = fopen($file, 'w'); fwrite($fh, '<?php $secret = "'.$secret.'";' . PHP_EOL); fclose($fh); return $secret; } return '';
}

/**
 * 密钥生成
 */
function csrf_generate_secret($len = 32) { $r = ''; for($i=0;$i<32;$i++){$r.=chr(mt_rand(0,255));} $r.=time().microtime(); return sha1($r);}
/**
 * 令牌生成
*/
function csrf_hash($value, $time = null) { if (!$time) $time = time();  return sha1(csrf_get_secret().$value.$time).','.$time;}
// Load user configuration
if (function_exists('csrf_startup')) csrf_startup();
// Initialize our handler
if ($GLOBALS['csrf']['rewrite'])     ob_start('csrf_ob_handler');
// Perform check
if (!$GLOBALS['csrf']['defer'])      csrf_check();
