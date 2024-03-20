<?php

class Session {

    private $idsession;
    private $name;
    private $numberallowed;
    private $event;
    private $startdate;
    private $enddate;

    function getHTML($includeEdit = true) {

        spl_autoload_register(function($class){
            require_once("./../utils/$class.class.php");
        });

		// get the current user
		$user = (CurrentUser::getCurrentUser());

        // get dates formated
        $start = (Utils::formatDate($this->startdate));
        $end = (Utils::formatDate($this->enddate));

		// get the proper register/unregister button
        $registerButton = (Utils::getRegisterButton(false, $this->idsession));

		$editButton = (Event::getEditButton($includeEdit, $user, $this->event, $this->idsession));
		$userList = (Event::displayUsers(false, $this->idsession));

        $venueSection = <<< END

            <div class="session">
                <p>{$this->name}$editButton</p>
                <p class="date">$start - $end 
                    <br>
                    <span class="capacity">Capacity: {$this->numberallowed}</span>
                    <br>
                </p>
                $userList<br>$registerButton
            </div>        

        END;

        return $venueSection;
    }

    // getters and setters
    public function getIdsession(){
		return $this->idsession;
	}

	public function setIdsession($idsession){
		$this->idsession = $idsession;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getNumberallowed(){
		return $this->numberallowed;
	}

	public function setNumberallowed($numberallowed){
		$this->numberallowed = $numberallowed;
	}

	public function getEvent(){
		return $this->event;
	}

	public function setEvent($event){
		$this->event = $event;
	}

	public function getStartdate(){
		return $this->startdate;
	}

	public function setStartdate($startdate){
		$this->startdate = $startdate;
	}

	public function getEnddate(){
		return $this->enddate;
	}

	public function setEnddate($enddate){
		$this->enddate = $enddate;
	}


 }