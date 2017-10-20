<?php
namespace libs;

class Tokens
{

    /**
     *
     * @var int $lengthToken
     */
    private $lengthToken = 25;

    /**
     *
     * @var int $lengthPassword
     */
    private $lengthPassword = 8;

    /**
     *
     * @var token
     */
    private $token;

    /**
     *
     * @var pass
     */
    private $password;

    /**
     *
     * @param number $lenTokn            
     * @param number $lenPass            
     */
    public function __construct($lenTokn = 25, $lenPass = 8)
    {
        if ($lenTokn<0) {
            $lenTokn = 25;
        }
        if ($lenPass < 0) {
            $lenPass = 25;
        }
        $this->lengthToken = $lenTokn;
        $this->lengthPassword = $lenPass;
    }

    /**
     */
    public function __destruct()
    {
        unset($this);
    }
    
    public function __toString(){
       return "the length of the password is set to $this->lengthPassword characters"; 
    }

    /**
     *
     * @param number $lengthToken            
     */
    public function setLengthToken($lengthToken)
    {
        $this->lengthToken = $lengthToken;
    }

    /**
     *
     * @param number $lengthPassword            
     */
    public function setLengthPassword($lengthPassword)
    {
        $this->lengthPassword = $lengthPassword;
    }

    /**
     *
     * @return the $tokenMd5
     */
    public function getTokenHased()
    {
        $this->token = self::createToken($this->lengthToken, true);
        return $this->token;
    }

    /**
     *
     * @return the $token
     */
    public function getToken()
    {
        $this->token = self::createToken($this->lengthToken, false);
        return $this->token;
    }

    /**
     *
     * @return the $password
     */
    public function getPassword()
    {
        $this->password = self::createPassword($this->lengthPassword, false);
        return $this->password;
    }

    /**
     *
     * @return the $passwordEspecial
     */
    public function getSpecialPassword()
    {
        $this->passwordEspecial = self::createPassword($this->lengthPassword, true);
        return $this->passwordEspecial;
    }

    private function createToken($length = 0, $isSha = false)
    {
        $token = '';
        if ($length < 0) {
            return $token;
        }
        
        mt_srand((double) microtime() * 1000000);
        $chars = array(
            'Q',
            '@',
            '8',
            'y',
            '%',
            '^',
            '5',
            'Z',
            '(',
            'G',
            '_',
            'O',
            '`',
            'S',
            '-',
            'N',
            '<',
            'D',
            '{',
            '}',
            '[',
            ']',
            'h',
            ';',
            'W',
            '.',
            '/',
            '|',
            ':',
            '1',
            'E',
            'L',
            '4',
            '&',
            '6',
            '7',
            '#',
            '9',
            'a',
            'A',
            'b',
            'B',
            '~',
            'C',
            'd',
            '>',
            'e',
            '2',
            'f',
            'P',
            'g',
            ')',
            '?',
            'H',
            'i',
            'X',
            'U',
            'J',
            'k',
            'r',
            'l',
            '3',
            't',
            'M',
            'n',
            '=',
            'o',
            '+',
            'p',
            'F',
            'q',
            '!',
            'K',
            'R',
            's',
            'c',
            'm',
            'T',
            'v',
            'j',
            'u',
            'V',
            'w',
            ',',
            'x',
            'I',
            '$',
            'Y',
            'z',
            '*'
        );
        
        // Array indice friendly number of chars; empty token string
        $numChars = count($chars) - 1;
        
        // Create random token at the specified lengthToken
        for ($i = 0; $i < $length; $i ++)
            $token .= $chars[mt_rand(0, $numChars)];
            
            // Should token be run through md5?
        if (true == $isSha) {
            
            // Number of 'N' char chunks
            $chunks = ceil(strlen($token) / $length);
            $md5token = '';
            
            // Run each chunk through md5
            for ($i = 1; $i <= $chunks; $i ++)
                $md5token .= hash("sha256", substr($token, $i * $length - $length, $length));
                
                // Trim the token
            $token = substr($md5token, 0, $length);
        }
        return $token;
    }

    /**
     *
     * @param integer $length            
     * @param boolean $specialChar            
     * @return string $passwd
     */
    private function createPassword($length = 0, $specialChar = false)
    {
        $passwd = '';
        if ($length < 0) {
            return $passwd;
        }
        // Seed random number generator
        // Only needed for PHP versions prior to 4.2
        mt_srand((double) microtime() * 1000000);
        
        // Array of digits, lower and upper characters; empty passwd string
        
        $chars = array(
            'digits' => array(
                0,
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9
            ),
            'lower' => array(
                'a',
                'b',
                'c',
                'd',
                'e',
                'f',
                'g',
                'h',
                'i',
                'j',
                'k',
                'l',
                'm',
                'n',
                'o',
                'p',
                'q',
                'r',
                's',
                't',
                'u',
                'v',
                'w',
                'x',
                'y',
                'z'
            ),
            'upper' => array(
                'A',
                'B',
                'C',
                'D',
                'E',
                'F',
                'G',
                'H',
                'I',
                'J',
                'K',
                'L',
                'M',
                'N',
                'O',
                'P',
                'Q',
                'R',
                'S',
                'T',
                'U',
                'V',
                'W',
                'X',
                'Y',
                'Z'
            )
        );
        
        // Add special chars to array, if permitted; adjust as desired
        if (true == $specialChar)
            $chars['special'] = array(
                '!',
                '@',
                '#',
                '$',
                '%',
                '^',
                '&',
                '*',
                '_',
                '+'
            );
            
            // Array indices (ei- digits, lower, upper)
        $charTypes = array_keys($chars);
        // Array indice friendly number of char types
        $numTypes = count($charTypes) - 1;
        
        // Create random password
        for ($i = 0; $i < $length; $i ++) {
            
            // Random char type
            $charType = $charTypes[mt_rand(0, $numTypes)];
            // Append random char to $passwd
            $passwd .= $chars[$charType][mt_rand(0, count($chars[$charType]) - 1)];
        }
        return $passwd;
    }
}
?>