//Перевод Числового кода ед. измер. в строку
function UnitToString(Unit){
    var UnitS="м<sup>2</sup>";
    switch (parseInt(Unit)){
        case 0:UnitS="м<sup>2</sup>"; break;
        case 1:UnitS="шт"; break;
        case 2:UnitS="кг"; break;
        case 3:UnitS="л"; break;
    };
    return UnitS;
}