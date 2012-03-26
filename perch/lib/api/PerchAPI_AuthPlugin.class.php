<?php

class PerchAPI_AuthPlugin extends PerchBase
{
	protected $table = 'YOUR_USER_TABLE';
    protected $pk = 'userID';
    
    private $logged_in = false;

	public $activation_failed = false;
	
    public function log_user_in($username, $password)
	{
		die('Auth plugin '. PERCH_AUTH_PLUGIN .' needs to implement method log_user_in($username, $password)');
		return false;
	}
	
	
    public function resume_session()
	{
		die('Auth plugin '. PERCH_AUTH_PLUGIN .' needs to implement method resume_session()');
		return false;
	}
	
	public function authenticate($username, $password)
	{
		$user_details = $this->log_user_in($username, $password);
		
		if (is_array($user_details)) {
			$this->logged_in = true;
			$details = array();
			$details['userID']    = $user_details['email'];
			$details['userEmail'] = $user_details['email'];
			$details['userRole']  = $user_details['role'];
			$this->set_details($details);
			
			// activate
			$AuthenticatedUser = new PerchAuthenticatedUser(array());
			if ($AuthenticatedUser->activate_from_plugin()) {
				return true;
			}else{
				$this->activation_failed = true;
			}
		}
		
		$this->logged_in = false;
		return false;
	}
	
	public function recover()
	{
		$user_details = $this->resume_session();
		
		if (is_array($user_details)) {
			$this->logged_in = true;
			$details = array();
			$details['userID']    = $user_details['email'];
			$details['userEmail'] = $user_details['email'];
			$details['userRole']  = $user_details['role'];
			$this->set_details($details);
			return true;
		}
		
		$this->logged_in = false;
		return false;
	}
	
    public function logged_in()
	{
		return $this->logged_in;
	}
	
	public function logout()
	{
		$this->logged_in = false;
		$this->log_user_out();
		
		return true;
	}
	
	public function id()
    {
        return $this->details['userEmail'];
    }
}

?>