<?php

namespace Baikal\Core;
use Baikal\Model\User;

/**
 * This is an authentication backend that uses a database to manage passwords.
 *
 * Format of the database tables must match to the one of \Sabre\DAV\Auth\Backend\PDO
 *
 * @copyright Copyright (C) 2013 Lukasz Janyst. All rights reserved.
 * @author Lukasz Janyst <ljanyst@buggybrain.net>
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 *
 * Modification by Moodle  Vitaly Sanda(Email:vit00lya@yandex.ru;Skype:vetalya.one)
 * Date: 05.01.15
 *
 */
class PDOBasicAuth extends \Sabre\DAV\Auth\Backend\AbstractBasic {

    /**
     * Reference to PDO connection
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * PDO table name we'll be using
     *
     * @var string
     */
    protected $tableName;

    /**
     * Authentication realm
     *
     * @var string
     */
    protected $authRealm;

    /**
     * Creates the backend object.
     *
     * If the filename argument is passed in, it will parse out the specified file fist.
     *
     * @param PDO $pdo
     * @param string $tableName The PDO table name to use
     */
    public function __construct(\PDO $pdo, $authRealm, $tableName = 'users') {

        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->authRealm = $authRealm;
    }

    /**
     * Validates a username and password
     *
     * This method should return true or false depending on if login
     * succeeded.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function validateUserPass($username, $password) {

        $stmt = $this->pdo->prepare('SELECT username, digesta1 FROM '.$this->tableName.' WHERE username = ?');
        $stmt->execute(array($username));
        $result = $stmt->fetchAll();

        //если Байкал не нашёл пользователя в своей базе он запрашивает его в базе moodle
        if (!count($result)) {

            require_once('../../../../../../config.php');

            global $DB, $CFG;

            require_once($CFG->dirroot .'/lib/moodlelib.php');

            $user = $DB->get_record('user', array('username'=>$username));
            if(validate_internal_user_password($user,$password)) return true;
                else return false;
        };

        $hash = md5( $username . ':' . $this->authRealm . ':' . $password );
        if( $result[0]['digesta1'] == $hash )
        {
            $this->currentUser = $username;
            return true;
        }
        return false;

    }

}
