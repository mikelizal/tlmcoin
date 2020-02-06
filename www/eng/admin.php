<?php


function adminsession_enter() {
    $_SESSION['auth']=1;
}

function adminsession_clear() {
    $_SESSION['auth']=1;
    session_destroy();
}

function check_adminsession() {
    if ( isset($_SESSION['auth']) ) {
        if ($_SESSION['auth']==1) {
            return true;
        }
    }
    return false;
}




function render_loginform($error = null) {
    if ($error) {
        print('<h2>'.$error.'</h2>'."\n");
    }
    print('<form method="POST" action="./login.php">'."\n");
    print('Name <input type="text" name="u" value=""><br>'."\n");    
    print('Pass <input type="password" name="p" value=""><br>'."\n");    
    print('<input type="submit" name="enter" value="enter"><br>'."\n");    
    print('</form>'."\n");    
}

function render_logoutlink() {
    print('[<a href="./logout.php">Exit</a>]'."\n");
} 

?>