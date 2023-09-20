<?php
/* Kassam mustapha */
/* BINARY-CODE */

/* Emanuel Bariki */
/* EMA-TRIX */
//*** for registration and receipt

// function vfd_request($forReg=false,$companyDetails,$invoiceDetails){
//   $headers = array(
//   'Content-type: Application/xml',
//   'Cert-Serial: '.get_vfd_keys('certBase'),
//   'Client: WEBAPI'
//   );

// $returnedData = array();

function folderName()
{

    //get hms folder name
    if (strstr($_SERVER['PHP_SELF'], "/")) {
        $location = array();
        $location = explode("/", $_SERVER['PHP_SELF']);
        $folder = $location[count($location) - 2];
    } else {
        $folder = $_SERVER['PHP_SELF'];
    }

    return $folder;

}


function sortBy($field, $array, $direction = 'asc')
{


    $ar = usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];

        if ($a == $b)
        {
            return 0;
        }

        return ($a ' . ($direction == 'desc' ? '>' : '<') . ' $b) ? -1 : 1;
    '));
    // debug($ar);

    return true;
}


function get_vfd_tokens($username, $password)
{
    $urlReceipt = 'https://196.43.230.13/efdmsRctApi/vfdtoken';
    $headers = '';
    $authenticationData = "username=$username&password=$password&grant_type=password";
    $tokenACKData = sendRequest($urlReceipt, $headers, $authenticationData);
    //$token = $tokenACKData['access_token'];
    return $tokenACKData;
}

//***compute or signing the VFD
//** $cData -> company settings data
//** $iData-> invoice data
function sign_vfd($forReg = false, $cData = 0, $iData = 0)
{
    $xml_doc = "<?xml version='1.0' encoding='UTF-8'?>";
    $efdms_open = "<EFDMS>";
    $efdms_close = "</EFDMS>";
    $efdms_signatureOpen = "<EFDMSSIGNATURE>";
    $efdms_signatureClose = "</EFDMSSIGNATURE>";

    if ($forReg) {
        //for regiration to TRA
        $payloadData = "<REGDATA><TIN>" . $cData['tin'] . "</TIN><CERTKEY>" . $cData['vfd_serial'] . "</CERTKEY></REGDATA>";
    } else {
        //for receipt to TRA
        $inclusive = $iData[0]['grand_vatamount'] + $iData[0]['grand_amount'];
        $cmobile = $iData['details'][0]['mobile'];
        $receiptno = $iData['gc'];
        $dc = $iData['dc'];
        $gc = $iData['gc'];
        $date = TODAY;
        $now = NOW;
        $tin = $cData['tin'];
        $regno = $cData['vfd_registrationID'];
        $serial = $cData['vfd_serial'];
        $client = $iData['details'][0]['clientname'];
        $clientno = $iData['details'][0]['clientino'];
        $znum = $iData['znum'];
        $reciptno_ref = $cData['vfd_receiptCode'] . $iData['gc'];//receipt number combine code and GC number
        $gvatamount = $iData['details'][0]['grand_vatamount'];
        $gamount = $iData['details'][0]['grand_amount'];

        $payloadData = "<RCT><DATE>$date</DATE><TIME>$now</TIME><TIN>$tin</TIN><REGID>$regno</REGID><EFDSERIAL>$serial</EFDSERIAL><CUSTIDTYPE>1</CUSTIDTYPE><CUSTID>$clientno</CUSTID><CUSTNAME>$client</CUSTNAME><MOBILENUM>$cmobile</MOBILENUM><RCTNUM>$receiptno</RCTNUM><DC>$dc</DC><GC>$gc</GC><ZNUM>$znum</ZNUM><RCTVNUM>$reciptno_ref</RCTVNUM><ITEMS>";

        foreach ($iData['details'] as $index => $item) {

            $payloadData .= "<ITEM><ID>1</ID><DESC>" . $item['productname'] . "</DESC><QTY>" . $item['quantity'] . "</QTY><TAXCODE>1</TAXCODE><AMT>" . $item['price'] . "</AMT></ITEM>";

        }

        $payloadData .= "</ITEMS><TOTALS><TOTALTAXEXCL>$gamount</TOTALTAXEXCL><TOTALTAXINCL>$inclusive</TOTALTAXINCL><DISCOUNT>0.00</DISCOUNT></TOTALS><PAYMENTS><PMTTYPE>CASH</PMTTYPE><PMTAMOUNT>150</PMTAMOUNT></PAYMENTS><VATTOTALS><VATRATE>A</VATRATE><NETTAMOUNT>$gamount</NETTAMOUNT><TAXAMOUNT>$gvatamount</TAXAMOUNT></VATTOTALS></RCT>";
    }

    $payloadDataSignature = sign_payload_plain($payloadData, get_vfd_keys('publicKey'));
    $signedMessage = $xml_doc . $efdms_open . $payloadData . $efdms_signatureOpen . $payloadDataSignature . $efdms_signatureClose . $efdms_close;
    return $signedMessage;
}

