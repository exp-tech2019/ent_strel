SELECT tWithDoors.*, tWithDoors.Name AS CustomerName, DATE_FORMAT(tWithDoors.DateCreate , '%d.%m.%Y') AS DateCreateS, DATE_FORMAT(tWithDoors.ShetDate, '%d.%m.%Y') AS ShetDateS, SUM(IFNULL(tWithDoors.DoorCountOne,0)) AS DoorCount, SUM(IFNULL(calc.Sum,0)) AS PaymentSum FROM
	(
		SELECT tMain.*, od.id AS idDoor, od.Count AS DoorCountOne, l.FIO AS Manager FROM 
		(
			SELECT tOrder.*, SUM(IFNULL(p.SumPayment, 0)) FROM
			(
				SELECT o.*, c.Name FROM TempOrders o, Customers c WHERE o.idCustomer=c.id
			) tOrder
			LEFT JOIN temporderpayments p
			ON tOrder.id=p.idOrder
			GROUP BY tOrder.id
		) tMain, temporderdoors od, logins l
		WHERE tMain.id=od.idOrder AND tMain.idManager=l.id
    ) tWithDoors
	LEFT JOIN temporderdoorcalc calc
	ON tWithDoors.idDoor=calc.idDoor
	GROUP BY tWithDoors.id