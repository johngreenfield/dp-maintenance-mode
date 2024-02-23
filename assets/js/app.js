// JS Script for dp-maintenance-mode
// Remap jQuery to $.
(function ($) {
   // Ajax request to refresh the image preview
   function refresh_Image(the_id){
      let data = {
         action: 'dpmm_get_image',
         id: the_id
      };

      $.get(ajaxurl, data, function(response) {

         if(response.success === true) {
               $('#dpmm-preview-image').replaceWith( response.data.image );

               remove_Delete_Button(the_id);
         }
      });
   }

   // Remove the delete button if no image is selected
   function remove_Delete_Button(id) {
      const del_btn = $('input#dpmm-media-delete');

      if(del_btn.length) {
         if(id > 0) {
               del_btn.show();
         } else {
               del_btn.hide();;
         }
      }
   }

   // Re-index profiles.
   function RefreshProfilesIndex(selector) {
      $(document).find("[id^=social_profile_"  + selector +  "]").each(function (index) {
         $(this).attr('id', 'social_profile_' +  selector +  '_'   (index));
         $(this).closest('div').find('label').attr('for', 'social_profile_' +  selector  + '_'   (index));
      });
   }

   // Capitalize first letter on string.
   function toTitleCase(str) {
      return str.replace(
         /\w\S*/g,
         function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
         }
      );
   }

   // Update Events.
   function RefreshEventListener() {
      // Remove handler from existing elements
      $("button.dp-social-remove").off();
      // Re-add event handler for all matching elements
      $("button.dp-social-remove").on("click", function (event) {
         event.preventDefault();
         var selected = $(event.target).parent('div').find('input').attr('class');
         $(event.target).parents('div.dp-social-profile').css('visibility', 'hidden').slideUp("normal", function () {
            $(this).remove();
            RefreshProfilesIndex(selected);
         });
      });
   }

   // Select options toggle refresh.
   function RefreshSelectOptions(target_id) {
      if (target_id === undefined) {
         var $target = $(document).find("select.select-toggle option");
      } else {
         var $target = $(document).find("#" + target_id).closest("form").find("select.select-toggle option");
      }
      $target.on("mousedown", function () {
         var $self = $(this);
         if ($self.prop("selected"))
            $self.prop("selected", false);
         else
            $self.prop("selected", true);
         return false;
      });
   }

   /* trigger when page is ready */
   $(document).ready( function($) {
      let imageId = $('input#dpmm-image-id');

      remove_Delete_Button(imageId.val());

      $('input#dpmm-media-manager').click(function(e) {
         e.preventDefault();

         let image_frame;

         if(image_frame){
            image_frame.open();
         }

         // Define image_frame as wp.media object
         image_frame = wp.media({
            title: 'Select Media',
            multiple : false,
            library : {
               type : 'image',
            }
         });

         // On close, get selections and save to the hidden input
         // plus other AJAX stuff to refresh the image preview
         image_frame.on('close',function() {
            let selection =  image_frame.state().get('selection');
            let gallery_ids = new Array();
            let my_index = 0;

            selection.each(function(attachment) {
               gallery_ids[my_index] = attachment['id'];
               my_index++;
            });

            let ids = gallery_ids.join(",");

            if(ids.length === 0) return true;//if closed withput selecting an image
            
            $('input#dpmm-image-id').val(ids);
            
            refresh_Image(ids);
            
            remove_Delete_Button(imageId.val());
         });

         // On open, get the id from the hidden input
         // and select the appropiate images in the media manager
         image_frame.on('open',function() {
            let selection =  image_frame.state().get('selection');
            let ids = $('input#dpmm-image-id').val().split(',');

            ids.forEach(function(id) {
               let attachment = wp.media.attachment(id);

               attachment.fetch();
               selection.add( attachment ? [ attachment ] : [] );
            });

            remove_Delete_Button(imageId.val());

         });

         image_frame.open();       
      });

      $('input#dpmm-media-delete').click(function(e) {
         e.preventDefault();

         if(imageId.val() > 0) {
               imageId.val(0);
               refresh_Image(imageId.val());
         }
      });

      $('input[type=checkbox][name=dpmm-enabled]').change(function(e){
         e.preventDefault();

         $('.dpmm-settings-wrapper').toggle();

         $('#enabled-warning').toggle();
      })

      $('input[type=radio][name=dpmm-mode]').change(function(e) {
         e.preventDefault();

         $('.form--dpmm-construction-settings').toggle();
      })

      $('.dpmm-advanced-settings').on('click', function(e) {
            e.preventDefault();

            $('.form--dpmm-advanced-settings').toggle();

            if ($('.form--dpmm-advanced-settings').is(':visible')) {
               $(this).find('.dpmm-advanced-settings__label-advanced').hide();
               $(this).find('.dpmm-advanced-settings__label-hide-advanced').show();
            } else {
               $(this).find('.dpmm-advanced-settings__label-advanced').show();
               $(this).find('.dpmm-advanced-settings__label-hide-advanced').hide();
            }
      });
      
      $('.dpmm-toggle-all').on('click', function(event) {
         event.preventDefault();

         var checkBoxes = $("input.dpmm-roles");

         checkBoxes.prop("checked", !checkBoxes.prop("checked"));
      });

      RefreshEventListener();
   
      $("#social_profile_add").on("click", function (event) {
         event.preventDefault();

         var selected = $("#social_profile_selector").val();
         var $clone = $(document).find(".dp-social-profile").first().clone();

         $clone = $('<div>').addClass("dp-social-profile");
         $clone.html(
            '<label for="dpmm-social-profiles-' + selected +'-1" class="dp-option-label">' + toTitleCase(selected) + ':</label>' + 
            '<input type="text" id="dpmm-social-profiles-' + selected + '-1" ' + 'name="dpmm-social-profiles[' + selected + '][]" class="' + selected + '" value="" placeholder="http://" />' + 
            '<button class="button dp-social-remove"><b>-</b></button>');
         $clone.insertBefore($(document).find(".dp-social-profile-selector-wrapper").prev()).hide().css({visibility: 'hidden'}).slideDown("normal", function () {
            $(this).css('visibility', 'visible');
         });

         RefreshEventListener();
      });

      RefreshSelectOptions();
   });
}(window.jQuery || window.$));