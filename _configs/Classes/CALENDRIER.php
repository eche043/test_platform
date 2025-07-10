<?php


class CALENDRIER {
    public function trouver_mois($mois) {
        $json = array(
            '01' => 'JANVIER',
            '02' => 'FEVRIER',
            '03' => 'MARS',
            '04' => 'AVRIL',
            '05' => 'MAI',
            '06' => 'JUIN',
            '07' => 'JUILLET',
            '08' => 'AOUT',
            '09' => 'SEPTEMBRE',
            '10' => 'OCTOBRE',
            '11' => 'NOVEMBRE',
            '12' => 'DECEMBRE'
        );
        return $json[$mois];
    }
    public function trouver_mois_abrege($mois) {
        $json = array(
            '01' => 'JAN',
            '02' => 'FEV',
            '03' => 'MAR',
            '04' => 'AVR',
            '05' => 'MAI',
            '06' => 'JUI',
            '07' => 'JUI',
            '08' => 'AOU',
            '09' => 'SEP',
            '10' => 'OCT',
            '11' => 'NOV',
            '12' => 'DEC'
        );
        return $json[$mois];
    }
}