<?php

class example_auth_plugin extends PerchAPI_AuthPlugin
{

    public function log_user_in($username, $password)
    {

		// Check the username and password e.g. against your database table
        $sql = 'SELECT userID, userEmail FROM my_user_table
                WHERE user_login='.$this->db->pdb($username).' AND user_password='.$this->db->pdb($password).'
                LIMIT 1';
        $row = $this->db->get_row($sql);
        
		// If it exists...
        if (is_array($row)) {
			// Set something so you can resume the session on each page load.
			PerchSession::set('email', $row['userEmail']);
			PerchSession::set('role', 'Admin');

			// Return an array with 'email' and 'role' items. Role should be Admin or Editor.
			return array(
				'email'=>$row['userEmail'],
				'role'=>'Admin'  // Admin or Editor
			);
        }
        
		// Login failed, so return false.
        return false;
    }
    
    public function resume_session()
    {
		// If login has been successful and userID is set in the session
        if (PerchSession::is_set('email')) {
                return array(
					'email'=>PerchSession::get('email'),
					'role'=>PerchSession::get('role')
				);
            }
        }
        return false;
    }
    
    public function log_user_out()
    {
        PerchSession::delete('email');
        PerchSession::delete('role');
        return true;
    }
    
}



?>