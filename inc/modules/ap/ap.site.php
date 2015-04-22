<?php

		//Make sure the file isn't accessed directly
		defined('IN_LCMS') or exit('Access denied!');

		//Ładowanie funkcji -  NIE KASOWAC , NIE EDYTOWAC, NIE RUSZAC! DONT DELETE , DONT EDIT, DONT  TOUCH!
                require(LANG.'ap.site.php'); 
                require(MODULES.'ap/ap.functions.php');
                
		//Date Poland
                date_default_timezone_set('Europe/Warsaw');

		//Replace pattern by function
		$core->replace('{{ap.register}}', register());
                $core->replace('{{ap.login}}', login());
                $core->replace('{{ap.editprofile}}', editprofile());
                $core->replace('{{ap.profile}}', profile());
                $core->replace('{{ap.userpanel}}', userpanel());
                $core->replace('{{ap.todaylogin}}', todaylogin());
                $core->replace('{{ap.onlinenow}}', onlinenow());
                $core->replace('{{page.contentWithReg}}', regcontent());
                $core->replace('{{page.titleWithReg}}',regtitle());
                $core->pattern = registercontent($core->pattern);
                
                
                
                

                
                
                // Zamieniamy tytuł strony szablonu w zaleznosci wybranej zakładki
                function regtitle()
                {
                    if(isset($_GET['account']))
                  {
                  if($_GET['account'] == 'profile')
                      $title = 'Profil';
                  if($_GET['account'] == 'editprofile')
                      $title = 'Edycja Profilu';
                  if($_GET['account'] == 'register')
                      $title = 'Rejestracja';
                  if($_GET['account'] == 'login')
                      $title = 'Logowanie'; 
                  if($_GET['account'] == 'todaylogin')
                      $title = 'Lista dzisiaj logowanych użytkowników';
                  if($_GET['account'] == 'onlinenow')
                      $title = 'Obecnie Zalogowani';
                    }
                    else
                    {
                        $title = '{{page.title}}';
                    }
                    return $title;
                }
                  // Zamieniamy zawartosc content szablonu w zaleznosci wybranej zakładki
                function regcontent()
                {
                  global $db,$core,$lang;
                  $user_data = get_user_data();
                  
                  if(isset($_GET['account']))
                  {
                  if($_GET['account'] == 'profile')
                      $content = '{{ap.profile}}';
                  if($_GET['account'] == 'editprofile')
                      $content = '{{ap.editprofile}}';
                  if($_GET['account'] == 'register')
                      $content = '{{ap.register}}';
                  if($_GET['account'] == 'login')
                      $content = '{{ap.login}}';
                  if($_GET['account'] == 'todaylogin')
                      $content = '{{ap.todaylogin}}';
                  if($_GET['account'] == 'onlinenow')
                      $content = '{{ap.onlinenow}}';
                  if($_GET['account'] == 'logout'){ 
                      $content = update_after();
                      $_SESSION['logged'] = false;
                      $_SESSION['user_id'] = -1;
                      header('Location: index.php');
                        }
                  }
                else {
                      $content = '{{page.content}}';
                    }
                    return  $content;
                } // END NEW CONTENT
		


                
                
                //Your functions --------------------------------------
                // REGISTER CONTENT, THIS FUNCTION IS CHANGING THE CONTENT!
                function registercontent($input){
		global $core, $db, $lang;
                $replace = null;
		if(is_array($input))
		$input = $input[1];
                $input = preg_replace("{{{page.content}}}","{{page.contentWithReg}}", $input);
                $input = preg_replace("{{{page.title}}}","{{page.titleWithReg}}", $input);
		return $input;
                
                }// END REGISTER CONTENT CHANGE


		function register() {
			global $lang,$db,$core;
                    $show = null;
                    // sprawdzamy czy user nie jest przypadkiem zalogowany
                    if(!$_SESSION['logged']) {
                    // jeśli zostanie naciśnięty przycisk "Zarejestruj"
                    if(isset($_POST['regname'])) {
                        // filtrujemy dane...
                        $_POST['regname'] = clear($_POST['regname']);
                        $_POST['regpassword'] = clear($_POST['regpassword']);
                        $_POST['regpassword2'] = clear($_POST['regpassword2']);
                        $_POST['regemail'] = clear($_POST['regemail']);

                        // sprawdzamy czy wszystkie pola zostały wypełnione
                        if(empty($_POST['regname']) || empty($_POST['regpassword']) || empty($_POST['regpassword2']) || empty($_POST['regemail'])) {
                            $show .= $lang['ap']['mustAllFields'];
                        // sprawdzamy czy podane dwa hasła są takie same
                        } elseif($_POST['regpassword'] != $_POST['regpassword2']) {
                            $show .= $lang['ap']['mustPasswordsSame'];
                        // sprawdzamy poprawność emaila
                        } elseif(filter_var($_POST['regemail'], FILTER_VALIDATE_EMAIL) === false) {
                            $show .= $lang['ap']['emailIncorrect'];
                        } else {
                            // sprawdzamy czy są jacyś uzytkownicy z takim loginem lub adresem email
                            $check_name = $db->select('accounts',array('name'=>$_POST['regname']));
                            $check_email = $db->select('accounts',array('email'=>$_POST['regemail']));
                            if($check_name[0]['name'] == $_POST['regname'] || $check_email[0]['email'] == $_POST['regemail']) {
                                $show .= $lang['ap']['usserThere'];
                            } else {
                                // jeśli nie istnieje to kodujemy haslo...
                                $_POST['regpassword'] = codepass($_POST['regpassword']);
                                // i wykonujemy zapytanie na dodanie usera
                                 $newRecord=$db->insert('accounts',array(null,$_POST['regname'],$_POST['regpassword'],$_POST['regemail'],date("d-m-Y"),'Brak','Brak','0',date("d-m-Y"),time(),'0','0'));
                                $show .= $lang['ap']['registerComplete'];

                            }
                        }
                    }

                    // wyświetlamy formularz
                    $show .= '<form method="post" action="index.php?account=register">

                            <label> Login </label>
                            <input type="text" value="'.$_POST['regname'].'" name="regname">

                            <label>Hasło </label>
                            <input type="password" value="'.$_POST['regpassword'].'" name="regpassword">

                            <label> Powtórz hasło </label>
                            <input type="password" value="'.$_POST['regpassword2'].'" name="regpassword2">

                            <label> E-Mail</label>
                            <input type="text" value="'.$_POST['regemail'].'" name="regemail">

                            <input type="submit" value="'.$lang['ap']['submitRegister'].'">

                    </form>';
                } else {
                    $show .= $lang['ap']['isLoggedCantRegister'];
                }
                return $show;
		}
                
                
                
                
                // Logowanie
                function login(){
                    global $db,$core,$lang;
                    $show  = null;
                    $user_data = get_user_data();
                    
                    // sprawdzamy czy user nie jest przypadkiem zalogowany
                    if(!$_SESSION['logged']) {
                    // jeśli zostanie naciśnięty przycisk "Zaloguj"
                    if(isset($_POST['name'])) {
                        // filtrujemy dane...
                        $_POST['name'] = clear($_POST['name']);
                        $_POST['password'] = clear($_POST['password']);
                        // i kodujemy hasło
                        $_POST['password'] = codepass($_POST['password']);

                        // sprawdzamy prostym zapytaniem sql czy podane dane są prawidłowe
                        $check_name = $db->select('accounts',array('name'=>$_POST['name']));
                        $check_password = $db->select('accounts',array('password'=>$_POST['password']));;
                          if($check_name[0]['name'] == $_POST['name'] && $check_password[0]['password'] == $_POST['password']) {
                            // jeśli tak to ustawiamy sesje "logged" na true oraz do sesji "user_id" wstawiamy id usera
                            $_SESSION['logged'] = true;
                            $_SESSION['user_id'] = $check_name[0]['id'];
                            $db->update('accounts',array('id'=>$check_name[0]['id']),array($check_name[0]['id'],$check_name[0]['name'],$check_name[0]['password'],$check_name[0]['email'],$check_name[0]['regdate'],$check_name[0]['from'],$check_name[0]['website'],$check_name[0]['range'],date("d-m-Y"),time(),'1','0'));
                            $show .= $lang['ap']['loginComplete'];
                            $_SESSION['lastactivity'] = time();
                            Header('Refresh:1.5;url=index.php?account=profile');

                          } else {
                            $show .= $lang['ap']['loginOrEmailIncorrect'];
                        }
                    }
 
                    // wyświetlamy komunikat na zalogowanie się
                    $show .= '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
                             <label> Login </label>
                            <input type="text" value="'.$_POST['name'].'" name="name">
                              <label> Hasło </label>
                            <input type="password" value="'.$_POST['password'].'" name="password">

                            <input type="submit" value="'.$lang['ap']['submitLogin'].'">

                    </form>';
                } else {
                $show .= $lang['ap']['isLogged'];
                }
                                    return $show;
                }
                
                
                
                
                // WYSWIETLANIE PROFILU
                
                function profile(){
                    global $db,$lang,$core;
                    $show = null;
                    
                    if(isset($_GET['id']))
                    $user_data = get_user_data($_GET['id']);
                    else
                    $user_data = get_user_data();
                    
                       $show .='<p>'.$lang['ap']['profileName'].$user_data['name'].'</p>';
                       $show .='<p>'.$lang['ap']['profileEmail'].$user_data['email'].'</p>';
                       $show .='<p>'.$lang['ap']['profileLastLogout'].$user_data['lastlogoutdate'].'</p>';
                       $show .='<p>'.$lang['ap']['profileFrom'].$user_data['from'].'</p>';
                       $show .='<p>'.$lang['ap']['profileWWW'].$user_data['website'].'</p>';
                       $show .='<p>'.$lang['ap']['profileJoined'].$user_data['regdate'].'</p>';
                       $show .='<p>'.$lang['ap']['profileStatus'];
                       if($user_data['online'] == '0')
                           $show .= "<font color=red>OFFINE</font>";
                       elseif($user_data['online'] == '1')
                           $show .="<font color=green>ONLINE</font>";
                       $show .= '</p>';
                       
                    
                    
                    return $show;                     
                }
                
                
                //EDYCJA PROFILU
                function editprofile()
                {
                    global $db,$core,$lang;
                    
 
                    $show = null;
                    
                    
                    if($_SESSION['logged'] == true){
                    $user_data = get_user_data();
 
                    // jeśli zostanie naciśnięty przycisk "Edytuj profil"
                    if(isset($_POST['edit'])) {
                    

                              
                        // filtrujemy dane
                      $_POST['website'] = clear($_POST['website']);
                      $_POST['from'] = clear($_POST['from']);
                      $_POST['new_password'] = clear($_POST['new_password']);
                      $_POST['new_password2'] = clear($_POST['new_password2']);
                      $_POST['password'] = clear($_POST['password']);
                      $_POST['email'] = clear($_POST['email']);
 

                      
                      if($_POST['new_password'] != $_POST['new_password2'])
                      {
                          $show .= $lang['ap']['mustPasswordsSame'];
                          $_POST['new_password'] = $user_data['password'];       
                      }
                      else
                      {
                          $_POST['new_password'] = codepass($_POST['new_password']);
                      }

                      if(empty($_POST['website']))
                      {
                          $_POST['website'] = 'Brak';
                      }
                      elseif(empty($_POST['from']))
                      {
                          $_POST['from'] = 'Brak';
                      }
                      elseif(empty($_POST['email']))
                      {
                          $_POST['email'] = $user_data['email'];
                      }
                      elseif(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false && !empty($_POST['email']))
                      {
                          $show .=$lang['ap']['emailIncorrect'];
                          $_POST['email'] = $user_data['email'];
                      }
                      if($_POST['email'] != $user_data['email'])
                      {
                          $select = $db->select('accounts',array('email'=>$_POST['email']));
                          if(count($select) > 0 && $select[0]['name'] != $user_data['name'])
                          {
                              $show .= '<p> Istnieje już taki e-mail w bazie!<br />'
                                      . 'Czy to nie twoje inne konto? <a href="index.php?account=profile&id='.$select[0]['id'].'"><b>'.$select[0]['name'].'</b></a>';
                                      $_POST['email'] = $user_data['email'];
                          }
                      }
                      
                                  $result = $db->update('accounts',array('id'=>$user_data['id']),array($user_data['id'],$user_data['name'],$_POST['new_password'],$_POST['email'],$user_data['regdate'],$_POST['from'],$_POST['website'],$user_data['range'],$user_data['lastlogoutdate'],$user_data['lastlogouttime'],$user_data['online'],'0'));     
                      
                               if($result)
                                    {
                                        $show .= $lang['ap']['editCorrectSuccessfull'];
                                        $user_data = get_user_data();
                                    } 
                                    else 
                                    {
                                        $show .= $lang['ap']['editError'];
                                    }
                      
                      }// end isset $_POST['edit'];
                      

                                    

                           

                            // wyświetlamy prosty formularz
                            $show .= '<form method="post" action="index.php?account=editprofile">
                                    <label> '.$lang['ap']['editLabelLogin'].' </label>
                                    <input type="text" value="'.$user_data['name'].'" disabled="true">
                                    <label> '.$lang['ap']['editLabelWWW'].' </label>
                                    <input type="text" value="'.$user_data['website'].'" name="website">
                                    <label>'.$lang['ap']['editLabelFrom'].'</label>
                                    <input type="text" value="'.$user_data['from'].'" name="from">
                                    <label> '.$lang['ap']['editLabelPassword'].' </label>
                                    <input type="password" value="" name="new_password" autocomplete="off">
                                      <label> '.$lang['ap']['editLabelRetryPassword'].' </label>
                                    <input type="password" value="" name="new_password2" autocomplete="off">
                                     <label>'.$lang['ap']['editLabelEmail'].'</label>
                                    <input type="text" value="'.$user_data['email'].'" name="email">

                                    <input type="submit" value="'.$lang['ap']['submitEditprofile'].'" name="edit">

                            </form>';
                                                }
                                                else
                                                {
                                                    $show = loginToSee();
                                                }

                            return $show;
                                            }

                                            // Nawigacja Użytkownika
                                            function userpanel(){
                                                global $core,$db,$lang;
                            if($_SESSION['logged'] == true)
                            {

                            // pobieramy dane usera
                            $user_data = get_user_data();

                            $show .= $lang['ap']['navWelcome'].'<b>'.$user_data['name'].'</b>!'
                                    . '<li><a href="index.php?account=profile">'.$lang['ap']['navProfile'].'</a></li>'
                                    . '<li><a href="index.php?account=editprofile">'.$lang['ap']['navEditProfile'].'</a></li>'
                                    . '<li><a href="index.php?account=todaylogin">'.$lang['ap']['navTodayLogin'].'</a></li>'
                                    . '<li><a href="index.php?account=onlinenow">'.$lang['ap']['navOnlineNow'].'</a></li>'
                                    . '<li><a href="index.php?account=logout">'.$lang['ap']['navLogout'].'</a></li>';


                            }
                            else
                            {
                                $show .= '<li><a href="index.php?account=register">'.$lang['ap']['navRegister'].'</a></li>'
                                         .'<li><a href="index.php?account=login">'.$lang['ap']['navLogin'].'</a></li>';
                            }
                        return $show;
                }

                
                function todaylogin(){
                    global $db,$lang,$core;
                    $show = null;
                    
                     if($_SESSION['logged']) {
                    $select = $db->select('accounts',array('lastlogoutdate' => date("d-m-Y")));
                    
                    foreach($select as $logins)
                    {
                        $show .= '<a href="index.php?account=profile&id='.$logins['id'].'">'.$logins['name'].'</a><br />';
                    }
                     }
                        else 
                        {
                            $show = loginToSee();
                        }
                   
                   
                    return $show;
                }
                
                function onlinenow()
                {
                    global $db,$lang;
                   if($_SESSION['logged']) {
                    $select = $db->select('accounts',array('online' => '1'));
                    foreach($select as $logins)
                    {
                        $show .= '<a href="index.php?account=profile&id='.$logins['id'].'">'.$logins['name'].'</a><br />';
                    }
                     }
                        else
                                           
                        {
                            $show = loginToSee();
                        }
                   
                   
                    return $show;
                }
                
                
                
                
                
          
?>
