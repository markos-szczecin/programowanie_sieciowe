<?php
session_name('mailer');
session_save_path('/tmp');
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();