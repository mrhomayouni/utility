<?php

class utility
{
    public static function query_run($sql)
    {
        // first connect to database in db class
        $stmt = db::$conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public static function get_real_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    // This function may have problems
    public static function protocol()
    {
        $is_ssl = in_array(isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : '', ['on', 1]) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : '') == 'https';
        return $is_ssl ? 'https://' : 'http://';
    }

    // for get the agent use this code:

    /*
     * "agent" => $_SERVER["HTTP_USER_AGENT"]
     */

    public static function captcha()
    {
        header('Content-type: image/jpeg');
        $time = time();
        $image = imagecreate(200, 100);
        $color_line = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        $color_pixle = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        $color_background = imagecolorallocate($image, 0, 0, 0);
        $text_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, 200, 100, $color_background);

        for ($i = 1; $i <= 10; $i++) {
            imageline($image, rand(0, 100), rand(0, 100), 200, rand(0, 100), $color_line);
        }
        for ($i = 1; $i <= 1000; $i++) {
            imagesetpixel($image, rand(0, 200), rand(0, 100), $color_pixle);
        }

        $letters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $len = strlen($letters);
        $word = "";
        $font = "arial.ttf";
        for ($i = 1; $i < 5; $i++) {
            $letter = $letters[rand(0, $len - 1)];
            imagettftext($image, 20, rand(20, 60), 25 + ($i * 30), 70, $text_color, $font, $letter);
            $word = $word . $letter;
            $_SESSION["captcha"] = $word;
        }
        imagepng($image);

    }

    /*
     * src if address of captcha file
     *
     * <div style="text-align: center;">
                <img src="captcha" alt="">
            </div>
     */

    // router function

    public static function uri($reservedUrl, $class, $method, $requestMethod = 'GET')
    {

        //current url array
        $currentUrl = explode('?', currentUrl())[0];
        $currentUrl = str_replace(CURRENT_DOMAIN, '', $currentUrl);
        $currentUrl = trim($currentUrl, '/');
        $currentUrlArray = explode('/', $currentUrl);
        $currentUrlArray = array_filter($currentUrlArray);

        //reserved Url array
        $reservedUrl = trim($reservedUrl, '/');
        $reservedUrlArray = explode('/', $reservedUrl);
        $reservedUrlArray = array_filter($reservedUrlArray);


        if (sizeof($currentUrlArray) != sizeof($reservedUrlArray) || methodField() != $requestMethod) {
            return false;
        }

        $parameters = [];
        for ($key = 0; $key < sizeof($currentUrlArray); $key++) {
            if ($reservedUrlArray[$key][0] == "{" && $reservedUrlArray[$key][strlen($reservedUrlArray[$key]) - 1] == "}") {
                array_push($parameters, $currentUrlArray[$key]);
            } elseif ($currentUrlArray[$key] !== $reservedUrlArray[$key]) {
                return false;
            }
        }

        if (methodField() == 'POST') {
            $request = isset($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
            $parameters = array_merge([$request], $parameters);
        }

        $object = new $class;
        call_user_func_array(array($object, $method), $parameters);
        exit();
    }

}