<?php
if (!isAdmin()) {
    exit();
}
?>

<?php require_once "../src/view/admin/elements/v_admin_sidebar.php"; ?>

<!-- remove modal -->
<div class="modal fade" id="validModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="modal-text" class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button id="btn-delete-modal" type="button" class="btn btn-danger" data-bs-dismiss="modal">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- update modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modal-update-text" &class="modal-title">Modification du rôle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="modal-text" class="modal-body">
                <!-- warning -->
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Attention !</strong> Enlever le rôle vendeur à un utilisateur, supprimera toutes ses annonces.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="form-check form-switch">
                    <input id="sellerCheck" class="form-check-input" type="checkbox">
                    <label class="form-check-label" for="sellerCheck">Vendeur</label>
                </div>
                <div class="form-check form-switch">
                    <input id="adminCheck" class="form-check-input" type="checkbox">
                    <label class="form-check-label" for="adminCheck">Admin</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button id="btn-confirm-update-modal" type="button" class="btn btn-success" data-bs-dismiss="modal">Confirmer</button>
            </div>
        </div>
    </div>
</div>



<!-- Form -->
<h3 class="text-center my-3">Gestion des utilisateurs</h3>
<div class="container my-4">
    <div class="row">
        <form id="form">
            <div class="col-xs-8 col-xs-offset-2">
                <div class="input-group">
                    <input id="q" type="text" class="form-control" name="q" placeholder="Rechercher...">
                    <div class="input-group-btn search-panel">
                        <select id="role" name="role" class="form-select" aria-label="Default select example">
                            <option selected value="all">Tous</option>
                            <option value="user">Utilisateurs</option>
                            <option value="seller">Vendeurs</option>
                            <option value="admin">Admins</option>
                        </select>
                    </div>
                    <button id="searchBtn" type="submit" class="btn btn-secondary btn-search"><span class="glyphicon glyphicon-search">&nbsp;</span> <span class="label-icon">Rechercher</span></button>

                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users table -->
<div class="container">
    <div class="row">
        <div class="main-box no-header clearfix">
            <div class="main-box-body clearfix">
                <div class="table-responsive">
                    <table class="table user-list">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Creation</th>
                                <th>Rôle</th>
                                <th>Email</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody id="user-container">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once "../src/view/admin/elements/v_admin_ender.php"; ?>


<script>
    $(document).ready(function() {
        actuUser();
    });

    function actuUser() {
        const q = $("#q").val();
        const role = $("#role").val();

        $.ajax({
            type: "POST",
            url: "./model/admin/model_user.php",
            dataType: "json",
            data: {
                q: q,
                role: role
            },
            success: function(datas) {
                displayUser(datas);
            }
        });

    }

    function displayUser(datas) {
        $("#user-container").html("");

        for (var data of datas) {
            var role = "Utilisateur";
            if (data.isAdmin === "1" && data.isSeller === "0") {
                role = "Admin";
            } else if (data.isSeller === "1" && data.isAdmin === "0") {
                role = "Vendeur"
            } else if (data.isSeller === "1" && data.isAdmin === "1") {
                role = "Admin et vendeur"
            }

            tr = document.createElement("tr");

            tdUsername = document.createElement("td");
            $(tdUsername).html(data.username);
            tr.append(tdUsername);

            tdDate = document.createElement("td");
            $(tdDate).html(data.date_registration);
            tr.append(tdDate);

            tdRole = document.createElement("td");
            $(tdRole).html(role);
            tr.append(tdRole);

            tdEmail = document.createElement("td");
            $(tdEmail).html(data.user_mail);
            tr.append(tdEmail);

            tdBtnContainer = document.createElement("td");
            $(tdBtnContainer).attr("style", "width:20%");

            spanModif = document.createElement("span");
            $(spanModif).attr("data-bs-toggle", "modal");
            $(spanModif).attr("data-bs-target", "#updateModal");
            $(spanModif).attr("class", "btn-modif table-link text-info fa-stack");
            $(spanModif).html("<i class='fa fa-square fa-stack-2x'></i><i class='fa fa-pencil fa-stack-1x fa-inverse'></i> ");
            $(spanModif).attr("id", data.user_mail);

            tdBtnContainer.append(spanModif);
            spanDelete = document.createElement("span");
            $(spanDelete).attr("data-bs-toggle", "modal");
            $(spanDelete).attr("data-bs-target", "#validModal");
            $(spanDelete).attr("class", "btn-delete table-link text-danger fa-stack");
            $(spanDelete).html("<i class='fa fa-square fa-stack-2x'></i><i class='fa fa-trash-o fa-stack-1x fa-inverse'></i> ");
            $(spanDelete).attr("id", data.user_mail);

            tdBtnContainer.append(spanDelete);
            tr.append(tdBtnContainer);
            $("#user-container").append(tr);
        }

        $(".btn-modif").click(function() {

            for (data of datas) {
                if (data['user_mail'] === this.id) {
                    $("#sellerCheck").prop("checked", false);
                    $("#adminCheck").prop("checked", false);
                    if (data['isAdmin'] === "1") {
                        $("#adminCheck").prop("checked", true);
                    }
                    if (data['isSeller'] === "1") {
                        $("#sellerCheck").prop("checked", true);
                    }
                    break;
                }
            }
            $("#modal-update-text").html("Modification des rôles de " + this.id);
            $("#btn-confirm-update-modal").attr("user", this.id);
        });

        $(".btn-delete").click(function() {
            $("#modal-text").html("Êtes-vous sur de vouloir supprimer l'utilisateur " + this.id + " ?");
            $("#validModal").attr("user", this.id);
        });

        $("#btn-delete-modal").click(function() {
            $.ajax({
                type: "POST",
                url: "./model/admin/model_remove_user.php",
                data: {
                    user: $("#validModal").attr("user")
                },
                success: function() {
                    actuUser();
                }
            });
        });

        $("#btn-confirm-update-modal").click(function() {
            $.ajax({
                type: "POST",
                url: "./model/admin/model_update_user.php",
                data: {
                    user: $("#btn-confirm-update-modal").attr("user"),
                    isAdmin: $("#adminCheck").prop("checked"),
                    isSeller: $("#sellerCheck").prop("checked")
                },
                success: function() {
                    actuUser();
                }
            });
        });

        $("#form").submit(function(e) {
            e.preventDefault();
            actuUser();
        });
    }
</script>

<style>
    .table thead tr th {
        text-transform: uppercase;
        font-size: 0.875em;
    }

    .table thead tr th {
        border-bottom: 2px solid #e7ebee;
    }

    .table tbody tr td {
        font-size: 0.875em;
        vertical-align: middle;
        border-top: 1px solid #e7ebee;
        padding: 12px 8px;
    }

    span {
        cursor: pointer;
    }
</style>