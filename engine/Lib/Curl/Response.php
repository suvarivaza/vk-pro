<?php

class Lib_Curl_Response
{
    private static $_errorText = [
        1 => 'Unsupported protocol. This build of curl has no support for this protocol.',
        2 => 'Failed to initialize.',
        3 => 'URL malformat. The syntax was not correct.',
        4 => 'URL user malformatted. The user-part of the URL syntax was not correct.',
        5 => 'Couldn\'t resolve proxy. The given proxy host could not be resolved.',
        6 => 'Couldn\'t resolve host. The given remote host was not resolved.',
        7 => 'Failed to connect to host.',
        8 => 'FTP weird server reply. The server sent data curl couldn\'t parse.',
        9 => 'FTP access denied. The server denied login.',
        10 => 'FTP user/password incorrect. Either one or both were not accepted by the server.',
        11 => 'FTP weird PASS reply. Curl couldn\'t parse the reply sent to the PASS request.',
        12 => 'FTP weird USER reply. Curl couldn\'t parse the reply sent to the USER request.',
        13 => 'FTP weird PASV reply, Curl couldn\'t parse the reply sent to the PASV request.',
        14 => 'FTP weird 227 format. Curl couldn\'t parse the 227-line the server sent.',
        15 => 'FTP can\'t get host. Couldn\'t resolve the host IP we got in the 227-line.',
        16 => 'FTP can\'t reconnect. Couldn\'t connect to the host we got in the 227-line.',
        17 => 'FTP couldn\'t set binary. Couldn\'t change transfer method to binary.',
        18 => 'Partial file. Only a part of the file was transfered.',
        19 => 'FTP couldn\'t download/access the given file, the RETR (or similar) command failed.',
        20 => 'FTP write error. The transfer was reported bad by the server.',
        21 => 'FTP quote error. A quote command returned error from the server.',
        22 => 'HTTP page not retrieved. The requested url was not found or returned another error with the HTTP error code being 400 or above.\n This return code only appears if -f/--fail is used.',
        23 => 'Write error. Curl couldn\'t write data to a local filesystem or similar.',
        24 => 'Malformed user. User name badly specified.',
        25 => 'FTP couldn\'t STOR file. The server denied the STOR operation, used for FTP uploading.',
        26 => 'Read error. Various reading problems.',
        27 => 'Out of memory. A memory allocation request failed.',
        28 => 'Operation timeout. The specified time-out period was reached according to the conditions.',
        29 => 'FTP couldn\'t set ASCII. The server returned an unknown reply.',
        30 => 'FTP PORT failed. The PORT command failed. Not all FTP servers support the PORT command, try doing a transfer using PASV instead!',
        31 => 'FTP couldn\'t use REST. The REST command failed. This command is used for resumed FTP transfers.',
        32 => 'FTP couldn\'t use SIZE. The SIZE command failed. The command is an extension to the original FTP spec RFC 959.',
        33 => 'HTTP range error. The range "command" didn\'t work.',
        34 => 'HTTP post error. Internal post-request generation error.',
        35 => 'SSL connect error. The SSL handshaking failed.',
        36 => 'FTP bad download resume. Couldn\'t continue an earlier aborted download.',
        37 => 'FILE couldn\'t read file. Failed to open the file. Permissions?',
        38 => 'LDAP cannot bind. LDAP bind operation failed.',
        39 => 'LDAP search failed.',
        40 => 'Library not found. The LDAP library was not found.',
        41 => 'Function not found. A required LDAP function was not found.',
        42 => 'Aborted by callback. An application told curl to abort the operation.',
        43 => 'Internal error. A function was called with a bad parameter.',
        44 => 'Internal error. A function was called in a bad order.',
        45 => 'Interface error. A specified outgoing interface could not be used.',
        46 => 'Bad password entered. An error was signaled when the password was entered.',
        47 => 'Too many redirects. When following redirects, curl hit the maximum amount.',
        48 => 'Unknown TELNET option specified.',
        49 => 'Malformed telnet option.',
        51 => 'The remote peer\'s SSL certificate wasn\'t ok',
        52 => 'The server didn\'t reply anything, which here is considered an error.',
        53 => 'SSL crypto engine not found',
        54 => 'Cannot set SSL crypto engine as default',
        55 => 'Failed sending network data',
        56 => 'Failure in receiving network data',
        57 => 'Share is in use (internal error)',
        58 => 'Problem with the local certificate',
        59 => 'Couldn\'t use specified SSL cipher',
        60 => 'Problem with the CA cert (path? permission?)',
        61 => 'Unrecognized transfer encoding',
        62 => 'Invalid LDAP URL',
        63 => 'Maximum file size exceeded',
    ];

    /** @var resource CURL handle */
    private $_ch;
    /** @var array CURL params */
    private $_params = [];

    /** @var string */
    private $_body = '';
    /** @var array */
    private $_info = [];
    /** @var string */
    private $_error = '';
    /** @var int */
    private $_errno = 0;

