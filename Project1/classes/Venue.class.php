<?php

class Venue {

    private $idvenue;
    private $name;
    private $capacity;

    function getHTML() {

        $venueSection = <<< END

            <p>{$this->name} <br /><span class="capacity">Capacity: {$this->capacity}</span></p>

        END;

        return $venueSection;
    }

    // getters and setters
    public function getIdvenue(){
		return $this->idvenue;
	}

	public function setIdvenue($idvenue){
		$this->idvenue = $idvenue;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getCapacity(){
		return $this->capacity;
	}

	public function setCapacity($capacity){
		$this->capacity = $capacity;
	}


 }