/**
  *  Bloginy, Blog Aggregator
  *  Copyright (C) 2012  Riad Benguella - Rizeway
  *
  *  This program is free software: you can redistribute it and/or modify
  *
  *  it under the terms of the GNU General Public License as published by
  *  the Free Software Foundation, either version 3 of the License, or
  *  any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  *  You should have received a copy of the GNU General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
var bloginyPager = function(){
    
    return {

        init: function(parent) {
            $('.bloginy-pager').css('opacity', '0.5');
            $('.bloginy-pager').hover(function(){
                    $(this).stop().animate({opacity : 1}, 300);
                }, function(){
                    $(this).stop().animate({opacity : 0.5}, 300);
                });

            $('.bloginy-pager', parent).click(function(e){
                e.preventDefault();
                var pager = $(this);
                $(this).addClass('selected');
                
                $.ajax({
                    url: pager.attr('href'),
                    type: 'GET',
                    success: function(data) {
                        if ($.trim(data) !== '') $(data).insertAfter(pager);
                        pager.replaceWith('<div class="bloginy-separator"></div>');
                    }
                })
            });
            
        }
    }

}();