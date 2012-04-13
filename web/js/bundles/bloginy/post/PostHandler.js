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
  * PostHandler : Post handler
  * @param post : dom
  */
var PostHandler = function(post) {

    var dom_object = post;

    /**
     * Handler of vote link
     * @param votelink : dom
     */
    var voteHandler = function(votelink) {
      $('.vote-button', dom_object).load($(votelink).attr('href'));
    }

    return {

        init: function() {
            dom_object.find('a.vote-link.effective').click(function(e){
                e.preventDefault();
                voteHandler($(this));
            });
        }
    }

};