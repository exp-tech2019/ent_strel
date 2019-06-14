function SimpleTable(Table,Pagination,Columns,PostAction){
    var PageCount=1;
    var PageNum=1;
    var FindValue="";
    this.Create=function () {
        var thead="";
        for(var i in Columns)
            thead=thead+
                "<th ColumnName='"+Columns[i].ColumnName+"' "+(Columns[i].Width!==null ? "style='width:"+Columns[i].Width+"px'" : "")+">"+
                Columns[i].Caption+
                "</th>";
        Table.append("<thead></thead><tbody></tbody>");
        Table.find("thead").append(thead);
        Pagination.find("li[Btn=Back]").click(function(){

        })
    };
    this.Select=function (TypeSelect,FuncSelect, FindValueIN) {
        FindValue=FindValueIN!==undefined ? FindValueIN : FindValue;
        var SelectFunc=this.Select;
        gl.Post(
            PostAction,
            {
                "FindText":FindValue,
                "PageNum":PageNum,
                "FieldCount":gl.FieldOnTable
            },
            function(str){
                var o=JSON.parse(str);
                if(TypeSelect=="Load") {
                    PageNum = 1;
                    PageCount = Math.ceil(o.CountList / gl.FieldOnTable);
                    Pagination.find("li[Btn!=Next][Btn!=Back]").remove();
                    for (let i = 1; i <= PageCount; i++)
                        Pagination.find("li[Btn=Next]").before(
                            "<li PageNum='" + i + "' class='" + (i == PageNum ? "active" : "") + "'><a href='#'>" + i + "</a></li>"
                        );
                    Pagination.find("li[Btn!=Next][Btn!=Back]").click(function(){
                        PageNum=$(this).attr("PageNum");
                        Pagination.find("li").removeClass("active");
                        Pagination.find("li[PageNum="+PageNum+"]").attr("class","active");
                        SelectFunc("",FuncSelect);
                    })
                };
                FuncSelect(o);
            }
        )
    };
    this.NexPage=function (FuncSelect) {
        if(PageNum<PageCount){
            PageNum++;
            Pagination.find("li").removeClass("active");
            Pagination.find("li[PageNum="+PageNum+"]").attr("class","active");
            this.Select("Select",FuncSelect);
        }
    }
    this.BackPage=function (FuncSelect) {
        if(PageNum>1){
            PageNum--;
            Pagination.find("li").removeClass("active");
            Pagination.find("li[PageNum="+PageNum+"]").attr("class","active");
            this.Select("Select",FuncSelect);
        }
    }
}