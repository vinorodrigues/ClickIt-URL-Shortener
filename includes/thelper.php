<?php
/**
 * @package    c1k.it
 * @author     Vino Rodrigues
 * @copyright  Tecsmith.com.au
 *   See LICENSE.TXT for copyright notice and details.
 * @license    Creative Commons Attribution-ShareAlike 3.0 Unported License
 * @author     Vino Rodrigues
 *   clickit [dot] source [at] mail [dot] vinorodrigues [dot] com
 * @version    $Id$
 */

if (!defined('IN_CLICKIT')) die('Restricted');

/**
 * Output table rows based on field values
 * @param string $field
 * @param any $value
 * @param string $type
 * @param array $data
 * @param bool $required
 * @param bool $last
 */
function output_field($field, $value,
	$type = '', $data = NULL, $required = FALSE, $last = FALSE,
	$col3 = NULL) {

	global $run, $tab;
	if (!isset($run)) $run = 0;
	if (!isset($tab)) $tab = 0;
	$run++;  $tab++;
	print "\n<tr class=\"row_" . $run . ' ' .
		(is_odd($run) ? 'odd' : 'even') .
		(($run == 1) ? ' first' : '') .
		($last ? ' last' : '') .
		((($run == 1) && $last) ? ' single' : '') .
		"\"><th class=\"col_1 p_$field\"><label for=\"f_$field" .
		"\">" . T('FIELD_' . strtoupper($field)) . "</label>:" .
		(($required && ($type != '')) ? '<span class="required"></span>' : '');

	print "</th><td class=\"col_2 f_$field\">";

	switch ($type) :
		case 'email' :
		case 'password' :
		case 'number' :
		case 'url' :
		case 'text' :
			print '<input type="' . __('text', $type, TRUE) . '" id="f_' .
				$field . '" name="' . $field . '" value="' . $value .
				'" tabindex="' . $tab . '"';
			if ($data !== NULL)
				foreach ($data as $attr => $val) :
					print ' ' . $attr . '="' . $val . '"';
				endforeach;
			if ($required) __('', ' required="required"');
			print ' />';
			break;
		case 'checkbox' :
			?><input type="checkbox" id="f_<?php print $field; ?>" name="<?php print $field; ?>" <?php if (boolval($value)) print "checked=\"checked\""; ?> value="1" tabindex="<?php print $tab; ?>" /><?php
			break;
		case 'textarea' :
			?><textarea id="f_<?php print $field; ?>" name="<?php print $field; ?>" tabindex="<?php print $tab; ?>" rows="2" cols="38"><?php print $value; ?></textarea><?php
			break;
		case 'select' :
			?><select id="f_<?php print $field; ?>" name="<?php print $field; ?>" tabindex="<?php print $tab; ?>"><?php
			foreach ($data as $val => $desc) :
				print "<option value=\"$val\"" .
					( ($val == $value) ? ' selected="selected"' : '' ) .
					">$val - $desc</option>";
			endforeach;
			?></select><?php
			break;
		case 'radio' :
			print "<fieldset id=\"f_$field\" tabindex=\"$tab\">";
			foreach ($data as $val => $desc) :
				print "<input type=\"radio\" name=\"$field\" value=\"$val\"" .
					" id=\"f_$field" . "_$val\"" .
					( ($val == $value) ? ' checked="checked"' : '' ) .
					"><label for=\"f_$field" . "_$val\">$desc</label><br />";
			endforeach;
			print "</fieldset>";
			break;
		case 'bool' :
			if (boolval($value)) :
				P('ON');
			else :
				P('OFF');
			endif;
			break;
		case 'callback' :
			print '<fieldset id="f_' . $field . '">';
			print '<input type="text" id="f_x_' . $field . '" value="' . $value .
				'"  readonly="readonly" disabled="disabled" /><br />';
			$newvalue = call_user_func($data['callback']);
			?><input type="checkbox" id="f_c_<?php print $field; ?>" name="<?php print $field; ?>" value="<?php print $newvalue; ?>" tabindex="<?php print $tab; ?>" /> <?php
			?><label for="f_c_<?php print $field; ?>"><?php print $data['label']; ?></label> <?php
			print '</fieldset>';
			break;
		default :
			print $value;
	endswitch;

	print "</td>";

	if ($col3 !== NULL)
		print '<td class="col_3">' . $col3 . '</td>';

	print "</tr>";
}
