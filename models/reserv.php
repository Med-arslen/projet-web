<?php
class Reservation {
    private ?int $id_reservation;
    private ?int $id_client;
    private ?int $id_event;
    private ?int $nb_places;
    private ?DateTime $date_reservation;
    private ?string $state;
    private ?string $type;
    private ?float $price;

    // Constructor
    public function __construct(
        ?int $id_reservation,
        ?int $id_client,
        ?int $id_event,
        ?int $nb_places,
        ?DateTime $date_reservation,
        ?string $state,
        ?string $type,
        ?float $price
    ) {
        $this->id_reservation = $id_reservation;
        $this->id_client = $id_client;
        $this->id_event = $id_event;
        $this->nb_places = $nb_places;
        $this->date_reservation = $date_reservation;
        $this->state = $state;
        $this->type = $type;
        $this->price = $price;
    }

    // Getters and Setters
    public function getIdReservation(): ?int {
        return $this->id_reservation;
    }

    public function setIdReservation(?int $id_reservation): void {
        $this->id_reservation = $id_reservation;
    }

    public function getIdClient(): ?int {
        return $this->id_client;
    }

    public function setIdClient(?int $id_client): void {
        $this->id_client = $id_client;
    }

    public function getIdEvent(): ?int {
        return $this->id_event;
    }

    public function setIdEvent(?int $id_event): void {
        $this->id_event = $id_event;
    }

    public function getNbPlaces(): ?int {
        return $this->nb_places;
    }

    public function setNbPlaces(?int $nb_places): void {
        $this->nb_places = $nb_places;
    }

    public function getDateReservation(): ?DateTime {
        return $this->date_reservation;
    }

    public function setDateReservation(?DateTime $date_reservation): void {
        $this->date_reservation = $date_reservation;
    }

    public function getState(): ?string {
        return $this->state;
    }

    public function setState(?string $state): void {
        $this->state = $state;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type): void {
        $this->type = $type;
    }

    public function getPrice(): ?float {
        return $this->price;
    }

    public function setPrice(?float $price): void {
        $this->price = $price;
    }
}
?>
