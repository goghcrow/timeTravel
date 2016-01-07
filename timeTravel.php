<?php
// uopz 与 runkit 扩展二选一，推荐uopz

// uopz
// echo extension=uopz.so >> /etc/php5/fpm/php.ini
// echo uopz.overloads=1 >> /etc/php5/fpm/php.ini
//
// runkit
// echo extension=runkit.so >> /etc/php5/fpm/php.ini
// echo runkit.internal_override=1 >> /etc/php5/fpm/php.ini

return function($travelName) {
	$noop = function() { return false; };
	if(!is_string($travelName) || !$travelName) {
		return $noop;
	}

	/*
	if(isset($_ENV[$travelName])) {
		return $noop;
	}
	*/

	if(!isset($_ENV[$travelName]) || !$_ENV[$travelName]) {
		if(isset($_REQUEST[$travelName]) && $_REQUEST[$travelName]) {
			$_ENV[$travelName] = (int)$_REQUEST[$travelName];
		} else {
			return $noop;
		}
	}

	$travelByUopz = function() use($travelName) {
	    // replace time()
	    uopz_rename("time", "original_time");
	    uopz_function("time", function() use($travelName) {
	        return original_time() + $_ENV[$travelName];
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

	$travelByRunkit = function() use($travelName) {
	    // replace time()
	    runkit_function_rename ("time", "original_time");
	    runkit_function_add("time", "", <<<FUNC
	        return original_time() + \$_ENV["$travelName"];
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

	if(extension_loaded("uopz")) {
		ini_set("uopz.overloads", 1);
		return $travelByUopz;
	} else if(extension_loaded("runkit")) {
		ini_set("runkit.internal_override", 1);
		return $travelByRunkit;
	} else {
		return $noop;
	}
};