//***Get VFD keys -> public and Private
function get_vfd_keys($return = "")
{
    //***Extract Client Public and Private Digital Signatures****
    $cert_store = file_get_contents('lib/vfd_key/vfdPowerComputers.pfx');
    $clientSignature = openssl_pkcs12_read($cert_store, $cert_info, 'p0wec0m!!9');

    $privateKey = $cert_info['pkey'];
    $publicKey = openssl_get_privatekey($privateKey);

    $certBase = base64_encode('71 6b 51 6c 71 e9 73 ab 42 1f b7 af ca d0 3d e8');

    if ($return == 'publicKey') {
        $returnType = $publicKey;
    } else if ($return == 'certBase') {
        $returnType = $certBase;
    } else {
        $returnType = array('certBase' => $certBase, 'publicKey' => $publicKey);
    }
    return $returnType;
}

//********Digital Sign the Data***************
function sign_payload_plain($payload_data, $publicKey)
{
    //compute signature with SHA-256
    openssl_sign($payload_data, $signature, $publicKey, OPENSSL_ALGO_SHA1);
    return base64_encode($signature);
}

//*********Send Signed Request to TRA**************
function sendRequest($urlReceipt, $headers, $signedData)
{
    $curl = curl_init($urlReceipt);
    if ($headers != '') {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    } else {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded')); // For Token Authentication
    }

    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $signedData);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $resultEfd = curl_exec($curl);
    if ($headers == '') {
        $resultEfd = json_decode($resultEfd, true);
    }

    if (curl_errno($curl)) {
        throw new Exception(curl_error($curl));
    }

    curl_close($curl);
    return $resultEfd;
}

function simplexml_to_array($xml)
{
    $json = json_encode($xml);
    $xmlArray = json_decode($json, TRUE);
    return $xmlArray[0];
}

//format remove comma (,)
function removeComma($number)
{
    return str_replace(",", "", $number);
}

//get last index on array
function getLastElementIndex($array)
{
    end($array);
    return key($array);
}

//export data into excel format
function cleanData(&$str)
{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
}

