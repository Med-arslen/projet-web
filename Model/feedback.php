<?php
class feedback {
    private ?string $id_fed;
    private ?int $id_rec;
    private ?string $analyse;
    private ?string $conseil;
    private ?string $simplicite;  // Nouveau champ pour la simplicité
    private ?string $temps;       // Nouveau champ pour le temps

    public function __construct(
        ?string $id_fed,
        ?int $id_rec,
        ?string $analyse,
        ?string $conseil,
        ?string $simplicite,  // Ajout du paramètre simplicite
        ?string $temps        // Ajout du paramètre temps
    ) {
        $this->id_fed = $id_fed;
        $this->id_rec = $id_rec;
        $this->analyse = $analyse;
        $this->conseil = $conseil;
        $this->simplicite = $simplicite;
        $this->temps = $temps;
    }

    public function getIdFed(): ?string {
        return $this->id_fed;
    }

    public function setIdFed(?string $id_fed): void {
        $this->id_fed = $id_fed;
    }

    public function getIdRec(): ?int {
        return $this->id_rec;
    }

    public function setIdRec(?int $id_rec): void {
        $this->id_rec = $id_rec;
    }

    public function getAnalyse(): ?string {
        return $this->analyse;
    }

    public function setAnalyse(?string $analyse): void {
        $this->analyse = $analyse;
    }

    public function getConseil(): ?string {
        return $this->conseil;
    }

    public function setConseil(?string $conseil): void {
        $this->conseil = $conseil;
    }

    public function getSimplicite(): ?string {  // Méthode pour obtenir la simplicité
        return $this->simplicite;
    }

    public function setSimplicite(?string $simplicite): void {  // Méthode pour définir la simplicité
        $this->simplicite = $simplicite;
    }

    public function getTemps(): ?string {  // Méthode pour obtenir le temps
        return $this->temps;
    }

    public function setTemps(?string $temps): void {  // Méthode pour définir le temps
        $this->temps = $temps;
    }
}
?>
