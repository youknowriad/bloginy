$(document).ready(function(){
   
   $('#menu a.current').css('border-color', $('#menu a.current').css('background-color'));
   
   $('#menu a').hover(function(){
       $.data($(this)[0], 'color', $(this).css('background-color'));
       $(this).stop().animate({
           'background-color' : '#000'
       }, 300);
   }, function() {
       $(this).stop().animate({
           'background-color' : $.data($(this)[0], 'color')
       }, 300);
   });
      
});


