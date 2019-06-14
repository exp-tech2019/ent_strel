<?php
    header('Access-Control-Allow-Origin: *');
    header("Content-type: application/json; charset=utf-8");
    $XMLParams=simplexml_load_file("../../params.xml");
    $m=new mysqli($XMLParams->ConnectDB->Host,$XMLParams->ConnectDB->User,$XMLParams->ConnectDB->Pass,$XMLParams->ConnectDB->DB) or die($m->connect_error);

    switch ($_POST["Action"]){
        case "ActLoad":
            $idOrder=$_POST["idOrder"];
            $d=$m->query("SELECT * FROM
	(SELECT od.name, od.H, od.W, od.S, od.SEqual, n.* FROM OrderDoors od, Naryad n WHERE od.id=n.idDoors AND od.idOrder=$idOrder) o
LEFT JOIN
	(SELECT sn.idNaryad FROM st_ActShpt sa, st_Actshptnaryads sn WHERE sa.idOrder=$idOrder AND sa.id=sn.idAct) st
ON o.id=st.idNaryad
WHERE st.IdNaryad IS NULL");
            $arr=array();
            if($d->num_rows>0)
                while($r=$d->fetch_assoc())
                    $arr[]=array(
                        "Name"=>$r["name"],
                        "Size"=>$r["H"]."x".$r["W"].($r["SEqual"]==1 ? "x Равн." : $r["S"]==null ? "" : "x".$r["S"]),
                        "idDoor"=>$r["idDoors"],
                        "idNaryad"=>$r["id"],
                        "NumInOrder"=>$r["NumInOrder"],
                        "NumNaryad"=>$r["Num"].$r["NumPP"]
                    );
            echo json_encode($arr);
            break;

        case "ActCreate":
            session_start();
            $Result=array("Rsult"=>"No");
            $idManagerLogin=$_SESSION["AutorizeLogin"];
            $d=$m->query("SELECT id FROM Logins WHERE Login='$idManagerLogin'");
            if($d->num_rows>0) {
                $r=$d->fetch_assoc();
                $idManager=$r["id"];
                $d->close();
                $idOrder=$_POST["idOrder"];
                $NaryadList = $_POST["NaryadList"];

                //Создадим акт
                $m->autocommit(false);

                $m->query("INSERT INTO st_ActShpt (idOrder, DateCreate, idManager) VALUES($idOrder, NOW(), $idManager)") or die($m->error);
                $idAct=$m->insert_id;
                foreach ($NaryadList as $Naryad){
                    $idNaryad=$Naryad["idNaryad"];
                    $m->query("INSERT INTO st_ActShptNaryads (idAct, idNaryad) VALUES($idAct, $idNaryad)") or die($m->error);
                };
                $m->commit();
                $Result["Result"]="Ok";
            };
            echo json_encode($Result);
            break;
    }
?>