<?php
require_once dirname(__FILE__) . '/../authentication.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
require_once ('CAS.php');

class CasAuthentication extends Authentication
{
    private $cas_settings;

    function CasAuthentication()
    {
    }

    public function check_login($user, $username, $password = null)
    {
        if (! $this->is_configured())
        {
            Display :: error_message(Translation :: get('CheckCASConfiguration'));
            exit();
        }
        else
        {
            $settings = $this->get_configuration();
            
            // initialize phpCAS
            phpCAS :: setDebug(Path :: get(SYS_PATH) . 'log.log');
            phpCAS :: client(CAS_VERSION_3_0, $settings['host'], (int) $settings['port'], '', true, 'saml');
            
            // SSL validation for the CAS server
            $crt_path = $settings['certificate'];
            phpCAS :: setExtraCurlOption(CURLOPT_SSLVERSION, 3);
            phpCAS :: setCasServerCACert($crt_path);
            //phpCAS :: setNoCasServerValidation();
            

            // force CAS authentication
            phpCAS :: forceAuthentication();
            
            $user_id = phpCAS :: getUser();
            
            $udm = UserDataManager :: get_instance();
            if (! $udm->is_username_available($user_id))
            {
                $user = $udm->retrieve_user_info($user_id);
            }
            else
            {
                $user = $this->register_new_user($user_id);
            }
            
            if (get_class($user) == 'User')
            {
                Session :: register('_uid', $user->get_id());
                Events :: trigger_event('login', 'user', array('server' => $_SERVER, 'user' => $user));
                
                $request_uri = Session :: retrieve('request_uri');
                
                if ($request_uri)
                {
                    $request_uris = explode("/", $request_uri);
                    $request_uri = array_pop($request_uris);
                    header('Location: ' . $request_uri);
                }
                
                $login_page = PlatformSetting :: get('page_after_login');
                if ($login_page == 'weblcms')
                {
                    header('Location: run.php?application=weblcms');
                }
            }
            else
            {
                return false;
            }
        }
    
    }

    public function is_password_changeable()
    {
        return false;
    }

    public function is_username_changeable()
    {
        return false;
    }

    public function can_register_new_user()
    {
        return true;
    }

    public function register_new_user($user_id)
    {
        if (! $this->is_configured())
        {
            Display :: error_message(Translation :: get('CheckCASConfiguration'));
            exit();
        }
        else
        {
            $user_attributes = phpCAS :: getAttributes();
            
            $user = new User();
            $user->set_username($user_id);
            $user->set_password('PLACEHOLDER');
            $user->set_status(5);
            $user->set_auth_source('cas');
            $user->set_platformadmin(0);
            $user->set_language('english');
            $user->set_email($user_attributes['email']);
            $user->set_lastname($user_attributes['last_name']);
            $user->set_firstname($user_attributes['first_name']);
            
            if (! $user->create())
            {
                return false;
            }
            else
            {
                return $user;
            }
        }
    }

    function logout($user)
    {
        if (! $this->is_configured())
        {
            Display :: error_message(Translation :: get('CheckCASConfiguration'));
            exit();
        }
        else
        {
            $settings = $this->get_configuration();
            
            // initialize phpCAS
            phpCAS :: client(CAS_VERSION_2_0, $settings['host'], (int) $settings['port'], '');
            
            // no SSL validation for the CAS server
            phpCAS :: setNoCasServerValidation();
            
            // force CAS authentication
            phpCAS :: forceAuthentication();
            
            // Do the logout
            phpCAS :: logout();
            
            Session :: destroy();
        }
    }

    function get_configuration()
    {
        if (! isset($this->cas_settings))
        {
            $cas = array();
            $cas['host'] = PlatformSetting :: get('cas_host');
            $cas['port'] = PlatformSetting :: get('cas_port');
            $cas['uri'] = PlatformSetting :: get('cas_uri');
            $cas['certificate'] = PlatformSetting :: get('cas_certificate');
            
            $this->cas_settings = $cas;
        }
        
        return $this->cas_settings;
    }

    function is_configured()
    {
        $settings = $this->get_configuration();
        
        foreach ($settings as $setting => $value)
        {
            if ((empty($value) || ! isset($value)) && $setting != 'uri')
            {
                return false;
            }
        }
        
        return true;
    }
}
?>