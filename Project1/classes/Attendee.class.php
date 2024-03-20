<?php

class Attendee {
    
    private $idattendee;
    private $name;
    private $role; 

	public function getHTML($firstFilePath = ".") {
		
		require_once "$firstFilePath/utils/DB.class.php";
		require_once "$firstFilePath/utils/CurrentUser.class.php";

		$db = new DB();
		$role = $db->associativeSelect("SELECT name FROM role WHERE idrole = :id", [":id"=>$this->role])[0]['name'];

        // get the unchangeable attendee 
        $superAdminID = $db->associativeSelect("SELECT AdminId FROM super_admin", [])[0]['AdminId'];
		if ($superAdminID == $this->idattendee) {
			$role = "super admin";
		}

		$html = <<< END

			<p class="user-row-link">
				<div class="user-row">
					<h2>{$this->name}</h2>
					<p>$role</p>
				</div>
			</p>

		END;

		// get the user role to see if it should be a link
		$userRole = CurrentUser::getCurrentUser()->getRole();
		if ($superAdminID != $this->idattendee && $userRole == '1') {
			$html = "<a class=\"user-row-link\" href=\"editpages/editAttendee.php?id={$this->idattendee}\">$html</a>";
		}


		return $html;
	}

    // getters and setters
    public function getIdattendee(){
		return $this->idattendee;
	}

	public function setIdattendee($idattendee){
		$this->idattendee = $idattendee;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getRole(){
		return $this->role;
	}

	public function setRole($role){
		$this->role = $role;
	}
}
