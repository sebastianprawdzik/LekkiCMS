<?php                                 
                                function clear($text) {
                                // jeśli serwer automatycznie dodaje slashe to je usuwamy
                                if(get_magic_quotes_gpc()) {
                                    $text = stripslashes($text);
                                }
                                $text = trim($text); // usuwamy białe znaki na początku i na końcu
                                $text = htmlspecialchars($text); // dezaktywujemy kod html
                                return $text;
                            }
                                // kodowanie hasła dla lepszego bezpieczeństwa
                                function codepass($password) {
                                   
                                    return sha1(md5($password).'#!%Rgd64');
                                }

                                // komnunikat dla niezalogowanych
                                function loginToSee() {
                                    $result = '<p>To jest strefa tylko dla użytkowników.</p>
                                        <p>[<a href="login.php">Logowanie</a>] [<a href="register.php">Zarejestruj się</a>]</p>';
                                    return $result;
                                }

                                // funkcja na pobranie danych usera
                                function get_user_data($user_id = -1) {
                                    // jeśli nie podamy id usera to podstawiamy id aktualnie zalogowanego
                                    global $db, $core;
                                    if($user_id == -1) {
                                        $user_id = $_SESSION['user_id'];
                                    }
                                    $result = $db->select('accounts',array('id'=>$user_id));
                                    if($result == 0) {
                                        return false;
                                    }
                                    return $result[0];
                                }
                                
                                function update_after(){
                                    global $db;
                                    $user_data = get_user_data();
                                $result = $db->update('accounts',array('id'=>$user_data['id']),array($user_data['id'],$user_data['name'],$user_data['password'],$user_data['email'],$user_data['regdate'],$user_data['from'],$user_data['website'],$user_data['range'],date("d-m-Y"),time(),'0','0'));
                                return $result;
                                
                                }



                                 // start sesji
                                session_start();

                                // jeśli nie ma jeszcze sesji "logged" i "user_id" to wypełniamy je domyślnymi danymi
                                if(!isset($_SESSION['logged'])) {
                                    $_SESSION['logged'] = false;
                                    $_SESSION['user_id'] = -1;
                                }
                                // Jeśli jesteśmy zalogowani
                                elseif($_SESSION['logged'] == true && $_SESSION['user_id'] > 0)
                                {
                                    // Sprawdzamy naszą ostatnią aktywnośc i wylogowywujemy bądź zapisujemy nową aktywnosc
                                  if($_SESSION['lastactivity']+15*60 < time())
                                      header('Location: index.php?account=logout');
                                  else 
                                      $_SESSION['lastactivity'] = time();
                                }
                                ?>