<?php
namespace libs;

/**
 *
 * @author victor
 *        
 */
final class Creator
{  
    /**
     * 
     * @param string $nameSelect - Name of new Select 
     * @param string $cssClass - CSS class of Select 
     * @param array $itemSelect - options available 
     * @return string $selectHtml  - html element
     * @example
     * <br>$itemSelectUpdate = array( 
     *      array( "value" => 1, "class" => "", "leyend" => "1 {#second}" ),<br>
     *      array( "value" => 2, "class" => "", "leyend" => "2 {#seconds}" ),<br>
     *      array( "value" => 5, "class" => "", "leyend" => "5 {#seconds}" ),<br>
     *      array( "value" => 10, "class" => "", "leyend" => "10 {#seconds}" ),<br>
     *      array( "value" => 60, "class" => "", "leyend" => "60 {#seconds}" )<br>
     *  );<br>
     *  $this->data['selectPolling'] = Creator::selectCreator("newSelect", "form-control m-b-sm" , $itemSelectUpdate );
     */
    static function selectCreator( $nameSelect = "" , $cssClass = "" ,$itemSelect = array() ,$multiple = false,$size=1 ) {
        $selectHtml="";
        if (true == $multiple) {
            if ($size > 1) {
                $selectHtml = '<select multiple id="'.$nameSelect .'" name="'.$nameSelect.'" class="'.$cssClass.'" size="24">';
            }else{
                $selectHtml = '<select multiple id="'.$nameSelect .'" name="'.$nameSelect.'" class="'.$cssClass.'">';
            }
        }else{
            if ($size > 1) {
                $selectHtml = '<select id="'.$nameSelect .'" name="'.$nameSelect.'" class="'.$cssClass.'" size="24">';
            }else{
                $selectHtml = '<select id="'.$nameSelect .'" name="'.$nameSelect.'" class="'.$cssClass.'">';
            }
            
        }
        
        if ($itemSelect ==!empty($itemSelect)) {
            foreach ($itemSelect as $option) {
                true == key_exists("value", $option) ? $selectHtml .= '<option value="'.$option['value'].'" ': $selectHtml .= '<option ';
                true == key_exists("selected", $option)? $selectHtml .= ' selected="selected">': $selectHtml .= '>';
                if (true == key_exists("leyend", $option))
                    $selectHtml .= $option['leyend'];
                $selectHtml .= '</option>';
            }
        }        
        $selectHtml .= '</select>';
        return $selectHtml ;
    }
    
    /**
     * 
     * @param string $nameInput
     * @param string $cssClass
     * @param string $value
     * @param string $placeHolder
     * @param string $enabled
     * @param string $requiered
     * @param string $limit
     * @return string
     */
    static function textBoxCreator($nameInput = "", $cssClass = "", $value = '', $placeHolder = "", $enabled = true, $requiered = false, $limit = NULL)
    {
        $textboxHtml = "";
        if (true == $enabled) {
            if (false == $requiered) {
                if ($limit > 0 ) {
                    $textboxHtml = '<input  type="text" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" placeholder="' . $placeHolder . '" value="' . $value . '" maxlength="'.$limit.'"/>';
                }else{
                    $textboxHtml = '<input  type="text" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" placeholder="' . $placeHolder . '" value="' . $value . '"/>';
                }
            } else {
                if ($limit > 0 ) {
                    $textboxHtml = '<input  type="text" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" placeholder="' . $placeHolder . '" value="' . $value . '" required="required" maxlength="'.$limit.'"/>';
                }else{
                    $textboxHtml = '<input  type="text" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" placeholder="' . $placeHolder . '" value="' . $value . '" required="required"/>';
                }
            }
        } else {
            if (false == $requiered) {
                if ($limit > 0 ) {
                    $textboxHtml = '<input disabled="disabled" type="text" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" placeholder="' . $placeHolder . '" value="' . $value . '" maxlength="'.$limit.'"/>';
                }else{
                    $textboxHtml = '<input disabled="disabled" type="text" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" placeholder="' . $placeHolder . '" value="' . $value . '"/>';
                }
            } else {
                if ($limit > 0 ) {
                    $textboxHtml = '<input disabled="disabled" type="text" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" placeholder="' . $placeHolder . '" value="' . $value . '" required="required" maxlength="'.$limit.'"/>';
                }else{
                    $textboxHtml = '<input disabled="disabled" type="text" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" placeholder="' . $placeHolder . '" value="' . $value . '" required="required"/>';
                }
            }
        }
        return $textboxHtml;
    }
    
