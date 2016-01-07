<?php
// sh
// echo extension=runkit.so >> /etc/php5/fpm/php.ini
// echo runkit.internal_override=1 >> /etc/php5/fpm/php.ini

return function($timeTravel) {
    $GLOBALS["__timeTravel"] = $timeTravel;

    // replace time()
    runkit_function_rename ("time", "original_time");
    runkit_function_add("time", "", <<<'FUNC'
        return original_time() + $GLOBALS["__timeTravel"];
FUNC
    );

    // replace strtotime()
    runkit_function_rename ("strtotime", "original_strtotime");
    runkit_function_add("strtotime", '$time, $now = 0', <<<'FUNC'
        if (isset($now)
                && !empty($now))
            return original_strtotime($time, $now);
        else
            return original_strtotime($time, time());
FUNC
    );

    // replace date()
    runkit_function_rename ("date", "original_date");
    runkit_function_add("date", '$format, $timestamp = 0', <<<'FUNC'
        if (isset($timestamp)
                && !empty($timestamp))
            return original_date($format, $timestamp);
        else
            return original_date($format, time());
FUNC
    );

    return true;
};
