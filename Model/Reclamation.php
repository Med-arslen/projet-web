<?php

class Reclamation
{
    private $id_rec;
    private $nomprenom;
    private $email;
    private $nomfilm;
    private $type_rec;
    private $detail;

    // Constructeur
    public function __construct($nomprenom, $email, $nomfilm, $type_rec, $detail,$reponse_rec ,$id_rec = null)
    {
        
        $this->nomprenom = $nomprenom;
        $this->email = $email;
        $this->nomfilm = $nomfilm;
        $this->type_rec = $type_rec;
        $this->detail = $detail;
        $this->reponse_rec = $reponse_rec;
        $this->id_rec = $id_rec;
    }

    // Getters et Setters
    public function getIdRec()
    {
        return $this->id_rec;
    }

    public function setIdRec($id_rec)
    {
        $this->id_rec = $id_rec;
    }

    public function getNomPrenom()
    {
        return $this->nomprenom;
    }

    public function setNomPrenom($nomprenom)
    {
        $this->nomprenom = $nomprenom;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getNomFilm()
    {
        return $this->nomfilm;
    }

    public function setNomFilm($nomfilm)
    {
        $this->nomfilm = $nomfilm;
    }

    public function getTypeRec()
    {
        return $this->type_rec;
    }

    public function setTypeRec($type_rec)
    {
        $this->type_rec = $type_rec;
    }

    public function getDetail()
    {
        return $this->detail;
    }

    public function setDetail($detail)
    {
        $this->detail = $detail;
    }
    public function getReponseRec()
    {
        return $this->reponse_rec;  
    }


    public function setReponseRec($reponse_rec)
    {
        $this->reponse_rec = $reponse_rec;
    }
}
   
?>
