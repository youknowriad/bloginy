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
  * ShareLinkView : The view that handles the link sharing
  * @param form : dom
  * @param target : dom
  * @param write_link : dom
  * @param big_target : dom
  */
var ShareLinkView = function(form, target, write_link, big_target) {

    var _target = target;
    var _form = form;
    var _write_link = write_link;
    var _big_target = big_target;

    var getLinkHandler = function(link) {
        link.hide();
        $.ajax({
           url: link.attr('href'),
           type: 'get',
           data: {shared_link : _form.find('.shared_link').attr('value')},
           success: function(data)
           {
               if (data == 'none')
               {
                   _target.empty();
                   link.show();
               }
               else
               {
                   link.remove();
                   _write_link.remove();
                   _form.find('.shared_link').attr('disabled', true);
                   _target.html(data);
                   $.fancybox.resize();
                   
                   _target.find('form').submit(function(e){
                      e.preventDefault();
                      shareLinkHandler($(this));
                    });
               }
           }
        });
    }
    
    var shareLinkHandler = function(form) {
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function(data)
            {
               _target.html(data);
               $.fancybox.resize();
                
               // Init the sharing form handling
               _target.find('form').submit(function(e){
                  e.preventDefault();
                  shareLinkHandler($(this));
                });
            }
        })
    }
    
    var initWritePostHandler = function(link) {
        $.ajax({
            url: link.attr('href'),
            type: 'get',
            success: function(data)
            {
                _big_target.html(data);
                $.fancybox.resize();
                
                // Init de la création d'article
                _big_target.find('form').submit(function(e){
                  e.preventDefault();
                  writePostHandler($(this));
                });
            }
        })
    }
    
    var writePostHandler = function(form) {
        form.find('button').attr('disabled', 'disabled');
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function(data)
            {
               _big_target.html(data);
               $.fancybox.resize();
                
               // Init the sharing form handling
               _big_target.find('form').submit(function(e){
                  e.preventDefault();
                  writePostHandler($(this));
                });
            }
        })
    }
    
    return {

        init: function() {
            
            $(_form).find('#get-link-button').click(function(e){
                e.preventDefault();
                getLinkHandler($(this))
            });
            
            $(_form).submit(function(e){
                e.preventDefault();
                getLinkHandler($(this).find('#get-link-button'));
            });
            
            _write_link.find('a').click(function(e) {
               e.preventDefault();
               initWritePostHandler($(this));
            });
        },
        
        initSharingForm: function()
        {
            _target.find('form').submit(function(e){
              e.preventDefault();
              shareLinkHandler($(this));
            });
        }
    }

};