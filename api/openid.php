<?php
/*
 * Include the needed pieces.
  */
  require_once "./lib/zend/libZend/Auth.php";
  require_once "./lib/zend/Zend/Auth/Adapter/OpenId.php";
  
  
  /*
   * Initialize our variables.
    */
    $status = "";
    $auth = Zend_Auth::getInstance();
    
    /*
     * There are 4 possible conditions.
      * First Condition
       * In this first test, we check to see if we already have an identity.  If we
        * do and the logout button has not been pressed, we simply display the
         * identity.  If the logout button HAS been pressed then we clear the
          * identity and notify the user.
           */
           if ($auth->hasIdentity()) {
               
                   if (isset($_POST['openid_action']) &&
                           $_POST['openid_action'] == "Logout") {
                                   $auth->clearIdentity();
                                           $status = "You are now logged out<br>\n";
                                               } else {
                                                       $status = "You are now logged in as ".$auth->getIdentity()."<br>\n";
                                                           }
                                                           /*
                                                            * Second Condition
                                                             * The login button was pressed and we have an openid_identifier.  Here we
                                                              * submit the openID identifier for authentication.  This means your page
                                                               * will be redirected to the OpenId service provider for authentication. Any
                                                                * code after the call to authenticate will be ignored unless something goes
                                                                 * wrong.
                                                                  *
                                                                   * If something does go horribly wrong, we notify the user and ask them to
                                                                    * look into it.
                                                                     */
                                                                     } else if  (isset($_POST['openid_action']) &&
                                                                          $_POST['openid_action'] == "Login" &&
                                                                               !empty($_POST['openid_identifier'])) {
                                                                               
                                                                                   $result = $auth->authenticate(
                                                                                           new Zend_Auth_Adapter_OpenId(@$_POST['openid_identifier']));
                                                                                               $status = 'Something went horribly wrong. Please consult your OpenID provider or $deity.'."<br />\n";
                                                                                               
                                                                                               /*
                                                                                                * Third Condition
                                                                                                 * This processes the callback from your OpenId service provider. This is a
                                                                                                  * rather simple check but it's enough to do the job. On the callback, we
                                                                                                   * recall authenticate() on the OpenId Adapter. Now it has all of the info
                                                                                                    * from your openId service provider and can make a decision as to whether or
                                                                                                     * not the authentication passed. If it did not pass, we do a clearIdentity()
                                                                                                      * just to make sure there's nothing there. In either case, we gather up any
                                                                                                       * messages the OpenID service provider sent and display them to the user.
                                                                                                        */
                                                                                                        }else if (isset($_GET['openid_mode']) ||
                                                                                                                  isset($_POST['openid_mode'])) {
                                                                                                                          
                                                                                                                              $result = $auth->authenticate(new Zend_Auth_Adapter_OpenId());
                                                                                                                              
                                                                                                                                  if (!$result->isValid()) {
                                                                                                                                          $auth->clearIdentity();
                                                                                                                                              } //if (!$result->isValid())
                                                                                                                                              
                                                                                                                                                  $status .= implode("<br />\n",$result->getMessages());
                                                                                                                                                  /*
                                                                                                                                                   * Fourth Condition
                                                                                                                                                    * The page is called and the user has not yet logged in.  This just lets
                                                                                                                                                     * them know.
                                                                                                                                                      */
                                                                                                                                                      } else {
                                                                                                                                                          $status = "You are not logged in.<br />\n";
                                                                                                                                                          }
                                                                                                                                                          ?>
                                                                                                                                                          <html><body>
                                                                                                                                                          <script>
                                                                                                                                                          <?php echo "$status";?>
                                                                                                                                                          <form method="post"><fieldset>
                                                                                                                                                          <legend>OpenID Login</legend>
                                                                                                                                                          <input type="text" name="openid_identifier" value="">
                                                                                                                                                          <input type="submit" name="openid_action" value="Login">
                                                                                                                                                          <input type="submit" name="openid_action" value="Logout">
                                                                                                                                                          <input type="submit" name="openid_action" value="Test">
                                                                                                                                                          </fieldset></form>
                                                                                                                                                          
                                                                                                                                                          </body></html>
                                                                                                            