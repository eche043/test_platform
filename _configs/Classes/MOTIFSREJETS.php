<?php

class MOTIFSREJETS extends BDD
{
    public function liste(){
        $a = $this->bdd->prepare("SELECT * FROM REF_FS_MOTIFS_REJETS WHERE DATE_FIN_VALIDITE IS NULL");
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }
}