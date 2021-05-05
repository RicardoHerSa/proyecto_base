if($(".reset-password").length > 0){
    $("body").addClass("footer-fixed");
}
$(".table tbody tr").each(function(){
    var row = $(this);
    var dataParent = $(this).data("parent-id");
    var id = $(this).data("id");
    $(row).insertAfter($(".table tbody").find("[data-id='" + dataParent + "']"));
});

$(".group-style").each(function(){
    var className = this.className.match(/item-[0-9]+/)[0];
    paddingElement = className.split('-');
    $(this).find("td:first-child").css( "padding-left", paddingElement[1]+"em" );
});
if($("iframe").length > 0){
    $('iframe').on("load", function(){
        this.style.height =
        this.contentWindow.document.body.offsetHeight + 'px';
    });
}    
 
