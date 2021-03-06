<?php

require_once "../../../src/controller/authController.php";

if (!isAdmin() || !isset($_POST["id"])) {
    exit();
}


require_once "../../../src/util/db.php";

$sql = "
select seller_candidacy.*, user.username from seller_candidacy
inner join user on user_from = user_mail
where id_candidacy = :id
";

$candidacies = prepare($sql, ["id" => $_POST["id"]]);

echo utf8_encode(json_encode($candidacies));
