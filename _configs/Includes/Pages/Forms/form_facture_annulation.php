<form id="form_facture_annulation">
    <div class="form-row">
        <div class="form-group col-sm-12">
            <label for="motif_annulation_input">Motif d'annulation</label>
            <textarea aria-label="Motif d'annulation" class="form-control form-control-sm" id="motif_annulation_input" placeholder="Renseignez le motif ici..."></textarea>
        </div>
        <div class="form-group col-sm-12 align_right">
            <a href="<?= URL.'agent/facture-selection-type.php?num='.$facture['FEUILLE'];?>" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-circle-left"></i> Retourner</a>
            <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Valider</button>
        </div>
    </div>
</form>