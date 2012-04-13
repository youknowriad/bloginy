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
  * CommentHandler : Comment handler
  * @param comment : dom
  */
var CommentHandler = function(comment) {

    var dom_object = comment;

    /**
     * Handler of vote link
     * @param approvelink : dom
     */
    var approveHandler = function(approvelink) {
      $.ajax({
         'url'     : approvelink.attr('href'),
         'success' : function() {
             approvelink.parents('.comment_details.waiting')
                .removeClass('waiting')
                .addClass('approved');
             approvelink.remove();
             BloginyMessage.show('The comment has been approved');
         }
      });
    }
    
    /**
     * Handler of vote link
     * @param approvelink : dom
     */
    var deleteHandler = function(deletelink) {
      if (confirm('Are you sure you want to delete this comment ?'))
      {
          $.ajax({
             'url'     : deletelink.attr('href'),
             'success' : function() {
                 deletelink.parents('.comment').remove();
                 BloginyMessage.show('The comment has been deleted');
             }
          });

      }
    }

    return {

        init: function() {
            dom_object.find('a.approve_link').click(function(e){
                e.preventDefault();
                approveHandler($(this));
            });
            
            dom_object.find('a.delete_link').click(function(e){
                e.preventDefault();
                deleteHandler($(this));
            });
        }
    }

};