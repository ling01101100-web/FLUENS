<?php
if (is_post()) {
    csrf_check();
    logout();
    session_start();
    flash('success', 'You have been logged out.');
}
redirect('login');
