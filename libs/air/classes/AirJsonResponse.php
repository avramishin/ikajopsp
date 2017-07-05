<?php

/**
 * Author: Vadim L. Avramishin <avramishin@gmail.com>
 * Class AirJsonResponse
 */
class AirJsonResponse
{
    public $errors = [];
    public $notify = [];
    public $data = [];
    public $rows = [];
    public $total = 0;
    public $jsonHeader = true;

    function error($errors)
    {
        $this->errors = is_array($errors) ? $errors : [$errors];
        $this->send();
    }

    /**
     * @param $message string
     * @param $class (success, info, warn, error)
     */
    function notify($message, $class)
    {
        $this->notify[] = [
            'message' => $message,
            'class' => $class
        ];
    }

    function send($exit = true)
    {
        if ($this->jsonHeader) {
            header('Content-Type: application/json');
        }

        echo json_encode($this);
        flush();
        if ($exit) {
            exit();
        }
    }
}