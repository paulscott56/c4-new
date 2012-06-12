<?php

namespace C4\Library\Session\Exception;

interface ExceptionInterface
{
    /**
     * sessionStartError
     *
     * @var string PHP Error Message
     */
    //static public $sessionStartError = null;

    /**
     * handleSessionStartError() - interface for set_error_handler()
     *
     * @param  int    $errno
     * @param  string $errstr
     * @return void
     */
//    static public function handleSessionStartError($errno, $errstr, $errfile, $errline, $errcontext)
//    {
//        self::$sessionStartError = $errfile . '(Line:' . $errline . '): Error #' . $errno . ' ' . $errstr . ' ' . $errcontext;
//    }

    /**
     * handleSilentWriteClose() - interface for set_error_handler()
     *
     * @param  int    $errno
     * @param  string $errstr
     * @return void
     */
//    static public function handleSilentWriteClose($errno, $errstr, $errfile, $errline, $errcontext)
//    {
//        self::$sessionStartError .= PHP_EOL . $errfile . '(Line:' . $errline . '): Error #' . $errno . ' ' . $errstr . ' ' . $errcontext;
//    }
}

