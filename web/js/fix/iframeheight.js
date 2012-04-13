function pageY(elem) {
    return elem.offsetParent ? (elem.offsetTop + pageY(elem.offsetParent)) : elem.offsetTop;
}
var buffer = 10; //scroll bar buffer
function resizeIframe() {
    var height = window.innerHeight || document.body.clientHeight || document.documentElement.clientHeight;
    height -= pageY(document.getElementById('external_page'))+ buffer ;
    height = (height < 0) ? 0 : height + 12;
    document.getElementById('external_page').style.height = height + 'px';
}

$(document).ready(function(){
   $('#external_page').load(resizeIframe);
   window.onresize = resizeIframe;
});