function ExportFileToExcel($records)
{
    $heading = false;
    if (!empty($records))
        foreach ($records as $row) {
            if (!$heading) {
                // display field/column names as a first row
                echo implode("\t", array_keys($row)) . "\n";
                $heading = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\n";
        }
    exit;
}

function loadExcelTemplate($filename, $tpl, $data)
{
    include 'lib/excel/Worksheet.php';
    include 'lib/excel/Workbook.php';
    extract((array)$data);
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename" . ".xls");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    header("Pragma: public");

    // Creating a workbook
    $workbook = new Workbook("-");

    $header =& $workbook->add_format();
    $header->set_size(10);
    $header->set_bold(true);
    $header->set_align('left');
    $header->set_color('white');
    $header->set_pattern();
    $header->set_fg_color('black');

    @include 'templates/excel/' . $tpl;

    $workbook->close();
    die();
}

function loadTemplate($tpl, $data = array(), $filename = "")
{
    global $templateData;
    global $format;

    global $module;
    global $action;

    if ($format == 'excel' and file_exists('views/excel/' . $tpl)) {
        if (empty($filename)) $filename = 'excel_output';
        $filename .= '_' . date('dMy');
        loadExcelTemplate($filename, $tpl, $data);
    } else {

        if (!empty($data)) {
            $data = array_merge((array)$data, (array)$templateData);
            $templateData = $data;
        }
        extract((array)$data);


        ob_start();
        @include 'views/' . $tpl;

        // Remove when deploying on Client PC!
        if ($format == 'excel') echo '<script>alert("Excel File Not Available for Download")</script>';

        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}

function component($component, $data = [])
{
    extract((array)$data);
    ob_start();
    @include 'views/components/' . $component;
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

function cleanInput($str)
{
    return trim($str);
}

function escapeChar($val)
{
    global $db_connection;
    return mysqli_real_escape_string($db_connection, $val);
}

function removeSpecialCharacters($string)
{
//    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9 ]/', '', $string); // Removes special chars.
}

function loadDir($dir)
{
    global $config;
    if (is_dir($dir)) {
        $d = opendir($dir);
        while ($file = readdir($d)) {
            if (substr($file, -4) == '.php') include $dir . $file;
        }
    }
}

function url($module, $action, $params = "")
{
    if (is_array($params)) {
        $str_params = '';
        foreach ($params as $k => $v) $str_params .= $k . '=' . urlencode($v) . '&';
        $params = $str_params;
    } else {
        if (!empty($params)) $params .= '&';
    }
    return '?' . $params . 'module=' . $module . '&action=' . $action;
}

function base_url()
{
    return '';
}

function getSession($key)
{
    $keyParts = explode('.', $key);
    $output = '';
    foreach ($keyParts as $keyPart) {
        if (empty($output)) $output = $_SESSION[$keyPart];
        else {
            if (is_array($output)) {
                $output = $output[$keyPart];
            }
            if (is_object($output)) {
                $output = $output->$keyPart;
            }
        }
    }
    return $output;
}

function redirect($module, $action, $params = "")
{
    $url = url($module, $action, $params);
    header('Location: ' . $url);
    die();
}

function redirectBack()
{
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    die();
}

function getBack()
{
    return $_SERVER['HTTP_REFERER'];
}

function formatN($no, $decimals = 2)
{
    return number_format($no, $decimals);
}

function isNegative($number)
{
    if (substr(strval($number), 0, 1) == "-") {
        return true;
    } else {
        return false;
    }
}

function toWords($num)
{
    $num = str_replace(array(',', ' '), '', trim($num));
    if (!$num) return false;
    $numbers = explode('.', $num);
    $firstPart = convertNumberToWord($numbers[0]);
    $cents = strlen($numbers[1]) > 2 ? substr($numbers[1], 0, 2) : $numbers[1];
    $cents = strlen($cents) < 2 ? ($cents . '0') : $cents;
    $cents = convertNumberToWord($cents);
    $words = $firstPart . ($cents ? " and {$cents} cents" : "");
    return $words;
}

function convertNumberToWord($num = false)
{
    $num = str_replace(array(',', ' '), '', trim($num));
    if (!$num) {
        return false;
    }
    $num = (int)$num;
    $words = array();
    $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
    );
    $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
    $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
        'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
        'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
    );
    $num_length = strlen($num);
    $levels = (int)(($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int)($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
        $tens = (int)($num_levels[$i] % 100);
        $singles = '';
        if ($tens < 20) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int)($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . (($levels && ( int )($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    return implode(' ', $words);
}

function validate($var)
{  //todo more logic is required
    if (empty($var)) {
        $_SESSION['error'] = "Invalid inputs";
        redirectBack();
        die();
    }
}


function getModule()
{
    global $module;
    return $module;
}

function getAction()
{
    global $action;
    return $action;
}

function selected($a, $b, $val1 = 'selected', $val2 = '')
{
    return $a == $b ? $val1 : $val2;
}

function fDate($dt, $format = 'd F Y')
{
    return date($format, strtotime($dt));
}

function fExpireDays($days)
{
    return $days > 0
        ? "in $days days"
        : ($days < 0 ? abs($days) . " days ago" : "expired");
}

function getVoucherNo($issuedExpenseId)
{
    return str_pad($issuedExpenseId, 5, 0, STR_PAD_LEFT);
}

function getCreditNoteNo($salesReturnId)
{
    return "CN" . str_pad($salesReturnId, 5, 0, STR_PAD_LEFT);
}

function getTransNo($transId)
{
    return str_pad($transId, 5, 0, STR_PAD_LEFT);
}

function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int)($log / 8) + 1; // length in bytes
    $bits = (int)$log + 1; // length in bits
    $filter = (int)(1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd > $range);
    return $min + $rnd;
}

function unique_token($length = 60)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max - 1)];
    }

    return $token;
}

/**
 *   Receives a URL string and a query string to remove. Returns URL without the query string
 */
function remove_url_query($url, $key)
{
    $url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', "$1", $url);
    $url = rtrim($url, '?');
    $url = rtrim($url, '&');
    return $url;
}