    /**
     * @param $params
     */
    public function __construct($params)
    {
        $this->_params = $params;
        $this->_body = $this->_queryModule();
    }

    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @return mixed|string
     */
    private function _queryModule()
    {
        $this->_ch = curl_init();

        if (isset($this->_params['use_proxy']) && $this->_params['use_proxy']) {
            curl_setopt($this->_ch, CURLOPT_PROXY, $this->_params['proxy']->ip);
        }

        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable

        if (isset($this->_params['timeout']) && is_integer($this->_params['timeout'])) {
            curl_setopt($this->_ch, CURLOPT_TIMEOUT, $this->_params['timeout']);
        }

        if (isset($this->_params['follow_redirect']) && $this->_params['follow_redirect']) {
            curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
            curl_setopt($this->_ch, CURLOPT_AUTOREFERER, 1);
        }

        if (isset($this->_params['httpheader']) && is_array($this->_params['httpheader']) && sizeof($this->_params['httpheader'])) {
            curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $this->_params['httpheader']); //send header
        }

        if (isset($this->_params['no_error']) && $this->_params['no_error']) {
            curl_setopt($this->_ch, CURLOPT_FAILONERROR, 1);
        }
        // params['silent'] removed, because it is deprecated from php5.3
        if (isset($this->_params['agent']) && $this->_params['agent']) {
            curl_setopt($this->_ch, CURLOPT_USERAGENT, $this->_params['agent']);
        }

        if (isset($this->_params['cookie']) && $this->_params['cookie'] != '') {
            if (is_file($this->_params['cookie'])) {
                curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $this->_params['cookie']);
                curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $this->_params['cookie']);
            } else {
                curl_setopt($this->_ch, CURLOPT_COOKIE, $this->_params['cookie']);
            }
        }

        if (isset($this->_params['referrer']) && $this->_params['referrer']) {
            curl_setopt($this->_ch, CURLOPT_REFERER, $this->_params['referrer']);
        }

        if (isset($this->_params['get_headers']) && $this->_params['get_headers']) {
            curl_setopt($this->_ch, CURLOPT_HEADER, 1);
        }

        if (isset($this->_params['no_body']) && $this->_params['no_body']) {
            curl_setopt($this->_ch, CURLOPT_NOBODY, 1);
        }

        if (isset($this->_params['query']) && $this->_params['query'] != '') {
            curl_setopt($this->_ch, CURLOPT_POST, 1); // set POST method
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $this->_params['query']); // add POST fields
        } else {
            curl_setopt($this->_ch, CURLOPT_POST, 0);
        } // set GET method

        if (isset($this->_params['username']) && $this->_params['username']) {
            curl_setopt($this->_ch, CURLOPT_USERPWD, $this->_params['username'] . ($this->_params['password'] ? ':' . $this->_params['password'] : ''));
        }

        if ($this->_params['url']) {
            curl_setopt($this->_ch, CURLOPT_URL, $this->_params['url']);
        }

        $body = '';
        $i = 0;
        $proxy = false;

        while ($this->_params['retry'] > $i) {
            if (isset($this->_params['only_proxy']) && $this->_params['only_proxy'] && $proxy == false) {
                error_log('lib.curl error: no proxy in DB');
                break;
            }

            $body = curl_exec($this->_ch); // run the whole process

            $this->_errno = curl_errno($this->_ch);
            $this->_info = curl_getinfo($this->_ch);

            if ($this->_errno != 0 || $body === false) {
                $this->_error = curl_error($this->_ch);

                if ($this->_error == '') {
                    $this->_error = self::$_errorText[$this->_errno];
                }

                if (!isset($this->_params['silent']) || $this->_params['silent'] !== true) {
                    error_log('Curl error: (' . $this->_errno . ') ' . $this->_error . ($this->_params['url'] ? (', URL: ' . $this->_params['url']) : ''));
                }
            } else {
                break;
            }

            $i++;
        }

        unset($proxy, $i, $err);
        curl_close($this->_ch);

        return $body;
    }

    /**
     * @return int
     */
    public function status()
    {
        return intval($this->_info['http_code']);
    }

    /**
     * @return mixed
     */
    public function body()
    {
        if (isset($this->_params['get_headers']) && true === $this->_params['get_headers']) {
            $headers = $this->headers();
            $body = substr($this->_body, $this->_info['header_size']);

            if (isset($headers['Content-Encoding']) && in_array('gzip', $headers['Content-Encoding'])) {
                $body = gzinflate(substr($body, 10));
            }

            return $body;
        }

        $headers = $this->headers();
        $body = $this->_body;

        if (isset($headers['Content-Encoding']) && in_array('gzip', $headers['Content-Encoding'])) {
            $body = gzinflate($this->_body);
        }

        return $body;
    }

    /**
     * @return array
     */
    public function headers()
    {
        $headers = [];
        $raw_headers = explode("\r\n", substr($this->_body, 0, $this->_info['header_size']));

        foreach ($raw_headers as &$raw_header) {
            if (strpos($raw_header, ':') === false) {
                continue;
            }
            list($name, $value) = explode(':', $raw_header, 2);
            $headers[$name][] = trim($value);
        }

        return $headers;
    }

    /**
     * @return mixed
     */
    public function contentType()
    {
        return $this->_info['content_type'];
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->_info;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * @return int
     */
    public function getErrorNo()
    {
        return $this->_errno;
    }
}
