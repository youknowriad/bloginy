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
  * Object that handle the default value of a form
  */
function SearchFormView(form) {

    var _form = form;
    var _initial_value = $(form).find('input').attr('value');
    
    return {
        init: function() {
            _form.find('input')
                .addClass('empty')
                .focusin(function() {
                    if ($(this).attr('value') == _initial_value)
                    {
                        $(this).attr('value', '');
                        $(this).removeClass('empty');
                    }
                })
                .focusout(function() {
                    if ($(this).attr('value') == '')
                    {
                        $(this).attr('value', _initial_value);
                        $(this).addClass('empty');
                    }
                });
        }
    }
}