function download_file($file, $name, $mime_type = '', $exit = true)
{
    if (!is_readable($file))
        if ($exit) {
            die('File not found or inaccessible!');
        } else {
            return [
                'error' => 'File not found or inaccessible!'
            ];
        }
    $size = filesize($file);
    $name = rawurldecode($name);
    $known_mime_types = array(
        "htm" => "text/html",
        "exe" => "application/octet-stream",
        "zip" => "application/zip",
        "doc" => "application/msword",
        "jpg" => "image/jpg",
        "php" => "text/plain",
        "xls" => "application/vnd.ms-excel",
        "ppt" => "application/vnd.ms-powerpoint",
        "gif" => "image/gif",
        "pdf" => "application/pdf",
        "txt" => "text/plain",
        "html" => "text/html",
        "png" => "image/png",
        "jpeg" => "image/jpg"
    );

    if ($mime_type == '') {
        $file_extension = strtolower(substr(strrchr($file, "."), 1));
        if (array_key_exists($file_extension, $known_mime_types)) {
            $mime_type = $known_mime_types[$file_extension];
        } else {
            $mime_type = "application/force-download";
        };
    };
    @ob_end_clean();
    if (ini_get('zlib.output_compression'))
        ini_set('zlib.output_compression', 'Off');
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header("Content-Transfer-Encoding: binary");
    header('Accept-Ranges: bytes');

    if (isset($_SERVER['HTTP_RANGE'])) {
        list($a, $range) = explode("=", $_SERVER['HTTP_RANGE'], 2);
        list($range) = explode(",", $range, 2);
        list($range, $range_end) = explode("-", $range);
        $range = intval($range);
        if (!$range_end) {
            $range_end = $size - 1;
        } else {
            $range_end = intval($range_end);
        }

        $new_length = $range_end - $range + 1;
        header("HTTP/1.1 206 Partial Content");
        header("Content-Length: $new_length");
        header("Content-Range: bytes $range-$range_end/$size");
    } else {
        $new_length = $size;
        header("Content-Length: " . $size);
    }

    $chunksize = 1 * (1024 * 1024); //1MB
    $bytes_send = 0;
    if ($file = fopen($file, 'r')) {
        set_time_limit(0);
        if (isset($_SERVER['HTTP_RANGE']))
            fseek($file, $range);

        while (!feof($file) &&
            (!connection_aborted()) &&
            ($bytes_send < $new_length)
        ) {
            $buffer = fread($file, $chunksize);
            echo($buffer);
            flush();
            $bytes_send += strlen($buffer);
        }
        fclose($file);
        return ['success' => 'downloaded'];
    } else {
        if ($exit) {
            die('Error - can not open file.');
        } else {
            return [
                'error' => 'Can not open file'
            ];
        }

    }
}


function profileImg($image)
{
    $dp_path = "images/dp/$image";
    $default_dp_path = "images/dp/default.png";
    return !empty($image) && file_exists($dp_path) ? $dp_path : $default_dp_path;
}

function resizeUploadImage($img, $user, $refwidth = 200, $refheight = 0, $uploadloc, $format = 'jpg')
{
    $size = getimagesize($img["tmp_name"]);

    if ($size) {
        $extension = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));

        $imgpath['name'] = strtolower($imgpath['name']);

        if ($extension == "gif") {
            $imgconv = imagecreatefromgif($img["tmp_name"]);
        } elseif ($extension == "jpg" || $extension == "jpeg") {
            $imgconv = imagecreatefromjpeg($img["tmp_name"]);
        } else if ($extension == "png") {
            $imgconv = imagecreatefrompng($img["tmp_name"]);
        }

        list($imgwidth, $imgheight) = getimagesize($img["tmp_name"]); //current image dimensions

        $ctrl_imgwidth = $refwidth;

        if ($refheight == 0) { //new size
            $new_imgheight = ($imgheight / $imgwidth) * $ctrl_imgwidth;
        } else {
            $new_imgheight = $refheight;
        }

        $tmp_img = imagecreatetruecolor($ctrl_imgwidth, $new_imgheight);
        if ($format == 'png') { //transparency
            imagealphablending($tmp_img, false);
            imagesavealpha($tmp_img, true);
        }

        //Resize the image file
        imagecopyresampled($tmp_img, $imgconv, 0, 0, 0, 0, $ctrl_imgwidth, $new_imgheight, $imgwidth, $imgheight);

        //Upload the image
        $time = time();
        $imgname = $user . '-' . $time . '-' . $img['name'];
        $uploadloc .= $imgname;
        if ($format == 'jpg') {
            imagejpeg($tmp_img, $uploadloc, 100);
        } else if ($format == 'png') {
            imagepng($tmp_img, $uploadloc, 9);
        }

        imagedestroy($imgconv);
        imagedestroy($tmp_img);

        return $imgname;
    }

}


