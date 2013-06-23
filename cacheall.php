<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
*
* Cacheall Class
* Stacked partial caching for CodeIgnitor
* This library can cache several parts of a page (views and data) and creates one cachefile
* Making partial caching more efficient
* See readme.txt or cache_all_info.php for example use
*
* @category       Libraries
* @author         RvH <ricardo1956@live.nl>
* @version        0.9.4  date 2013-06-11
* @license        cacheall by RvH is licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License
* @copyright     (c) 2013 RvH
* @link           http://www.happyshop.nl/index.php/home/cicaching
*/

class Cacheall
{
    private $_ci;
    private $_expiration;
    private $_path;
    private $_seperator;
    private $_serialize;

    /**
     * Constructor - Initializes and references CI and picks up configuration
     */
    function __construct($params=array())
    {
        $this->_ci = & get_instance();
        // The values below should be defined in application/config/config.php or at this point directly
        $this->_expiration = $this->_ci->config->item('library_cacheall_expiration'); // $config['library_cacheall__expiration'] = 86400; in seconds = 1 day
        $this->_path       = $this->_ci->config->item('library_cacheall_path');       // $config['library_cacheall__path']       = '/your_pathtoCI/application/cache/';
        $this->_seperator  = $this->_ci->config->item('library_cacheall_seperator');  // $config['library_cacheall__seperator']  = '||||';
        $this->_serialize  = $this->_ci->config->item('library_cacheall_serialize');  // $config['library_cacheall__serialize']  = 0;
    }

    /**
     * Read Cache File
     *
     * @access   public
     * @param    string
     * @return   cache or false
     */
    function read($cachefilename)
    {
        $file_path = $this->_path.md5($cachefilename);

        if (@is_file($file_path))                                               // is_file() is faster than file_exists()
        {
            if ( isset($_SERVER['REQUEST_TIME']) )                              // not always available, but if so faster dan time()
                $now = $_SERVER['REQUEST_TIME'];
            else
                $now = time();

            $time_of_cachefile = filemtime($file_path);

            if ( ($now -  $time_of_cachefile) < $this->_expiration)             // only read the cachefile if it is still valid
            {
                $cache = '';

                if ( ! $this->_serialize)
                    $cache = file_get_contents($file_path);
                else
                    $cache = un_serialize(file_get_contents($file_path));

                return $cache;                                                  // cache ok so stop
            }
            @unlink($file_path);                                                // cache too old delete it
        }
        return false;                                                           // cache file does not exist or is too old
    }

    /**
     * Write Cache File
     *
     * @access   public
     * @param    array
     * @param    string
     * @return   cache
     */
    function write($cached_items=array(),$cachefilename)
    {
        $output = '';
        $error  = 0;

        if (is_dir($this->_path) && is_really_writable($this->_path))
        {
            ignore_user_abort(true);                                            // create the cache even if the user leaves the page

            $file_path = $this->_path.md5($cachefilename);

            foreach ($cached_items as $item)
            {
                if ( is_array($item) && sizeof($item) )
                    foreach($item as $data)                                     // data or seperator
                        $output .= $data;
                else if ($item === $this->_seperator)
                    $output .= $this->_seperator;                               // seperator
                else
                    $output .= $this->_ci->load->view($item,'',true);           // views
            }

            if ( $this->_serialize)                                             // serialize if requested
                $output = serialize($output);

            if (!@file_put_contents($file_path,$output, LOCK_EX))               // write
                $error = 1;
        }
        else
            $error = 1;
        if ($error)
            log_message('error', "Could not write cachefile : $file_path check permissions");
        return $output;
    }

    /**
     * Delete Cache File
     *
     * @access    public
     * @param     string
     * @return    string or empty string on failure
     */
    function delete($cachefilename)
    {
        $file_path = $this->_path.md5($cachefilename);
        if ( !@unlink($file_path))
            log_message('error', "Could not delete cachefile $file_path check permissions");
    }

    /**
     * Delete Full Cache
     *
     * @access    public
     * @param     void
     * @return    void
     */
    function delete_all()
    {
        $files = array_diff(scandir( $this->_path), array('..', '.','.htaccess','index.html'));
        foreach($files as $file)
            if ( ! @unlink($this->_path.$file) )
                log_message('error', "Could not delete cachefile $this->_path.$file check permissions");
    
    }}
