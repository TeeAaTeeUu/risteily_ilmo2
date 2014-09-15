<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL | E_STRICT);
//ini_set('error_log', 'script_errors.log');
//ini_set('log_errors', 'On');

//sendMail("tatu.tallberg@gmail.com", "tatu@limes.fi", null, null, "Tämä on otsikko", '<p>viesti sinulle, suuri johtaja.</p>');

function sendMail($to, $from, $cc, $bcc, $subject, $html) {

    $kaikki = array("to", "from", "cc", "bcc", "subject", "html");
    foreach ($kaikki as $x)
        $$x = str_replace('\\', "", html_entity_decode($$x, ENT_QUOTES, "UTF-8"));

    $subject = mime_header(str_replace(" ", "_", $subject));

    $from = post_mime_header_explode($from);
    $to = post_mime_header_explode($to);
    $cc = post_mime_header_explode($cc);
    $bcc = post_mime_header_explode($bcc);

    $headers = 'From: ' . mime_header_many($from) . "\r\n";
//            'Reply-To: ' . mime_header_many($from) . "\r\n";

    if (!empty($cc))
        $headers .= 'Cc: ' . mime_header_many($cc) . "\r\n";

    if (!empty($bcc))
        $headers .= 'Bcc: ' . mime_header_many($bcc) . "\r\n";

    if (!empty($html)) {
        $random_hash = md5(date('r', time()));
        $headers .= 'Content-Type: multipart/alternative; boundary=' . $random_hash;

        $message = "--" . $random_hash . "\r\n" .
                'Content-Type: text/plain; charset=UTF-8' . "\r\n" .
                'Content-Transfer-Encoding: quoted-printable' . "\r\n" .
                "\r\n" .
                wordwrap(mime_header(trim(str_replace("\t", "", strip_tags(str_replace('\\', "", $html))))), 76, "\r\n") . "\r\n" .
                "\r\n" .
                "--" . $random_hash . "\r\n" .
                'Content-Type: text/html; charset=UTF-8' . "\r\n" .
                'Content-Transfer-Encoding: quoted-printable' . "\r\n" .
                "\r\n" .
                mime_header($html) . "\r\n" .
                "\r\n" .
                "--" . $random_hash . "--" . "\r\n";
    }

    for ($i = 0; $i <= count($to) - 1; $i++) {
        mail_utf8(str_replace('\\', "", mime_header_to($to[$i])), str_replace('\\', "", $subject), str_replace('\\', "", $message), str_replace('\\', "", $headers));
    }
}

function mail_utf8($to, $subject = '', $message = '', $header = '') {
    $header_ = 'MIME-Version: 1.0' . "\r\n";
    mail($to, wordwrap("=?UTF-8?Q?" . $subject . "?=", 75, "?=\r\n\t=?UTF-8?Q?"), $message, $header_ . $header);
}

function spamcheck($field) {
    $field = filter_var($field, FILTER_SANITIZE_EMAIL);
    if (filter_var($field, FILTER_VALIDATE_EMAIL))
        return true;
    else
        return false;
}

function mime_header_many($array) {
    $return = "";

    if (!empty($array[0][0])) {
        for ($j = 0; $j <= count($array) - 1; $j++) {
            $return2 = "";
            for ($i = 0; $i <= count($array[$j]) - 1; $i++) {
                if ($i == count($array[$j]) - 1) {
                    if (count($array[$j]) == 1)
                        $return2 .= "<" . $array[$j][$i] . ">";
                    else
                        $return2 = str_replace(" ", "_", "=?UTF-8?Q?" . imap_8bit($return2) . "?=") . " <" . $array[$j][$i] . ">";
                    if ($j < count($array) - 1)
                        $return2 .= ", ";
                }
                else
                    $return2 .= $array[$j][$i] . " ";
            };
            $return .= $return2;
        };
    };
    return $return;
}

function unmime_header_many($array) {
    $return = "";

    if (!empty($array[0][0])) {
        for ($j = 0; $j <= count($array) - 1; $j++) {
            $return2 = "";
            for ($i = 0; $i <= count($array[$j]) - 1; $i++) {
                if ($i == count($array[$j]) - 1) {
                    $return2 .= "<" . $array[$j][$i] . ">";
                    if ($j < count($array) - 1)
                        $return2 .= ", ";
                }
                else
                    $return2 .= $array[$j][$i] . " ";
            };
            $return .= $return2;
        };
    };
    return $return;
}