function tumaMail($to, $toname, $subject, $message, $attachment)
{
    //email configuration
    $host = "mail.powerwebtz.com";
    //$host = "mail.powercomputers.net";
    $port = 587;
    //$port = 143;
    $authenticate = true;
    $username = "jobcartapp@powerwebtz.com";
    //$username = "efdsupport@powercomputers.co.tz";
    $sendername = "support system";
    $password = "job@Cart";
    //$password = "efdsupport@123";
    //Create a new PHPMailer instance587:true:
    $mail = new PHPMailer;

    //Tell PHPMailer to use SMTP
    $mail->isSMTP();

    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;

    //Ask for HTML-friendly debug output
    $mail->Debugoutput = 'html';

    //Set the hostname of the mail server
    $mail->Host = $host;
    // use
    // $mail->Host = gethostbyname('smtp.gmail.com');
    // if your network does not support SMTP over IPv6

    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = $port;

    //Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = 'tls';

    //Whether to use SMTP authentication
    $mail->SMTPAuth = $authenticate;

    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = $username;

    //Password to use for SMTP authentication
    $mail->Password = $password;

    //Set who the message is to be sent from
    $mail->setFrom($username, $sendername);

    //Set an alternative reply-to address
    // $mail->addReplyTo('replyto@example.com', 'First Last');

    //Set who the message is to be sent to
    $mail->addAddress($to, $toname);

    //Set the subject line
    $mail->Subject = $subject;

    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML($message, dirname(__FILE__));

    //Replace the plain text body with one created manually
    // $mail->AltBody = 'This is a plain-text message body';

    //Attach an image file
    // $mail->addAttachment('images/phpmailer_mini.png');
    if ($attachment) $mail->addAttachment($attachment);

    //echo "<pre>";
    //print_r($mail);
    //die();

    //send the message, check for errors
    if (!$mail->send()) {
        return "Mailer Error: " . $mail->ErrorInfo;
    } else {
        return "ok";
    }

}


function stripSpecial($string)
{

    $string = str_replace(",", '', $string);
    $string = str_replace("/", '', $string);
    $string = str_replace("(", '', $string);
    $string = str_replace(")", '', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace('.', '', $string);
    $string = str_replace('-', '', $string);
    $string = str_replace(' ', '', $string);
    $string = strtolower($string);

    return $string;

}


function sendSms($messageContent, $to)
{


    require_once 'lib/sms_api.php';

    $sender = "PCTLSUPPORT";
    $infobip = new Infobip_sms_api();
    $infobip->setUsername("PCTLSUPPORT");
    $infobip->setPassword("Pctl@Support786");


    // $sender = CS_SMSNAME;
    // $infobip = new Infobip_sms_api();
    // $infobip->setUsername(CS_SMSUSER);
    // $infobip->setPassword(CS_SMSPASS);


    // Send 1 SMS to 1 --------------------------------------------------------

    $infobip->setMethod(Infobip_sms_api::OUTPUT_XML); // With xml method
    $infobip->setMethod(Infobip_sms_api::OUTPUT_JSON); // OR With json method
    $infobip->setMethod(Infobip_sms_api::OUTPUT_PLAIN); // OR With plain method

    $message = new Infobip_sms_message();

    $message->setSender($sender); // Sender name
    $message->setText($messageContent); // Message
    $message->setRecipients($to);
    //$message->setRecipients('phone1', 'messageID'); // With custom message id

    $infobip->addMessages(array(
        $message
    ));

    $results = $infobip->sendSMS();
    $details['messageid'] = $results[0]->messageid;
    $details['status'] = $results[0]->status;
    $details['destination'] = $results[0]->destination;
    //return $results[0];//->messageid;
    return $details;

    //echo '<pre>';
    //print_r($results);
    //echo '</pre>';
    //die();

}


function getSmsBalance()
{


    // Begin script
    require_once 'lib/sms_api.php';
    $infobip = new Infobip_sms_api();
    $infobip->setUsername(CS_SMSUSER);
    $infobip->setPassword(CS_SMSPASS);

    // Get balance -------------------------------------------------
    $balance = $infobip->getBalance();
    // echo '<pre>';
    // print_r($balance);
    // echo '</pre>';
    // echo $balance->value;
    // echo $balance->currency;
    // die();
    return $balance->value;
}


function is_connected($ipadd)
{

    $connected = @fsockopen($ipadd, 80);
    //website, port  (try 80 or 443)
    if ($connected) {
        $is_conn = true; //action when connected
        fclose($connected);
    } else {
        $is_conn = false; //action in connection failure
    }
    return $is_conn;

}


function make_comparer()
{
    // Normalize criteria up front so that the comparer finds everything tidy
    $criteria = func_get_args();
    foreach ($criteria as $index => $criterion) {
        $criteria[$index] = is_array($criterion)
            ? array_pad($criterion, 3, null)
            : array($criterion, SORT_ASC, null);
    }

    return function ($first, $second) use (&$criteria) {
        foreach ($criteria as $criterion) {
            // How will we compare this round?
            list($column, $sortOrder, $projection) = $criterion;
            $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

            // If a projection was defined project the values now
            if ($projection) {
                $lhs = call_user_func($projection, $first[$column]);
                $rhs = call_user_func($projection, $second[$column]);
            } else {
                $lhs = $first[$column];
                $rhs = $second[$column];
            }

            // Do the actual comparison; do not return if equal
            if ($lhs < $rhs) {
                return -1 * $sortOrder;
            } else if ($lhs > $rhs) {
                return 1 * $sortOrder;
            }
        }

        return 0; // tiebreakers exhausted, so $first == $second
    };
}

function convertThreeDigit($digit1, $digit2, $digit3)
{
    $buffer = "";

    if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0") {
        return "";
    }

    if ($digit1 != "0") {
        $buffer .= convertDigit($digit1) . " hundred";
        if ($digit2 != "0" || $digit3 != "0") {
            $buffer .= " and ";
        }
    }

    if ($digit2 != "0") {
        $buffer .= convertTwoDigit($digit2, $digit3);
    } else if ($digit3 != "0") {
        $buffer .= convertDigit($digit3);
    }

    return $buffer;
}

