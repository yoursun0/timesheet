<?php
// $Id: mrbs_auth.inc,v 1.8.2.1 2005/03/29 13:26:27 jberanek Exp $

// include the authentification wrappers
include "auth_$auth[type].inc";
if (isset($auth['session'])) include "session_$auth[session].inc";



/* getAuthorised($user, $pass, $level)
 * 
 * Check to see if the user name/password is valid
 * 
 * $user  - The user name
 * $pass  - The users password
 * $level - The access level required
 * 
 * Returns:
 *   0        - The user does not have the required access
 *   non-zero - The user has the required access
 */
function getAuthorised($level)
{
    global $auth;

    $user = getUserName();
    if(isset($user) == FALSE) {
        authGet();
        return 0;
    }

    return authGetUserLevel($user, $auth["admin"]) >= $level;
}

/* getWritable($creator, $user)
 * 
 * Determines if a user is able to modify an entry
 *
 * $creator - The creator of the entry
 * $user    - Who wants to modify it
 *
 * Returns:
 *   0        - The user does not have the required access
 *   non-zero - The user has the required access
 */
function getWritable($creator, $user)
{
    global $auth;

    // Always allowed to modify your own stuff
    if(strcasecmp($creator, $user) == 0)
        return 1;

    if(authGetUserLevel($user, $auth["admin"]) >= 2)
        return 1;

    // Unathorised access
    return 0;
}

/* showAccessDenied()
 * 
 * Displays an appropate message when access has been denied
 * 
 * Retusns: Nothing
 */
function showAccessDenied($day, $month, $year, $area)
{
    global $HTTP_REFERER;

    print_header($day, $month, $year, $area);
?>
  <H1><?php echo get_vocab("accessdenied")?></H1>
  <P>
   <?php echo get_vocab("norights")?>
  </P>
  <P>
   <A HREF="<?php echo $HTTP_REFERER; ?>"><?php echo get_vocab("returnprev"); ?></A>
  </P>
 </BODY>
</HTML>
<?php
}
?>
