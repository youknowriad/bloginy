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
  * AjaxFormHandler : Object used to post forms using ajax
  * @param form : the form dom object
  * @param target_div : the target to load the response (must contain at least a form)
  */
var AjaxFormHandler = function(form, target_div) {

    var dom_object = form;
    var target = target_div

    /**
     * Handler of ajax form
     * @param form : dom
     */
    var formHandler = function(form) {
        $.ajax({
            type: 'POST',
            data: form.serialize(),
            url: form.attr('action'),
            success: function(response)
            {
                target.html(response);
            }
        });
    }

    return {

        init: function() {
            dom_object.submit(function(e){
                e.preventDefault();
                formHandler($(this));
            });
        }
    }

};