<?php
/**
 * Author: michael
 * Date: 3/1/22
 */

namespace Erc\Cli;

trait UserTrait {
    /**
     * @return array
     */
    protected function getPasswordChoice(): string
    {
        $choice1 = 'Generate random password';
        $choice2 = 'Set password';
        $options = [$choice1, $choice2];
        $input = $this->climate->radio("Select how to set this user's password:", $options);
        switch ($input->prompt()) {
            case $choice1:
                $password = $this->randomPassword();
                break;
            case $choice2:
                $password = false;
                while ($password === false) {
                    $password = $this->enterPassword();
                }
                break;
        }
        return $password;
    }

    protected function randomPassword() {
        $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * @return void
     */
    protected function enterPassword()
    {
        $input = $this->climate->input("Enter the password for this user");
        $answer = $input->prompt();
        $input = $this->climate->input("Enter the password for this user");
        $confirmation = $input->prompt();
        if(strcmp($answer, $confirmation) ==0 ) return $answer;
        return false;
    }
}
