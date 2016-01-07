<?php
// sh
// echo extension=uopz.so >> /etc/php5/fpm/php.ini
// echo uopz.overloads=1 >> /etc/php5/fpm/php.ini

return function($timeTravel) {
    // replace time()
    uopz_rename("time", "original_time");
    uopz_function("time", function() use($timeTravel) {
        return original_time() + $timeTravel;
    });

    // replace strtotime()
    uopz_rename("strtotime", "original_strtotime");
    uopz_function("strtotime", function($time, $now = 0) {
        if (isset($now) && !empty($now)) {
            return original_strtotime($time, $now);
        } else {
            return original_strtotime($time, time());
        }
    });

    // replace date()
    uopz_rename("date", "original_date");
    uopz_function("date", function($format, $timestamp = 0) {
        if (isset($timestamp) && !empty($timestamp)) {
            return original_date($format, $timestamp);
        } else {
            return original_date($format, time());
        }
    });

    return true;
};
