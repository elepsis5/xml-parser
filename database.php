<?php
/**
 * Description
 * 
 * Php version 8.1.5

 * @category Components
 * @package  WordPress
 * @author   Your Name <yourname@example.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link     https://yoursite.com
 * @since    1.0.0
 */


/**
 * Description class
 * 
 * @category Components
 * @package  WordPress
 * @author   Your Name <yourname@example.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link     https://yoursite.com
 * @since    1.0.0
 */
class Database
{
    private $_host;
    private $_dbname;
    private $_user;
    private $_pass;
    public $result;
    /**
     * Comment 
     * 
     * @param string $host   parameter
     * @param string $dbname parameter
     * @param string $user   parameter
     * @param string $pass   parameter
     * 
     * @return no
     */
    public function __construct($host, $dbname, $user, $pass)
    {
        $this->_host = $host;
        $this->_dbname = $dbname;
        $this->_user = $user;
        $this->_pass = $pass;
        $this->result = null;

        try {
            $this->result = new PDO(
                'mysql:host=' . $this->_host . ';
                dbname=' . $this->_dbname, $this->_user, $this->_pass
            );
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage() . '!';
        }
        //return $this->result;
    }

    /**
     * Description func
     * 
     * @return db
     */
    public function getDb()
    {
        if ($this->result instanceof PDO) {
            return $this->result;
        }
    }
}