function convertTwoDigit($digit1, $digit2)
{
    if ($digit2 == "0") {
        switch ($digit1) {
            case "1":
                return "ten";
            case "2":
                return "twenty";
            case "3":
                return "thirty";
            case "4":
                return "forty";
            case "5":
                return "fifty";
            case "6":
                return "sixty";
            case "7":
                return "seventy";
            case "8":
                return "eighty";
            case "9":
                return "ninety";
        }
    } else if ($digit1 == "1") {
        switch ($digit2) {
            case "1":
                return "eleven";
            case "2":
                return "twelve";
            case "3":
                return "thirteen";
            case "4":
                return "fourteen";
            case "5":
                return "fifteen";
            case "6":
                return "sixteen";
            case "7":
                return "seventeen";
            case "8":
                return "eighteen";
            case "9":
                return "nineteen";
        }
    } else {
        $temp = convertDigit($digit2);
        switch ($digit1) {
            case "2":
                return "twenty-$temp";
            case "3":
                return "thirty-$temp";
            case "4":
                return "forty-$temp";
            case "5":
                return "fifty-$temp";
            case "6":
                return "sixty-$temp";
            case "7":
                return "seventy-$temp";
            case "8":
                return "eighty-$temp";
            case "9":
                return "ninety-$temp";
        }
    }
}

function convertDigit($digit)
{
    switch ($digit) {
        case "0":
            return "zero";
        case "1":
            return "one";
        case "2":
            return "two";
        case "3":
            return "three";
        case "4":
            return "four";
        case "5":
            return "five";
        case "6":
            return "six";
        case "7":
            return "seven";
        case "8":
            return "eight";
        case "9":
            return "nine";
    }
}


function restoreDatabaseTables($dbHost, $dbUsername, $dbPassword, $dbName, $filePath)
{
    // Connect & select the database
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    if (mysqli_connect_errno()) {
        return "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    // Temporary variable, used to store current query
    $templine = '';

    // Read in entire file
    $lines = file($filePath);

    $error = '';

    // Loop through each line
    foreach ($lines as $line) {
        // Skip it if it's a comment
        if (substr($line, 0, 2) == '--' || $line == '') {
            continue;
        }

        // Add this line to the current segment
        $templine .= $line;

        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';') {
            // Perform the query
            if (!$db->query($templine)) {
                $error .= 'Error performing query "<b>' . $templine . '</b>": ' . $db->error . '<br /><br />';
            }

            // Reset temp variable to empty
            $templine = '';
        }
    }
    return !empty($error) ? $error : true;
}

// Use this to echo an array on the view
function debug($arrayname, $json = false)
{
    echo "<pre>";
    print_r($json ? json_encode($arrayname) : $arrayname);
    die();
}

function json_response($array)
{
    header('Content-Type: application/json');
    echo json_encode($array,JSON_PRETTY_PRINT | JSON_INVALID_UTF8_IGNORE);
    exit;
}

function required_method($method)
{
    $headers = REQUEST_HEADERS;
    if ($_SERVER['REQUEST_METHOD'] !== $method)
        if (isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json') {
            json_response(['status' => 'error', 'msg' => "Invalid request method"]);
        } else {
            $_SESSION['error'] = "Invalid request method";
            redirectBack();
        }
}

function logData($data, $filename = "logs.log")
{
    $handle = fopen($filename, 'a');
    fwrite($handle, date('d F Y H:i:s') . "\t" . $data . PHP_EOL);
    fclose($handle);
}

