<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Locker
 *
 * @author hoabt2
 */

namespace App\Common;

class Locker {

    //put your code here
    private $sem;
    private $is_unlock;

    function __construct($code) {
        $this->sem = sem_get($code, 1);
        $this->is_unlock = false;
    }

    function lock() {
        if (sem_acquire($this->sem)) {
            return true;
        }
        return false;
    }

    function getLock() {
        return $this->is_unlock;
    }

    function unlock() {
        sem_release($this->sem);
        $this->is_unlock = true;
    }

    function __destruct() {
        if (!$this->is_unlock) {
            sem_release($this->sem);
        }
    }

}
