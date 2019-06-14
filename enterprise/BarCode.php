<?php
class barcode {
    protected static $code39 = array(
        '0' => 'bwbwwwbbbwbbbwbw', '1' => 'bbbwbwwwbwbwbbbw',
        '2' => 'bwbbbwwwbwbwbbbw', '3' => 'bbbwbbbwwwbwbwbw',
        '4' => 'bwbwwwbbbwbwbbbw', '5' => 'bbbwbwwwbbbwbwbw',
        '6' => 'bwbbbwwwbbbwbwbw', '7' => 'bwbwwwbwbbbwbbbw',
        '8' => 'bbbwbwwwbwbbbwbw', '9' => 'bwbbbwwwbwbbbwbw',
        'A' => 'bbbwbwbwwwbwbbbw', 'B' => 'bwbbbwbwwwbwbbbw',
        'C' => 'bbbwbbbwbwwwbwbw', 'D' => 'bwbwbbbwwwbwbbbw',
        'E' => 'bbbwbwbbbwwwbwbw', 'F' => 'bwbbbwbbbwwwbwbw',
        'G' => 'bwbwbwwwbbbwbbbw', 'H' => 'bbbwbwbwwwbbbwbw',
        'I' => 'bwbbbwbwwwbbbwbw', 'J' => 'bwbwbbbwwwbbbwbw',
        'K' => 'bbbwbwbwbwwwbbbw', 'L' => 'bwbbbwbwbwwwbbbw',
        'M' => 'bbbwbbbwbwbwwwbw', 'N' => 'bwbwbbbwbwwwbbbw',
        'O' => 'bbbwbwbbbwbwwwbw', 'P' => 'bwbbbwbbbwbwwwbw',
        'Q' => 'bwbwbwbbbwwwbbbw', 'R' => 'bbbwbwbwbbbwwwbw',
        'S' => 'bwbbbwbwbbbwwwbw', 'T' => 'bwbwbbbwbbbwwwbw',
        'U' => 'bbbwwwbwbwbwbbbw', 'V' => 'bwwwbbbwbwbwbbbw',
        'W' => 'bbbwwwbbbwbwbwbw', 'X' => 'bwwwbwbbbwbwbbbw',
        'Y' => 'bbbwwwbwbbbwbwbw', 'Z' => 'bwwwbbbwbbbwbwbw',
        '-' => 'bwwwbwbwbbbwbbbw', '.' => 'bbbwwwbwbwbbbwbw',
        ' ' => 'bwwwbbbwbwbbbwbw', '*' => 'bwwwbwbbbwbbbwbw',
        '$' => 'bwwwbwwwbwwwbwbw', '/' => 'bwwwbwwwbwbwwwbw',
        '+' => 'bwwwbwbwwwbwwwbw', '%' => 'bwbwwwbwwwbwwwbw'
    );

    public static function code39($text) {
        while(strlen($text)<6)
            $text="0".$text;

        if (!preg_match('/^[A-Z0-9-. $+\/%]+$/i', $text)) {
            throw new Exception('Ошибка ввода');
    }

    $text = '*'.strtoupper($text).'*';
    $length = strlen($text);
    $chars = str_split($text);
    $colors = '';

    foreach ($chars as $char) {
        $colors .= self::$code39[$char];
    }

    $html = '
            <div style=" float:left;">
            <div>';

    foreach (str_split($colors) as $i => $color) {
        if ($color=='b') {
            $html.='<SPAN style="BORDER-LEFT: 0.02in solid; DISPLAY: inline-block; HEIGHT: 0.5in;"></SPAN>';
        } else {
            $html .= '<SPAN style="BORDER-LEFT: white 0.02in solid; DISPLAY: inline-block; HEIGHT: 0.5in;"></SPAN>';
        }
    }

    /*$html.='</div>
        <div style="float:left; width:100%;" align=center >'.$text.'</div></div>';*/
    //  echo htmlspecialchars($html);
    return $html;
    }
}
    if(isset($_GET["idNaryad"])) {
        $NaryadStr=(string)$_GET["idNaryad"];
        while(strlen($NaryadStr)<6)
            $NaryadStr="0".$NaryadStr;
        echo barcode::code39($NaryadStr);
    };
?>