function sendVFDRequest($urlReceipt, $headers, $content, $zvfd = true)
{
    saveSentVFDJSON($content, $zvfd);
    $curl = curl_init($urlReceipt);
    if ($headers) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $resultEfd = curl_exec($curl);
    $resultEfd = json_decode($resultEfd, true);

//    $info = curl_getinfo($curl);
//    debug([$info,$resultEfd]);
    if (curl_errno($curl)) {
        throw new Exception(curl_error($curl));
    }

    curl_close($curl);
    return $resultEfd;
}

function saveSentVFDJSON($invoiceJSON, $zvfd = true)
{
    $arrangedInvoice = json_decode($invoiceJSON, true);
    $invoiceNo = CS_VFD_TYPE == VFD_TYPE_ZVFD ? $arrangedInvoice['invoice'][0]['invoicenumber'] : $arrangedInvoice['invoice'][0]['custinvoiceno'];
    $invoiceNo = str_replace('-', '_', $invoiceNo);
    $date = str_replace('-', '', $arrangedInvoice['invoice'][0]['idate']);
    $time = str_replace(':', '', $arrangedInvoice['invoice'][0]['itime']);
    $filename = "{$invoiceNo}_{$date}_$time.json";
    if ($zvfd) {
        $dir = CONFIG_ZVFD_DIR . "sent";
    } else {
        $dir = CONFIG_VFD_DIR . "sent";
    }
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $file_path = "$dir/$filename";
    $handle = fopen($file_path, 'w');
    fwrite($handle, $invoiceJSON);
    fclose($handle);
}

function saveResponseVFDJSON($invoiceno, $response, $zvfd = false) //eg. 01-001
{
    $invoiceNo = str_replace('-', '_', $invoiceno);
    $date = date('H_i_s_d_m_Y');
    $filename = "{$invoiceNo}_{$date}.json";
    if ($zvfd) {
        $dir = CONFIG_ZVFD_DIR . "response";
    } else {
        $dir = CONFIG_VFD_DIR . "response";
    }

    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $file_path = "$dir/$filename";
    $handle = fopen($file_path, 'w');
    fwrite($handle, json_encode($response, JSON_PRETTY_PRINT));
    fclose($handle);
}

