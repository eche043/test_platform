<?php
    require_once '../../../Classes/UTILISATEURS.php';
    if(isset($_SESSION['ECMU_USER_ID'])) {
    echo '<script>window.location.href="'.URL.'"</script>';
}else {
?>
<div class="col">
    <div class="row justify-content-md-center">
        <div class="col col-sm-3" id="div_login">
            <p class="align_center"><img src="<?= IMAGES.'logo_cnam.png';?>" width="100" alt="LOGO CNAM" /><br /><b class="display-4">AGAC</b><hr /></p>
            <p id="p_resultats" class="align_center"></p>
            <?php //include "Forms/form_connexion.php";?>
            <?php //include "Forms/form_mot_de_passe_oublie.php";?>
            <?php //include "Forms/form_compte.php";?>
            <?php //include "Forms/form_recrutement_agac.php";?>

            <form id="form_connexion_recrutement_agac">
                <div class="form-row">
                    <div class="col-md-12 mb-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-phone"></i> <!-- Icône Font Awesome -->
                            </span>
                            <input type="text" class="form-control form-control-sm" id="numero_telephone_input" maxlength="10" placeholder="Numéro de téléphone AGAC" autocomplete="off" />
                        </div>
                    </div>
                    <div class="div-col-sm-12 mb-4" id="div_code_otp_input">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control" placeholder="-" id="otp_1_input" maxlength="1" />
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" placeholder="-" id="otp_2_input" maxlength="1" />
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" placeholder="-" id="otp_3_input" maxlength="1" />
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" placeholder="-" id="otp_4_input" maxlength="1" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-12" id="div_btn_connexion">
                        <button type="submit" id="button_connexion" class="btn btn-sm btn-info btn-block"><i class="fa fa-exchange-alt"></i> Entrez </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<script>
    $("#div_code_otp_input").hide();
    $("#div_btn_connexion").hide();

    $("#numero_telephone_input").keyup(function () {
        var telephone = $("#numero_telephone_input").val(),
            code_otp = $("#code_otp_input").val();
        if (telephone.length === 10) {
            $("#numero_telephone_input").prop("disabled",true)
            $.ajax({
                url: '../_configs/Includes/Searches/Recrutement/search_numero_telephone.php',
                dataType: 'json',
                type: 'POST',
                data: {
                    'telephone': telephone
                },
                success: function (data) {
                    $("#numero_telephone_input").prop("disabled",true)
                    if (data['status'] === "success") {
                        $("#p_resultats").removeClass('alert alert-danger')
                                         .addClass('alert alert-success');
                        $("#p_resultats").html(data['message'] + '<br />' );
                        //$("#div_code_otp_input").show();
                        $("#div_btn_connexion").show();
                    }else{
                        $("#numero_telephone_input").prop("disabled",false)
                        $("#p_resultats").removeClass('alert alert-success')
                                         .addClass('alert alert-danger');
                        $("#p_resultats").html(data['message'] + '<br />' );
                        setTimeout(function(){
                            window.location.reload();
                        }, 3000);
                    }
                }
            });
            return false;
        } else {
            $("#div_code_otp_input").hide();
            $("#div_btn_connexion").hide();
        }

        return false;
    });

    $("#otp_2_input").prop('disabled',true);
    $("#otp_3_input").prop('disabled',true);
    $("#otp_4_input").prop('disabled',true);


    $("#button_connexion").click(function(){
        $("#button_connexion").prop("disabled",true);
        var otp_1 = $("#otp_1_input").val(),
            otp_2 = $("#otp_2_input").val(),
            otp_3 = $("#otp_3_input").val(),
            otp_4 = $("#otp_4_input").val(),
            code_otp = otp_1+otp_2+otp_3+otp_4,
            telephone = $("#numero_telephone_input").val();
            console.log(code_otp);
        $("#button_connexion").prop('disabled',true);
        $("#button_connexion").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        $.ajax({
            url: '../_configs/Includes/Submits/Recrutement/submit_connexion_recrutement_agac.php',
            dataType: 'json',
            type: 'POST',
            data: {
                'telephone': telephone,
            },
            success: function (data) {
                $("#button_connexion").prop("disabled",true)
                if (data['status'] === "success") {
                    setTimeout(function(){
                        window.location.href='https://ecmu.ipscnam.ci/recrutement/recrutement.php?id-agac='+data['id_agac'];
                    }, 3000);
                }else{
                    $("#p_resultats").removeClass('alert alert-success')
                        .addClass('alert alert-danger');
                    $("#p_resultats").html(data['message'] + '<br />' );
                    $("#button_connexion").prop('disabled',false);
                    $("#button_connexion").html('<i class="fa fa-exchange-alt"></i> Entrez ');
                }
            }
        });
        return false;
        //return false;
    })
</script>
