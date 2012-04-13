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
  * Objet that handles a vertical menu
  */
function VerticalMenu(menu, target) {

    var _menu = menu;
    var _target = target;
    return {
        init: function() {
            $(_menu).find('a').click(function(e){
                if (!$(this).hasClass('fancy'))
                {
                    e.preventDefault();
                    $(_target).load($(this).attr('href'));
                    $(_menu).find('li').removeClass('current');
                    $(this).parent('li').addClass('current');
                }
            });
        }
    }
}