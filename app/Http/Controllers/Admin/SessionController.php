<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    public function ajaxCheck()
    {
        /*
         * TODO abstract this logic away to the domain
         */

        // Configuration
        $maxIdleBeforeLogout = 1000 * 1;
        $maxIdleBeforeWarning = 90 * 1;
        $warningTime = $maxIdleBeforeLogout - $maxIdleBeforeWarning;

        // Calculate the number of seconds since the user's last activity
        $idleTime = time() - Session::get('lastActive');
        // Warn the user they will be logged out if idle for too long
        if ($idleTime > $maxIdleBeforeWarning && empty(Session::get('idleWarningDisplayed'))) {
            Session::put('idleWarningDisplayed', true);
            return 'You have ' . $warningTime . ' seconds left before you are logged out';
        }

        // Log out the user if idle for too long
        if ($idleTime > $maxIdleBeforeLogout && empty(Session::get('logoutWarningDisplayed'))) {
            // *** Do stuff to log out user here
             Session::put('logoutWarningDisplayed', true);
             return 'loggedOut';
        }

        return '';
    }
}
