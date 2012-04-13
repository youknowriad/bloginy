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
  *
  * Objet that handles the view of the login panel
  */
function RefreshDomHandler() {

    return {
        refreshElement: function(timeout, element, url) {
           $(element).fadeTo(timeout, 1, function(){
               $.ajax({
                   type: "GET",
                   url: url,
                   dataType: "html",
                   success: function(data){
                       var div = "<div>"+data+"</div>";
                       jQuery(div).prependTo(element).hide().fadeIn(1000);
                   }
               });
           });
        }
    }
}