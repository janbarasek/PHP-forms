<?php

/**
 * forms.php
 *
 * Autovalidate html form system
 *
 * @category   Form Framework
 * @author     Jan Barášek
 * @copyright  2016 Baraja
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 */

class Forms {
	// --- default error messages ---
	public static $error = array(
		'unset' => 'Toto pole je povinné!',
		'text' => 'Byl zadán nevhodný formát.',
		'pass_nosame' => 'Hesla se neshodují.',
		'length' => 'Vstup musí mít přesnou délku.',
		'short' => 'Vstup je příliš krátký.',
		'long' => 'Vstup je příliš dlouhý.',
		'numeric' => 'Toto není platné číslo.',
		'mail' => 'Toto není platný formát e-mailu.',
		'contain' => 'Vstup nemá požadovaný obsah.',
		'captcha' => 'Kontrolní kód nebyl opsán správně.',
	);

	// --- output to html code ---
	public static function printer($data, $param) {
		if (self::$is_post && self::$is_ok) {

			// -----
			// -----
			// -----
			// Here is your data output

					echo '<pre><code>';
						var_dump(self::$data);
					echo '</code></pre>';

			// -----
			// -----
			// -----
		} else {
			echo '<div style="max-width: 800px; background: white; padding: 32px; margin: auto;">';
				echo $data;
			echo '</div>';
		}
		return true;
	}

	// ----------------------------------------------
	// -------------------- CORE --------------------
	// ----------------------------------------------
	
	public static $data = array(), $data_type = array();
	public static $is_ok = true, $is_post = false, $sum_captcha = 0;

