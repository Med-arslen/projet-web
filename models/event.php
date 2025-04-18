<?php
class Event {
    private $id_event;
    private $name_event;
    private $location;
    private $total_places;
    private $price_event;
    private $description;
    private $id_film;
    private $date_event;


    public function __construct($id_event, $name_event, $location, $total_places, $price_event, $description, $id_film, $date_event) {
        $this->id_event = $id_event;
        $this->name_event = $name_event;
        $this->location = $location;
        $this->total_places = $total_places;
        $this->price_event = $price_event;
        $this->description = $description;
        $this->id_film = $id_film;
        $this->date_event = $date_event;
    }

    public function getNameEvent() {
        return $this->name_event;
    }

    public function getLocation() {
        return $this->location;
    }

    public function getTotalPlaces() {
        return $this->total_places;
    }

    public function getPriceEvent() {
        return $this->price_event;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getIdFilm() {
        return $this->id_film;
    }

    public function getDateEvent() {
        return $this->date_event;
    }
}


?>
