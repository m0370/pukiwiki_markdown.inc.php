<?php
/**
 * Markdon Syntax
 *
 * @author     sonots
 * @license    http://www.gnu.org/licenses/gpl.html GPL v2
 * @link       http://lsx.sourceforge.jp/?Plugin%2Fmarkdown.inc.php
 * @version    $Id: markdown.inc.php,v 1.2 2007-02-24 16:28:39Z sonots $
 * @package    plugin
 */
 // v1.21 PHP8.0 & Markdown PHP 1.9.1対応 2021-12-11 byはいふん
 // v1.22 PHP8.0対応 プラグイン、Pukiwiki式リンク、Markdown式リンクを使用可能に by m0370（erusev/markdown必要）

function plugin_markdown_convert()
{
    require_once(PLUGIN_DIR . 'vendor/autoload.php');

    $args = func_get_args();
    $body = array_pop($args);
    
    if (! is_array($body)) $lines = explode("\r", $body);
    foreach ( $lines as &$line ) {
		$matches = array();
		if ( preg_match('/^\\!([a-zA-Z0-9_]+)(\\(([^\\)\\n]*)?\\))?/', $line, $matches) ) {
			$plugin = $matches[1];
			if ( exist_plugin_convert($plugin) ) {
				$name = 'plugin_' . $matches[1] . '_convert';
				$params = array();
				if ( isset($matches[3]) ) {
					$params = explode(',', $matches[3]);
				}
				$line = call_user_func_array($name, $params);
			} else {
				$line = "plugin ${plugin} failed.";
			}
		} else {
		    $line = preg_replace('/\[(.*?)\]\((https?\:\/\/[\-_\.\!\~\*\'\(\)a-zA-Z0-9\;\/\?\:\@\&\=\+\$\,\%\#]+)( )?(\".*\")?\)/', "[[$1>$2]]", $line);
		    $line = make_link($line);
			// ファイル読み込んだ場合に改行コードが末尾に付いていることがあるので削除
			// 空白は削除しちゃだめなのでrtrim()は使ってはいけない
            $line = str_replace(array("\r\n","\n","\r"), "", $line);
		}
	}
	unset($line);

	$body = implode("\n", $lines);

    $parsedown = new \Parsedown(); //Parsedown→ParsedownExtraに変更しても良い
	$result = $parsedown ->setBreaksEnabled(true) ->text($body); // ->setBreaksEnabled(true)を付けて改行を可能にしている

	return $result;
}

function plugin_markdown_is_edit_auth($page, $user = '')
{
    global $edit_auth, $edit_auth_pages, $auth_method_type;
    if (! $edit_auth) {
        return FALSE;
    }
    // Checked by:
    $target_str = '';
    if ($auth_method_type == 'pagename') {
        $target_str = $page; // Page name
    } else if ($auth_method_type == 'contents') {
        $target_str = join('', get_source($page)); // Its contents
    }

    foreach($edit_auth_pages as $regexp => $users) {
        if (preg_match($regexp, $target_str)) {
            if ($user == '' || in_array($user, explode(',', $users))) {
                return TRUE;
            }
        }
    }
    return FALSE;
}
?>
