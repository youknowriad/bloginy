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
  * AvatarHandler : Avatar handler
  * @param avatar_form : dom
  */
var AvatarHandler = function(avatar_form) {

    var form = avatar_form;

    /**
     * Handler of use_gravatar input
     * @param input : dom
     */
    var gravatarPreferenceHandler = function(input) {
        form.find('.avatar_path input').attr('disabled',  input.attr('checked'));
    }

    return {

        init: function() {
            gravatarPreferenceHandler(form.find('.use_gravatar input'));

            form.find('.use_gravatar input').change(function(e){
                gravatarPreferenceHandler($(this));
            });
        }
    }

};