<?php

class Base64Converter {

    protected static $baseAlfa = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    const EMPTY_DATA_ERROR = 1;
    const INVALID_BASE64_ERROR = 2;

    /**
     * @param string $filePath
     * @return string
     *
     * @throws Exception
     */
    public function convertFile(string $filePath) : string
    {
        return $this->encode(file_get_contents($filePath));
    }

    /**
     * @param string $input
     * @return string
     *
     * @throws Exception
     */
    public function decode(string $input) : string
    {
        if($input == null)
        {
            throw new Exception("Data is empty", self::EMPTY_DATA_ERROR);
        }
        if (mb_strlen($input) % 4 != 0)
        {
            throw new Exception("Invalid base64 string", INVALID_BASE64_ERROR);
        }
        $decoded[] = ((mb_strlen($input) * 3) / 4) - (strrpos($input,'=') > 0 ? (mb_strlen($input) - strrpos($input,'=')) : 0);
        $inChars = str_split($input);
        $j = 0;
        $b = array();
        for ($i = 0; $i < count($inChars); $i += 4) {
            $b[0] = strpos(self::$baseAlfa, $inChars[$i]);
            $b[1] = strpos(self::$baseAlfa, $inChars[$i + 1]);
            $b[2] = strpos(self::$baseAlfa, $inChars[$i + 2]);
            $b[3] = strpos(self::$baseAlfa, $inChars[$i + 3]);
            $decoded[$j++] = (($b[0] << 2) | ($b[1] >> 4));
            if ($b[2] < 64)      {
                $decoded[$j++] = (($b[1] << 4) | ($b[2] >> 2));
                if ($b[3] < 64)  {
                    $decoded[$j++] = (($b[2] << 6) | $b[3]);
                }
            }
        }
        $decodedStr = '';
        for($i=0;$i<count($decoded);$i++)
        {
            $decodedStr .= chr($decoded[$i]);

        }
        return $decodedStr;
    }

    /**
     * @param string $data
     * @return string
     *
     * @throws Exception
     */
    public function encode(string $data) : string
    {
        if($data == null || mb_strlen($data) === 0)
        {
            throw new Exception("Data is empty", self::EMPTY_DATA_ERROR);
        }
        $dataLength = mb_strlen($data);
        $data = str_split($data);
        $out = '';
        $letter = '';
        for ($i = 0; $i < $dataLength; $i += 3)  {
            $letter = (ord($data[$i]) & 0xFC) >> 2;
            $out .= (self::$baseAlfa[$letter]);
            $letter = (ord($data[$i]) & 0x03) << 4;
            if ($i + 1 < $dataLength) {

                $letter |= (ord($data[$i + 1]) & 0xF0) >> 4;
                $out .= (self::$baseAlfa[$letter]);
                $letter = (ord($data[$i + 1]) & 0x0F) << 2;

                if ($i + 2 < $dataLength)  {
                    $letter |= (ord($data[$i + 2]) & 0xC0) >> 6;
                    $out .= (self::$baseAlfa[$letter]);
                    $letter = ord($data[$i + 2]) & 0x3F;
                    $out .= (self::$baseAlfa[$letter]);
                } else  {
                    $out .= (self::$baseAlfa[$letter]);
                    $out .= ('=');
                }
            } else{
                $out .= (self::$baseAlfa[$letter]);
                $out .= ("==");
            }
        }

        return $out;
    }
}
