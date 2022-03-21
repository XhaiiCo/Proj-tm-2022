<?php

require_once "..\util\db.php";

$stmt = getDB()->query("
select sale.*, 
case 
	when car_picture.picture_order = 1 
		then car_picture.picture_name
	else 
		null 
end as picture_name
from sale
left join car_picture using(id_sale);
");
$stmt->execute();

$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo utf8_encode(json_encode($rs));
