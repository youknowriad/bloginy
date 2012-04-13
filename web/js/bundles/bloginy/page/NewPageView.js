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
  * NewPageView : The view that handles the link sharing
  * @param form : dom
  * @param target : dom
  */
var NewPageView = function(form, target) {

    var _target = target;
    var _form = form;
    var _menu = menu;
    
    var newPageHandler = function(form) {
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function(data)
            {
               if (data.indexOf('document.location') < 0)
               {
                    $(_target).replaceWith(data);
                    $.fancybox.resize();
               }
               else
               {
                   $(_target).append(data);
               }
            }
        })
    }
    
    return {

        init: function() {
            
            $(_form).submit(function(e){
                e.preventDefault();
                newPageHandler($(this));
            });
        }
    }

};