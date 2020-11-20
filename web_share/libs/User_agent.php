<?php
require_once('ext/lib_base'.EXT);
class User_agent extends lib_base {
    public function __construct() {
        parent::__construct();
        if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $this->agent = trim($_SERVER['HTTP_USER_AGENT']);
            $this->_compile_data();
        }
    }
    public $agent = NULL;
    public $is_browser = FALSE;
    public $is_robot = FALSE;
    public $is_mobile = FALSE;
    public $languages = array();
    public $charsets = array();
    public $platforms = array(
        'windows nt 10.0'   => 'Windows 10',
        'windows nt 6.3'    => 'Windows 8.1',
        'windows nt 6.2'    => 'Windows 8',
        'windows nt 6.1'    => 'Windows 7',
        'windows nt 6.0'    => 'Windows Vista',
        'windows nt 5.2'    => 'Windows 2003',
        'windows nt 5.1'    => 'Windows XP',
        'windows nt 5.0'    => 'Windows 2000',
        'windows nt 4.0'    => 'Windows NT 4.0',
        'winnt4.0'          => 'Windows NT 4.0',
        'winnt 4.0'         => 'Windows NT',
        'winnt'             => 'Windows NT',
        'windows 98'        => 'Windows 98',
        'win98'             => 'Windows 98',
        'windows 95'        => 'Windows 95',
        'win95'             => 'Windows 95',
        'windows phone'         => 'Windows Phone',
        'windows'           => 'Unknown Windows OS',
        'android'           => 'Android',
        'blackberry'        => 'BlackBerry',
        'iphone'            => 'iOS',
        'ipad'              => 'iOS',
        'ipod'              => 'iOS',
        'os x'              => 'Mac OS X',
        'ppc mac'           => 'Power PC Mac',
        'freebsd'           => 'FreeBSD',
        'ppc'               => 'Macintosh',
        'linux'             => 'Linux',
        'debian'            => 'Debian',
        'sunos'             => 'Sun Solaris',
        'beos'              => 'BeOS',
        'apachebench'       => 'ApacheBench',
        'aix'               => 'AIX',
        'irix'              => 'Irix',
        'osf'               => 'DEC OSF',
        'hp-ux'             => 'HP-UX',
        'netbsd'            => 'NetBSD',
        'bsdi'              => 'BSDi',
        'openbsd'           => 'OpenBSD',
        'gnu'               => 'GNU/Linux',
        'unix'              => 'Unknown Unix OS',
        'symbian'           => 'Symbian OS'
    );
    public $browsers = array(
        'OPR'           => 'Opera',
        'Flock'         => 'Flock',
        'Edge'          => 'Spartan',
        'CriOS'         => 'Chrome',
        'Chrome'        => 'Chrome',
        'Opera.*?Version'   => 'Opera',
        'Opera'         => 'Opera',
        'MSIE'          => 'Internet Explorer',
        'Internet Explorer' => 'Internet Explorer',
        'Trident.* rv'  => 'Internet Explorer',
        'Shiira'        => 'Shiira',
        'FxiOS'         => 'FxiOS',
        'Firefox'       => 'Firefox',
        'Chimera'       => 'Chimera',
        'Phoenix'       => 'Phoenix',
        'Firebird'      => 'Firebird',
        'Camino'        => 'Camino',
        'Netscape'      => 'Netscape',
        'OmniWeb'       => 'OmniWeb',
        'Safari'        => 'Safari',
        'Mozilla'       => 'Mozilla',
        'Konqueror'     => 'Konqueror',
        'icab'          => 'iCab',
        'Lynx'          => 'Lynx',
        'Links'         => 'Links',
        'hotjava'       => 'HotJava',
        'amaya'         => 'Amaya',
        'IBrowse'       => 'IBrowse',
        'Maxthon'       => 'Maxthon',
        'Ubuntu'        => 'Ubuntu Web Browser',
        
    );
    public $mobiles = array(
        'mobileexplorer'    => 'Mobile Explorer',
        'palmsource'        => 'Palm',
        'palmscape'         => 'Palmscape',
        'motorola'      => 'Motorola',
        'nokia'         => 'Nokia',
        'palm'          => 'Palm',
        'iphone'        => 'Apple iPhone',
        'ipad'          => 'iPad',
        'ipod'          => 'Apple iPod Touch',
        'sony'          => 'Sony Ericsson',
        'ericsson'      => 'Sony Ericsson',
        'blackberry'    => 'BlackBerry',
        'cocoon'        => 'O2 Cocoon',
        'blazer'        => 'Treo',
        'lg'            => 'LG',
        'amoi'          => 'Amoi',
        'xda'           => 'XDA',
        'mda'           => 'MDA',
        'vario'         => 'Vario',
        'htc'           => 'HTC',
        'samsung'       => 'Samsung',
        'sharp'         => 'Sharp',
        'sie-'          => 'Siemens',
        'alcatel'       => 'Alcatel',
        'benq'          => 'BenQ',
        'ipaq'          => 'HP iPaq',
        'mot-'          => 'Motorola',
        'playstation portable'  => 'PlayStation Portable',
        'playstation 3'     => 'PlayStation 3',
        'playstation vita'      => 'PlayStation Vita',
        'hiptop'        => 'Danger Hiptop',
        'nec-'          => 'NEC',
        'panasonic'     => 'Panasonic',
        'philips'       => 'Philips',
        'sagem'         => 'Sagem',
        'sanyo'         => 'Sanyo',
        'spv'           => 'SPV',
        'zte'           => 'ZTE',
        'sendo'         => 'Sendo',
        'nintendo dsi'  => 'Nintendo DSi',
        'nintendo ds'   => 'Nintendo DS',
        'nintendo 3ds'  => 'Nintendo 3DS',
        'wii'           => 'Nintendo Wii',
        'open web'      => 'Open Web',
        'openweb'       => 'OpenWeb',

        // Operating Systems
        'android'       => 'Android',
        'symbian'       => 'Symbian',
        'SymbianOS'     => 'SymbianOS',
        'elaine'        => 'Palm',
        'series60'      => 'Symbian S60',
        'windows ce'    => 'Windows CE',

        // Browsers
        'obigo'         => 'Obigo',
        'netfront'      => 'Netfront Browser',
        'openwave'      => 'Openwave Browser',
        'mobilexplorer' => 'Mobile Explorer',
        'operamini'     => 'Opera Mini',
        'opera mini'    => 'Opera Mini',
        'opera mobi'    => 'Opera Mobile',
        'fennec'        => 'Firefox Mobile',

        // Other
        'digital paths' => 'Digital Paths',
        'avantgo'       => 'AvantGo',
        'xiino'         => 'Xiino',
        'novarra'       => 'Novarra Transcoder',
        'vodafone'      => 'Vodafone',
        'docomo'        => 'NTT DoCoMo',
        'o2'            => 'O2',

        // Fallback
        'mobile'        => 'Generic Mobile',
        'wireless'      => 'Generic Mobile',
        'j2me'          => 'Generic Mobile',
        'midp'          => 'Generic Mobile',
        'cldc'          => 'Generic Mobile',
        'up.link'       => 'Generic Mobile',
        'up.browser'    => 'Generic Mobile',
        'smartphone'    => 'Generic Mobile',
        'cellphone'     => 'Generic Mobile'
    );
    public $platform = '';
    public $browser = '';
    public $version = '';
    public $mobile = '';
    public $referer;

    public function parse($string = "")
    {
        // Reset values
        $this->is_browser = FALSE;
        $this->is_robot = FALSE;
        $this->is_mobile = FALSE;
        $this->browser = '';
        $this->version = '';
        $this->mobile = '';

        
        // Set the new user-agent string and parse it, unless empty
        if ( ! empty($string))
        {
            $this->agent = $string;
            $this->_compile_data();
        }else{
            $this->agent = trim($_SERVER['HTTP_USER_AGENT']);
            $this->_compile_data();
        }
    }

    protected function _compile_data (){
            $this->_set_platform();
            foreach (array('_set_browser', '_set_mobile') as $function)
            {
                if ($this->$function() === TRUE)
                {
                    break;
                }
            }
        
    }
    protected function _set_platform()
    {
        if (is_array($this->platforms) && count($this->platforms) > 0)
        {
            foreach ($this->platforms as $key => $val)
            {
                if (preg_match('|'.preg_quote($key).'|i', $this->agent))
                {
                    $this->platform = $val;
                    return TRUE;
                }
            }
        }

        $this->platform = 'Unknown Platform';
        return FALSE;
    }

    public function platform(){
        return $this->platform;
    }

    protected function _set_browser()
    {
        if (is_array($this->browsers) && count($this->browsers) > 0)
        {
            foreach ($this->browsers as $key => $val)
            {
                if (preg_match('|'.$key.'.*?([0-9\.]+)|i', $this->agent, $match))
                {
                    $this->is_browser = TRUE;
                    $this->version = $match[1];
                    $this->browser = $val;
                    $this->_set_mobile();
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    public function browser()
    {
        return $this->browser;
    }

    public function version()
    {
        return $this->version;
    }

    protected function _set_mobile()
    {
        if (is_array($this->mobiles) && count($this->mobiles) > 0)
        {
            foreach ($this->mobiles as $key => $val)
            {
                if (FALSE !== (stripos($this->agent, $key)))
                {
                    $this->is_mobile = TRUE;
                    $this->mobile = $val;
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    public function mobile()
    {
        return $this->mobile;
    }

}