function mime_header_to($array) {
    $return = "";

    for ($i = 0; $i <= count($array) - 1; $i++) {
        if ($i == count($array) - 1) {
            if (count($array) == 1)
                $return = "<" . $array[$i] . ">";
            else
                $return = "=?UTF-8?B?" . base64_encode(html_entity_decode($return, ENT_QUOTES, "UTF-8")) . "?=" . " <" . $array[$i] . ">";
        }
        else
            $return .= $array[$i] . " ";
    };
    return $return;
}

function mime_header($array) {
    return quoted_printable_encode2($array);
}

function post_mime_header_explode($array) {
    $array = str_replace(array("<", ">"), "", explode(", ", $array));

    for ($i = 0; $i <= count($array) - 1; $i++)
        $array[$i] = explode(" ", $array[$i]);

    return $array;
}

//..deed.ztinmehc-ut.zrh@umuumu@hrz.tu-chemnitz.deed...
//
//I use the following function instead of imap_8bit
//when using PHP without the IMAP module,
//which is based on code found in
//http://www.php.net/quoted_printable_decode,
//and giving (supposedly) exactly the same results as imap_8bit,
//(tested on thousands of random strings containing lots
//of spaces, tabs, crlf, lfcr, lf, cr and so on,
//no counterexample found SO FAR:)
//
//AND you can force a trailing space to be encoded,
//as opposed to what imap_8bit does,
//which I consider is a violation of RFC2045,
//(see http://bugs.php.net/bug.php?id=35290)
//by commenting that one central line.

function quoted_printable_encode2($sText,$bEmulate_imap_8bit=true) {
  // split text into lines
  $aLines=explode(chr(13).chr(10),$sText);

  for ($i=0;$i<count($aLines);$i++) {
    $sLine =& $aLines[$i];
    if (strlen($sLine)===0) continue; // do nothing, if empty

    $sRegExp = '/[^\x09\x20\x21-\x3C\x3E-\x7E]/e';

    // imap_8bit encodes x09 everywhere, not only at lineends,
    // for EBCDIC safeness encode !"#$@[\]^`{|}~,
    // for complete safeness encode every character :)
    if ($bEmulate_imap_8bit)
      $sRegExp = '/[^\x20\x21-\x3C\x3E-\x7E]/e';

    $sReplmt = 'sprintf( "=%02X", ord ( "$0" ) ) ;';
    $sLine = preg_replace( $sRegExp, $sReplmt, $sLine ); 

    // encode x09,x20 at lineends
    {
      $iLength = strlen($sLine);
      $iLastChar = ord($sLine{$iLength-1});

      //              !!!!!!!!   
      // imap_8_bit does not encode x20 at the very end of a text,
      // here is, where I don't agree with imap_8_bit,
      // please correct me, if I'm wrong,
      // or comment next line for RFC2045 conformance, if you like
      if (!($bEmulate_imap_8bit && ($i==count($aLines)-1)))
         
      if (($iLastChar==0x09)||($iLastChar==0x20)) {
        $sLine{$iLength-1}='=';
        $sLine .= ($iLastChar==0x09)?'09':'20';
      }
    }    // imap_8bit encodes x20 before chr(13), too
    // although IMHO not requested by RFC2045, why not do it safer :)
    // and why not encode any x20 around chr(10) or chr(13)
    if ($bEmulate_imap_8bit) {
      $sLine=str_replace(' =0D','=20=0D',$sLine);
      //$sLine=str_replace(' =0A','=20=0A',$sLine);
      //$sLine=str_replace('=0D ','=0D=20',$sLine);
      //$sLine=str_replace('=0A ','=0A=20',$sLine);
    }

    // finally split into softlines no longer than 76 chars,
    // for even more safeness one could encode x09,x20
    // at the very first character of the line
    // and after soft linebreaks, as well,
    // but this wouldn't be caught by such an easy RegExp                  
    preg_match_all( '/.{1,73}([^=]{0,2})?/', $sLine, $aMatch );
    $sLine = implode( '=' . chr(13).chr(10), $aMatch[0] ); // add soft crlf's
  }

  // join lines into text
  return implode(chr(13).chr(10),$aLines);
}

?>