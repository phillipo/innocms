<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * Helper para Form
 * 
 * @category   KumbiaPHP
 * @package    Helpers 
 * @copyright  Copyright (c) 2005-2010 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class innoForm extends Form
{
    
    /**
     * Campo Select
     *
     * @param string $field nombre de campo
     * @param string $data array de valores para la lista desplegable
     * @param string|array $attrs atributos de campo
     * @param string $value
     * @return string
     */
    public static function select($field, $data, $attrs = NULL, $value = NULL, $indent = '')
    {
        if(is_array($attrs)){
            $attrs = Tag::getAttrs($attrs);
        }
        
        // Obtiene name, id y value (solo para autoload) para el campo y los carga en el scope
        extract(self::_getFieldData($field, $value === NULL), EXTR_OVERWRITE);
        
        $options = '';
        foreach($data as $k => $v) {
            $k = htmlspecialchars($k, ENT_COMPAT, APP_CHARSET);
            $options .= "<option value=\"$k\"";
            if($k == $value) {
                $options .= ' selected="selected"';
            }
            $options .= '>' . $indent . htmlspecialchars($v, ENT_COMPAT, APP_CHARSET) . '</option>';
        }
        
        return "<select id=\"$id\" name=\"$name\" $attrs>$options</select>";
    }
    
    /**
     * Campo checkbox
     *
     * @param string $field nombre de campo
     * @param string $checkValue valor en el checkbox
     * @param string|array $attrs atributos de campo
     * @param string $checked indica si se marca el campo
     * @return string
     */
    public static function check($field, $checkValue, $attrs = NULL, $checked = NULL)
    {
        if(is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
        
		// Obtiene name y id para el campo y los carga en el scope
		extract(self::_getFieldData($field, $checked === NULL), EXTR_OVERWRITE);
		
        if($checked || ($checked === NULL && $checkValue == $value)) {
            $checked = 'checked="checked"';
        }
        
        return "<input id=\"$id\" name=\"$name\" type=\"checkbox\" value=\"$checkValue\" $attrs $checked/>";
    }
    
    /**
     * Campo radio button
     *
     * @param string $field nombre de campo
     * @param string $radioValue valor en el radio
     * @param string|array $attrs atributos de campo
     * @param string $checked indica si se marca el campo
     * @return string
     */
    public static function radio ($field, $radioValue, $attrs = NULL, $checked = NULL)
    {
        if(is_array($attrs)){
            $attrs = Tag::getAttrs($attrs);
        }
		        
        // Obtiene name y id para el campo y los carga en el scope
		extract(self::_getFieldData($field, $checked === NULL), EXTR_OVERWRITE);
		
        if($checked || ($checked === NULL && $radioValue == $value)) {
            $checked = 'checked="checked"';
        }
        
		// contador de campos radio
		if(isset(self::$_radios[$field])) {
			self::$_radios[$field]++;
		} else {
			self::$_radios[$field] = 0;
		}
		$id .= self::$_radios[$field];
		
        return "<input id=\"$id\" name=\"$name\" type=\"radio\" value=\"$radioValue\" $attrs $checked/>";
    }
    
 
}
