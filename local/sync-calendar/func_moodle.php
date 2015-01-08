<?php
/**
 * Created by Vitaly Sanda(Email:vit00lya@yandex.ru;Skype:vetalya.one)
 * Date: 06.01.15
 */


       function moodle_name_search($username, $password)
       {
           $piece = explode('/', $_SERVER['SCRIPT_NAME']);

           require_once($_SERVER['DOCUMENT_ROOT'] . '/' . $piece[1] . '/config.php');

           global $DB, $CFG;

           require_once($CFG->dirroot . '/lib/moodlelib.php');

           $user = $DB->get_record('user', array('username' => $username));

           if (validate_internal_user_password($user, $password)) {

               $oModel = new \Baikal\Model\User();

           } else return false;

       }
