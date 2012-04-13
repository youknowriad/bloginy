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
var BloginyMessage = function(){

    return {

        show: function(message) {

            var dom = $('#bloginy-message');
            dom.css('opacity', '0.7');

            dom.html(message)
               .show()
               .slideUp(0, function(){
                   $(this).slideDown(200, function(){
                        $(this).animate({opacity : '0.8'}, 2000, function(){
                            $(this).slideUp(200);
                        });
                    });
               })
               
        }
    }
}();