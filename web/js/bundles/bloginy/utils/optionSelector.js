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
var optionSelector = function(){

    return {

        /**
         * @param selector The selector of the select box
         * @param url The url to go to: string that must containt option_string
         * @param option_string: the string to replace by the reel parameter
         */
        init: function(selector, url, option_string) {
            if (url.indexOf(option_string) < 0)
            {
                throw new Exception('The URL is not correct');
            }

            $(selector).change(function(e){
                e.preventDefault();
                document.location = url.replace(option_string, $(this).val());
            });
        }
    }

}();