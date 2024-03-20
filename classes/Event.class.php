<?php

class Event {

    private $idevent;
    private $name;
    private $datestart;
    private $dateend;
    private $numberallowed;
    private $venue;

    function getHTML(bool $includeEdit = true) {

        spl_autoload_register(function($class){
            require_once("./../utils/$class.class.php");
        });

        // get the current user
        $user = (CurrentUser::getCurrentUser());

        // get dates formated
        $start = (Utils::formatDate($this->datestart));
        $end = (Utils::formatDate($this->dateend));

        // get the venue and session data
        $venue = $this->getVenueBlock();
        $sessions = $this->getSessions();

        // get html for all sessions
        $allSessions = "";
        foreach ($sessions as $key => $value) {
            $allSessions = $allSessions . $value->getHTML($includeEdit);
        }
        
        // get the edit button and see if it should be added
        $editButton = self::getEditButton($includeEdit, $user, $this->idevent);

        // get the proper register/unregister button
        $registerButton = (Utils::getRegisterButton(true, $this->idevent));

        // display users
        $userList = self::displayUsers(true, $this->idevent);


        $eventSection = <<< END

        <section>
            <h2>{$this->name}$editButton</h2>
            <p class="date">$start - $end</p>
            {$venue[0]->getHTML()}
            <p>$userList</p>
            <br>
            $registerButton

            <div class="sessions-block">
                $allSessions
            </div>
            <hr>
        </section>

        END;

        return $eventSection;
    }

    private function getVenueBlock() {

        include_once "./utils/DB.class.php";

        $db = new DB();
        $venue = $db->objectSelect("SELECT * FROM venue WHERE idvenue = :id", [":id"=>$this->venue], "Venue");

        return $venue;
    }

    private function getSessions() {

        include_once "./utils/DB.class.php";

        $db = new DB();
        $sessions = $db->objectSelect("SELECT * FROM session WHERE event = :id", [":id"=>$this->idevent], "Session");

        return $sessions;
    }

    // get the edit button and see if it should be added
    public static function getEditButton($includeEdit, $user, $idevent, $sessionId = "-1") {

        if ($sessionId == "-1") {
            $editButton = "<a href=\"editpages/editEvent.php?id=$idevent\"><span class=\"material-symbols-outlined\">edit</span></a>";
        } else {
            $editButton = "<a href=\"editpages/editSession.php?id=$sessionId\"><span class=\"material-symbols-outlined\">edit</span></a>";
        }
        
        // if user is an event manager, check to see if this event is under their management
        if ($user->getRole() == '2') {
            include_once "./utils/DB.class.php";

            $db = new DB();
            $event = $db->associativeSelect("SELECT * FROM manager_event WHERE event = :id AND manager = :manager ", [":id"=>$idevent, ":manager"=>$user->getIdattendee()]);


            // if it is their event, return the edit button
            if (!$includeEdit || count($event) < 1) {
                return "";
            } else {
                return $editButton;
            }
        }

        // if no button requested or user is an attendee, show no edit button
        if (!$includeEdit || $user->getRole() == '3') {
            return $editButton = "";
        }
        return $editButton;
    }

    public static function displayUsers($isEvent, $id) {

        // create db variable
        include_once "./utils/DB.class.php";
        $db = new DB();

        // find out if the user list is editable
        $canEditUsers = false;

         // get the current user's id
         $currentUser = (CurrentUser::getCurrentUser());
         $currentRole = $currentUser->getRole();

        if ($currentRole == "1") {
            $canEditUsers = true;
        }
        else if ($currentRole == "2") {
            // check if it is their event
            $event = $db->associativeSelect("SELECT * FROM manager_event WHERE event = :id AND manager = :manager ", [":id"=>$id, ":manager"=>$currentUser->getIdattendee()]);
            
            if (count($event) >= 1) {
                // an event came up
                $canEditUsers = true;
            }
        }

        
         // show the users
         $userList = "Users:<br>";

         if ($isEvent) {
             $users = $db->objectSelect("SELECT idattendee, name, password, role FROM attendee INNER JOIN attendee_event ON attendee_event.attendee = attendee.idattendee WHERE attendee_event.event = :id ", [":id"=>$id], "Attendee");
         } else {
             $users = $db->objectSelect("SELECT idattendee, name, password, role FROM attendee INNER JOIN attendee_session ON attendee_session.attendee = attendee.idattendee WHERE attendee_session.session = :id ", [":id"=>$id], "Attendee");
         }
         
         // print the users
         foreach ($users as $key => $user) {
             //var_dump($user);
             if ($canEditUsers) {
                $userList .= "<a href=\"./editpages/registerUser.php?isEvent=$isEvent&isEdit=true&id=$id&idattendee={$user->getIdattendee()}\">{$user->getName()}</a><br>";
             } else {
                $userList .= "{$user->getName()}<br>";
             }
             
         }

        
         $isEventStr = $isEvent ? "true" : "false";

         if ($canEditUsers && count($users) < 1) {
            // no users, can edit
            return "Users: none <br><a href=\"./editpages/registerUser.php?isEvent=$isEventStr&isEdit=false&id=$id\"><span class=\"material-symbols-outlined\">add</span></a>";

         } else if (!$canEditUsers && count($users) < 1) {
            // no users, can not edit
            return "Users: none <br>";

         } else if ($canEditUsers) {
            // yes users, can edit
            return $userList."<br><a href=\"./editpages/registerUser.php?isEvent=$isEventStr&isEdit=false&id=$id\"><span class=\"material-symbols-outlined\">add</span></a>"; 
         } else {
            return $userList; 
         }
    }

    // getters and setters
    public function getIdevent(){
		return $this->idevent;
	}

	public function setIdevent($idevent){
		$this->idevent = $idevent;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getDatestart(){
		return $this->datestart;
	}

	public function setDatestart($datestart){
		$this->datestart = $datestart;
	}

	public function getDateend(){
		return $this->dateend;
	}

	public function setDateend($dateend){
		$this->dateend = $dateend;
	}

	public function getNumberallowed(){
		return $this->numberallowed;
	}

	public function setNumberallowed($numberallowed){
		$this->numberallowed = $numberallowed;
	}

	public function getVenue(){
		return $this->venue;
	}

	public function setVenue($venue){
		$this->venue = $venue;
	}

 }