    /**
     * 
     * @param  string $type - type of button
     * @param  string $nameInput - name and id of html element
     * @param  string $cssClass - css class
     * @param  string $value - leyend of button
     * @param  boolean $enabled - if button will be enabled or disabled 
     * @return string a button html
     */
    static function buttonCreator($type = "button", $nameInput = "", $cssClass = "", $value = '', $enabled = true)
    {
        $textboxHtml = "";
        if (true == $enabled) {
            $textboxHtml = '<input  type="button" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" value="' . $value . '"/>';
        } else {
            $textboxHtml = '<input disabled="disabled" type="button" class="' . $cssClass . '" name="' . $nameInput . '" id="' . $nameInput . '" value="' . $value . '"/>';
        }
        return $textboxHtml;
    }
    
    static function divCreator($name,$content,$attrib=array()){
        $divHtml ="<div id='$name' name='$name'";
        foreach ($attrib as $key => $value) {
            $divHtml .=" $key='$value'";
        }
        $divHtml .=">";
        $divHtml .=$content;
        $divHtml .="</div>";
        return $divHtml;  
    }
    
    static function textAreaCreator($name = "",$class="", $row=3,$col=3,$value=""){
        $textArea= "<textarea class='$class' rows='$row'cols='$col'>";
        $textArea.= "$value</textarea>";
        return $textArea;
    }
    
    static function linkCreator($value, $href = "",$idLink="",$cssClass="",$extProperties=array())
    {
        $linkHtml = "";
        $linkHtml .= "<a ";
        if (true == ! empty($href)) {
            $linkHtml .= ' href="' . $href . '"';
        }
        if (true == ! empty($cssClass)) {
            $linkHtml .= ' class="' . $cssClass . '"';
        }
        foreach ($extProperties as $key => $val) {
            $linkHtml .= ' '.$key.'="' . $val . '"';
        }
        $linkHtml .= ">";
        if (true == ! empty($value)) {
            $linkHtml .= $value;
        }
        $linkHtml .= "</a>";
        return $linkHtml;
    }
    
    /**
     * 
     * @param Array $items
     * @return string
     * @example
     * <code>
     * $array = getListMeterComplete();<br>
     * $i=0;<br>
     *  foreach ($array as $row) { <br>
            $array[$i]["tr_class"] ="odd";<br>
            $array[$i]["tr_rol"] ="row";<br>
            $array[$i]["td_class"] ="";<br>
            $i++;<br>
        }<br>
        $this->data['bodyTable'] = Creator::createBodyTable($array);<br>
        </code>
     */
    static function createBodyTable($items = array()) {
        $bodyTableHtml = "" ;
        if (true ==!empty($items)) {
            foreach ($items as $row) {
                $bodyTableHtml.="<tr ";
                if (true == key_exists("tr_class", $row)){
                    $bodyTableHtml.=" class='".$row['tr_class']."' ";
                }
                if (true == key_exists("tr_rol", $row)){
                    $bodyTableHtml.=" rol='".$row['tr_rol']."' ";
                }
                $bodyTableHtml.=">";
                foreach ($row as $key=>$value) {
                    if (true == key_exists("td_class", $row)) {
                        $class = $row['td_class'];
                    }
                    if ($key!="tr_class" && $key!="tr_rol") {
                        $bodyTableHtml.="";
                        if ($key != "td_class"){
                            if (true ==!empty($class)) {
                                $bodyTableHtml.='<td class="'.$class.'"';
                            }
                            else{
                                $bodyTableHtml.='<td ';
                            }
                        }
                        if ($key != "td_class"){
                            $bodyTableHtml.='>';
                        }
                        if ($key != "td_class"){
                            $bodyTableHtml.=$value;
                            $bodyTableHtml.="</td>";
                        }
                    }
                    
                }
                $bodyTableHtml.="</tr>";
            }
        }
        return $bodyTableHtml;
    }
}

?>