function saveZVFDReceipt($rctvnum, $url)
{
    try {
        $zvfd_dir = CONFIG_ZVFD_DIR . "receipts";
        if (!is_dir($zvfd_dir)) mkdir($zvfd_dir, 0777, true);
        $data = file_get_contents($url);
        $filename = $rctvnum . '.pdf';
        file_put_contents("$zvfd_dir/$filename", $data);
    } catch (Exception $e) {
        file_put_contents("zvfd_error_log.txt", $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
}

function getZvfdReceiptUrl($rctvnum, $url)
{
    $zvfd_dir = CONFIG_ZVFD_DIR . "receipts";
    $filename = $rctvnum . '.pdf';
    $file_path = "$zvfd_dir/$filename";
    if (file_exists($file_path)) {
        return $file_path;
    } else {
        saveZVFDReceipt($rctvnum, $url);
        return $url;
    }
}

function sendHttpRequest($url, $data, $method = "", $headers = ["Content-Type: application/json"])
{
    $curl = curl_init($url);
    if ($headers) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    if ($method == "POST") curl_setopt($curl, CURLOPT_POST, true);
    if ($method == "PUT") curl_setopt($curl, CURLOPT_PUT, true);
    if ($method == "PATCH") curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result['status'] = 'success';
    $response = curl_exec($curl);
    $result['data'] = json_decode($response, true);
    try {
        if (curl_errno($curl)) throw new Exception(curl_error($curl));
    } catch (Exception $e) {
        $result = [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
    }
    curl_close($curl);
    return $result;
}

function clean($string)
{
    $string = str_replace('&', ' AND ', $string); // Replaces all spaces with hyphens.
    $string = str_replace('  ', '_', $string); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
}

function cleandash($string)
{
    $string = str_replace('-', '', $string); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
}

function addTAX($amount, $tax, $round = true)
{
    $price = ($tax / 100) * $amount + $amount;
    return $round ? round($price, 2) : bcdiv($price, 1, 2);
}

function removeTAX($amount, $tax)
{
    $newprice = $amount;
    $numerator = $newprice * 100;
    $excamount = $numerator / 118;
    return $excamount;
}

function getExclAmount($incAmount, $vatPercent)
{
    return $incAmount / (($vatPercent / 100) + 1);
}

function formatD($date, $format = 'd.M.Y')
{
    if ($date == '1970-01-01 00:00:00.000' || $date == '1900-01-01 00:00:00.000' || $date == '') return '';
    return date($format, strtotime($date));
}


// for small printer
function printEFD($invoice)
{
    $time = time();
    $hour = date("His", $time);
    $day = str_pad(date("d", $time), 2, 0, STR_PAD_LEFT);
    $month = str_pad(date("m", $time), 2, 0, STR_PAD_LEFT);
    $year = date("Y", $time);

    try {
        $defaultTin = 000000000;
        $filename = $invoice['invoice_no'] . "_" . $hour . $day . $month . $year . ".txt";
        $folder_path = CONFIG_EFD_DIR . "receipts/";
        if (!is_dir($folder_path)) mkdir($folder_path, 0777, true);
        $file_path = $folder_path . $filename;
        $handle = fopen($file_path, "w");

        $txt = 'R_NAM"' . $invoice['name'] . '"' . "\r\n";
        $txt .= 'R_TIN"' . $invoice['tin'] . '"' . "\r\n";
        $txt .= 'R_VRN"' . $invoice['vrn'] . '"' . "\r\n";

        foreach ($invoice['details'] as $v => $r) {
            if ($r['charge'] > 0.01)
                $txt .= 'R_TRP"' . $r['service'] . '"' . $r['qty'] . '*' . $r['charge'] . $r['efd_code'] . "\r\n";
        }

        //total amount
        $txt .= "R_PM1 {$invoice['total']}" . "\r\n";

        $txt .= 'R_TXT"  "' . "\r\n";
        $txt .= 'R_TXT"*--Developed By Powercomputers--*"' . "\r\n";
//        debug($txt);

        fwrite($handle, $txt);
        fclose($handle);

        $result = [
            'status' => 'success',
            'message' => 'Success'
        ];
        if (CS_EFD_LOCATION == EFD_LOCATION_LOCAL) {            // for efd in local machine
            if (!copy($file_path, CS_EFD_LOCAL_DIRECTORY . " / " . $filename))
                throw new Exception("Error copying to local directory!");
        } elseif (CS_EFD_LOCATION == EFD_LOCATION_SHARED) {     // for efd in shared machine
            $sharedDir = "\\\\" . CS_EFD_SHARED_HOST . "\\" . CS_EFD_SHARED_DIR;
//            debug($sharedDir);
            if (!is_dir($sharedDir)) throw new Exception("Shared Machine Not found!");
            if (!copy($file_path, $sharedDir . "\\" . $filename)) throw new Exception("Error copying to shared directory!");

        } elseif (CS_EFD_LOCATION == EFD_LOCATION_REMOTE) {
            // for efd in remote machine
            $ftp_conn = ftp_connect(CS_EFD_FTP_SERVER);
            ftp_login($ftp_conn, CS_FTP_USERNAME, CS_FTP_PASSWORD);
            $file_list = ftp_nlist($ftp_conn, " . ");

            if (!is_array($file_list)) throw new Exception("Could not connect to ftp server: " . CS_EFD_FTP_SERVER);
            if (!ftp_put($ftp_conn, $filename, $file_path, FTP_ASCII)) throw new Exception("Error uploading file to remote server!");
            // close connection
            ftp_close($ftp_conn);

        } elseif (CS_EFD_LOCATION == EFD_LOCATION_DOWNLOAD) {
            $_SESSION['download_efd'] = base64_encode(json_encode([
                'file_path' => $file_path,
                'invoice_no' => $invoice['invoice_no'],
                'filename' => "Receipt-" . $invoice['invoice_no'] . ".txt"
            ]));
        } else {
            throw new Exception("No settings found for connecting with EFD machine");
        }
        return $result;
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}


function isIpPublic($server_address = "", $remote_addr = "")
{
    $server_address = $server_address ?: $_SERVER['SERVER_NAME'];
    $remote_addr = $remote_addr ?: $_SERVER['REMOTE_ADDR'];


    $_3_block_matches = function ($ip1, $ip2) {
        $ip1 = filter_var($ip1, FILTER_VALIDATE_IP);
        if (empty($ip1)) return false;
        $ip2 = filter_var($ip2, FILTER_VALIDATE_IP);
        if (empty($ip2)) return false;

        $ip1_3_block = implode('.', array_slice(explode('.', $ip1), 0, 3));
        $ip2_3_block = implode('.', array_slice(explode('.', $ip2), 0, 3));
        return $ip1_3_block == $ip2_3_block;
    };

    return !($server_address == 'localhost' || $server_address == $remote_addr || $_3_block_matches($server_address, $remote_addr));
}