	public static function render($data, $param) {
		if (isset($param['method'])) $method = $param['method']; else $method = 'GET';
		if (isset($param['action'])) $action = $param['action']; else $action = '';
		if (@$_GET['formsubmit']) $validate = true; else $validate = false;
		if (!isset($_GET['formsubmit']) && @$_POST['formsubmit']) $validate = true; else $validate = false;
		if ($validate) self::$is_post = true;
		// -----
		// Start of form
		$out = '<form action="http'.(!empty($_SERVER['HTTPS'])?'s':'').'://'. $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] .'" method="'.$method.'">';
		for ($i=0;isset($data[$i]);$i++) {
			if ($method == 'GET') $data[$i]['input'] = @$_GET[$data[$i]['id']];
			if ($method == 'POST') $data[$i]['input'] = @$_POST[$data[$i]['id']];
			self::$data[$data[$i]['id']] = $data[$i]['input'];
			self::$data_type[$data[$i]['id']] = $data[$i]['type'];
			// -----
			// HTML render
				$form_valid = self::valid($validate, $data[$i]['type'], $data[$i]['input'], @$data[$i]['valid'], $data[$i]['required']); // validator
				$is_valid = $form_valid['valid'];
				if (!$is_valid) self::$is_ok = false;
				if (@$form_valid['string']) $data[$i]['input'] = $form_valid['string'];
				// -----
				$out .= '<div class="form-group'. (!$is_valid?' has-error':'') .' has-feedback">';
					if ($data[$i]['type'] != 'checkbox') $out .= '<label class="control-label" for="f'.$i.'">'.($data[$i]['required']?'* ':'').$data[$i]['name'].':</label>';
					if (isset($data[$i]['note'])) $out .= '<span id="helpBlock2" class="help-block">'.$data[$i]['note'].'</span>';
					switch ($data[$i]['type']) {
						case 'area':
							$out .= '<textarea name="'.$data[$i]['id'].'" class="form-control" rows="5" id="f'.$i.'" placeholder="'.(@$data[$i]['data']?$data[$i]['data']:@$data[$i]['none']).'">'.$data[$i]['input'].'</textarea>';
							break;
						case 'checkbox':
							$out .= '<div'. (!$is_valid?' style="color: #a94442;"':'') .'><label>';
								$out .= '<input name="'.$data[$i]['id'].'" id="f'.$i.'" value="'.$data[$i]['id'].'" type="checkbox"'.($data[$i]['input'] == $data[$i]['id']?' checked="checked"':'').'>&nbsp;'.($data[$i]['required']?'* ':'').$data[$i]['name'];
							$out .= '</label></div>';
							break;
						case 'radio':
						case 'point':
							for ($j=0;isset($data[$i]['data'][$j]);$j++) {
								$out .= '<div><label class="radio-inline">';
									$out .= '<input type="radio" name="'.$data[$i]['id'].'" id="f'.$i.'_'.$j.'" value="'.$j.'|'.$data[$i]['data'][$j].'"'.($data[$i]['input'] == $j.'|'.$data[$i]['data'][$j]?' checked="checked"':'').'> '.$data[$i]['data'][$j];
								$out .= '</label></div>';
							}
							break;
						case 'captcha':
							if (self::$sum_captcha == 0) {
								$out .= '<div>';
									$captcha = self::captcha();
									$out .= $captcha['img'];
									$out .= '<input type="hidden" name="captcha_hash" value="'.$captcha['hash'].'">';
									$out .= '<input type="text" name="captcha" class="form-control" id="f'.$i.'" aria-describedby="inputSuccess3Status">';
								$out .= '</div>';
								self::$sum_captcha++;
							} else $out .= '<div style="font-size: 18pt; color: red;">FATAL ERROR: You can use only one captcha code!</div>';
							break;
						default:
							$out .= '<input name="'.$data[$i]['id'].'" type="'.($data[$i]['type']?$data[$i]['type']:'text').'" class="form-control" id="f'.$i.'" value="'.$data[$i]['input'].'"'.(isset($data[$i]['data'])?' placeholder="'.$data[$i]['data'].'"':'').' aria-describedby="inputSuccess3Status">';
							break;
					}
					if (!$is_valid) $out .= '<span id="helpBlock2" class="help-block">'. (@$data[$i]['valid']['error']?$data[$i]['valid']['error']:self::$error[$form_valid['error']]) .'</span>';
				$out .= '</div>';
			// -----
		}
		// -----
		// Submit buttons
		if (isset($param['submit'])) {
			$out .= '<input type="submit" name="formsubmit" value="'.$param['submit'].'" class="btn btn-defaull">';
		} else {
			$out .= '<input type="submit" name="formsubmit" value="Odeslat" class="btn btn-defaull">';
		}
		// -----
		// Exit of form
		$out .= '<p style="margin: 8px 0;">* Položky označené hvězdičkou jsou povinné.</p>';
		$out .= '</form>';
		// -----
		// HTML printer
		self::printer($out, $param);
		return true;
	}

	public static function valid($validate, $type, $data, $valid, $required) {
		$filter['valid'] = true;
		if ($validate) {
			if ($type == 'password') {
				$password_inputs = self::$data_type;
				$password_data = self::$data;
				$first_pass = ''; $pass_status = true;
				foreach ($password_data as $key => $value) {
					if ($password_inputs[$key] == 'password') {
						if ($value && !$first_pass) {
							$first_pass = $value;
						} else {
							if ($first_pass != $value) $pass_status = false;
						}
					}
				}
				if (!$pass_status) {
					$filter['valid'] = false;
					$filter['error'] = 'pass_nosame';
				}
			}
			if ($type == 'captcha' && $filter['valid']) {
				$captcha = @$_GET['captcha_hash'];
				if (!isset($captcha)) $captcha = @$_POST['captcha_hash'];
				if (md5(strtoupper($data)) != $captcha){
					$filter['valid'] = false;
					$filter['error'] = 'captcha';
				}
			}
			if (count($valid) > 0) {
				if (@$valid['filter'] == 'mail') {
					$valid_mail = filter_var($data, FILTER_VALIDATE_EMAIL);
					if (!$valid_mail) {
						$filter['valid'] = false;
						$filter['error'] = 'mail';
					}
				}
				if (@$valid['filter'] == 'numeric') {
					$filter['string'] = preg_replace('/\D/', '', $data);
					if (strlen($filter['string']) < 1) {
						$filter['valid'] = false;
						$filter['error'] = 'numeric';
					}
				}
				if (isset($valid['strlen'])) {
					$strlen = preg_replace('/\D/', '', $valid['strlen']);
					if ($valid['strlen'][0] != '+' && $valid['strlen'][0] != '-') {
						if (mb_strlen($data) != $strlen) {
							$filter['valid'] = false;
							$filter['error'] = 'length';
						}
					} elseif ($valid['strlen'][0] == '+') {
						if (mb_strlen($data) < $strlen) {
							$filter['valid'] = false;
							$filter['error'] = 'short';
						}
					} else {
						if (mb_strlen($data) > $strlen) {
							$filter['valid'] = false;
							$filter['error'] = 'long';
						}
					}
				}
				if (isset($valid['contain'])) {
					if (!(strpos($data, $valid['contain']) !== false)) {
						$filter['valid'] = false;
						$filter['error'] = 'no-contain';
					}
				}
				
			}
			// -----
			if (!$data && $required) {
				$filter['valid'] = false;
				$filter['error'] = 'unset';
			}
			// -----
			return $filter;
		}
		return $filter;
	}
	
	public static function captcha() {
		// --- random string ---
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 8; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		// --- generator of image ---
		ob_start();
		$v = rand($minNUM,($minNUM*10)-1);
		$im = imagecreate(120, 64);
		imagecolortransparent($im, imagecolorallocate($im, 25, 255, 255));
		$textcolor = imagecolorallocate($im, 0, 0, 166);
		$red = imagecolorallocate($im, 169, 68, 66);
		for ($i=0;$i<=5;$i++) {
			imageline($im, rand(0, 120),rand(0, 64), rand(0, 120), rand(0, 64), $red) ;
		}
		imagestring($im, 10, 25, 24, $randomString, $textcolor);
		imagepng($im);
		$outputBuffer = ob_get_clean();
		$base64 = base64_encode($outputBuffer);
		$captcha['img'] = '<img src="data:image/png;base64,'.$base64.'" style="border: 1px solid #aaa; margin: 8px 0;">';
		$captcha['original'] = $randomString;
		$captcha['hash'] = md5(strtoupper($randomString));
		return $captcha;
	}
}