<?php

class AppException extends Exception {
    public $status;

    public function __construct($message, $status = 400) {
        parent::__construct($message);
        $this->status = $status;
    }
}
