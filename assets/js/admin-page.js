var builder = this.ec_builder || (this.ec_builder = {});
var utils = builder.Utils || (builder.Utils = {});
var functions = builder.Functions || (builder.Functions = {});
var settings = builder.Settings || (builder.Settings = {});
var templates = builder.Templates || (builder.Templates = {});


jQuery(function() {
  jQuery('body').addClass('ec-body');

  jQuery('#footer-thankyou,#footer-upgrade').remove();
  jQuery('#ec_woo_order').change();

  setTimeout(function() {
    jQuery('#ec_woo_save_as_lang').addClass('ec-modal-input');
    jQuery('#ec_woo_save_as_lang').css('width', '30%');
  }, 100);


  // var _top = jQuery('.ec-wrapper').position().top;
  // if (_top == 0) {
  //   jQuery('.ec-builder-header').css('margin-top', '32px');
  // } else if (_top > 32) {
  //   jQuery('.ec-builder-header').css('margin-top', '32px');
  // }
  var __height = jQuery('#wpadminbar').height() + jQuery('.ec-builder-header').height();
  jQuery('.ec-preview').css('margin-top', __height + 'px');

  scrollTop();


  var _is_demo = woo_ec_vars.is_demo;
  if (_is_demo) {
    var introguide = introJs();

    introguide.setOptions({
      showProgress: false
    });

    introguide.start();
  }
});

jQuery(document).on('change', '.ec-settings-menu', function() {
  var _val = jQuery(this).val();
  var _data = {
    action: 'save_panel_position',
    position: _val
  };

  ajax_request(_data, function() {
    iziToast.success({
      title: 'Success!',
      message: 'Saved panel position',
      position: 'bottomRight'
    });
    jQuery('.ec-builder-header').attr('style', '');
    if (_val === 'left') {
      jQuery('.ec-wrapper').removeClass('ec-panel-right');
    } else {
      jQuery('.ec-wrapper').addClass('ec-panel-right');
    }
  });
});

jQuery(document).on('click', '.ec-modal-library-grid-item .ec-modal-library-grid-row', function() {
  var _self = jQuery(this);
  var _parent = _self.parent();
  var _list = _parent.find('.ec-modal-library-grid-item-list');

  if (_parent.hasClass('collapsed')) {
    _list.slideDown(300, function() {
      _parent.removeClass('collapsed');
    });
  } else {
    _list.slideUp(300, function() {
      _parent.addClass('collapsed');
    });
  }

});
jQuery(document).on('click', '#settings-image-change', function() {
  if (window_media === undefined) {
    var window_media = wp.media({
      title: 'Select a media',
      library: {
        type: 'image'
      },
      multiple: false,
      button: {
        text: 'Select'
      }
    });

    var self = this; // Needed to retrieve our variable in the anonymous function below
    window_media.on('select', function() {
      var first = window_media.state().get('selection').first().toJSON();
      jQuery('#settings-image-source-url').val(first.url);
      jQuery('#settings-image-source-url').change();
    });
  }

  window_media.open();
});

jQuery(document).on('click', '.ec-modal-input-container.active #ec-import-submit', function() {
  var __self = jQuery(this);

  if (__self.hasClass('ec-clicked')) {
    return false;
  }
  var $label = __self.find('.ec-modal-input-submit-label');
  var $loading = __self.find('.ec-modal-input-submit-loading');

  __self.addClass('ec-clicked');
  $label.hide();
  $loading.show();

  var file_data = jQuery('#import-file').prop('files')[0];
  var _ajax_url = woo_ec_vars.ajax_url;


  var form_data = new FormData();
  form_data.append('import_file', file_data);
  form_data.append('action', 'import_json');
  jQuery.ajax({
    url: _ajax_url,
    dataType: 'json',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    success: function(response) {
      data = jsonParse(response.data);
      functions.import_json(data, true);
      jQuery('#modal-import').fadeOut();
      __self.removeClass('ec-clicked');
      $label.show();
      $loading.hide();
    }
  });
});

jQuery(document).on('click', '#ec-email-submit', function() {
  var __self = jQuery(this);
  if (__self.hasClass('ec-clicked')) {
    return false;
  }
  var $email = jQuery('#ec-email-address');

  if ($email.val() == '') {
    $email.addClass('ec-modal-input-has-error');
    return false;
  }
  $email.removeClass('ec-modal-input-has-error');
  var $loading = jQuery('.ec-modal-input-submit-loading');
  var $label = jQuery('.ec-modal-input-submit-label');

  var __html = functions.export_html(false);
  var _data = {
    action: 'send_email',
    email: $email.val(),
    html: __html
  };

  $label.hide();
  $loading.show();
  __self.addClass('ec-clicked');

  ajax_request(_data, function() {
    iziToast.success({
      title: 'Sent email!',
      message: 'Please check your email address',
      position: 'bottomRight'
    });

    $label.show();
    $loading.hide();
    __self.removeClass('ec-clicked');

    jQuery('#modal-send-email').fadeOut();

  },function () {
    $label.show();
    $loading.hide();
    __self.removeClass('ec-clicked');
  });
});

var __load_template = function() {
  // utils.remove_modal(function() {
  //   console.log('yessss');
  // });
  if (jQuery('.ec-tab-styles').hasClass('ec-active')) {
    jQuery('.ec-panel-header-icon').click();
  }
  jQuery('.ec-preview').addClass('ec-loading');
  jQuery('.ec-preview-content-wrapper').hide();

  var data = {
    action: 'template_load',
    lang: jQuery('#ec_woo_lang').val(),
    type: jQuery('#ec_woo_type').val(),
    order_id: jQuery('#ec_woo_order').val()
  };

  ajax_request(data, function(response) {
    //response = jsonParse(response);
    shortcode_list = response.shortcode_data;
    var __email = response.email;
    if (__email != undefined ) {
      if (__email.length>0) {
        functions.import_json(jsonParse(__email), true);
        generate_shortcode_for_all_text();
      }else {
        utils.load_default_row();
      }

    } else {
      utils.load_default_row();
    }
    jQuery('.ec-preview-content-wrapper').show();
    jQuery('.ec-preview').removeClass('ec-loading');


  });
}
jQuery(document).on('change', '#ec_woo_type', function() {

  var $type = jQuery('#ec_woo_type');
  if ($type.val() != '') {
    __load_template();
    $type.find('option[value=""]').remove();
    enable_save_template();
  }

});
jQuery(document).on('change', '#ec_woo_order', function() {
  __load_template();
});
jQuery(document).on('change', '#ec_woo_lang', function() {
  __load_template();
});

jQuery(document).on('click', '.ec-preview-header-control-item.ec-control-save', function() {
  var __self = jQuery(this);
  if (__self.hasClass('ec-control-save-disabled')) {
    return false;
  }

  var $loading = jQuery('.ec-save-loading');
  var $label = jQuery('.ec-save-label');
  __self.addClass('ec-control-save-disabled');

  var __exported_json = functions.export_json();
  var data = {
    action: 'template_save',
    lang: jQuery('#ec_woo_lang').val(),
    type: jQuery('#ec_woo_type').val(),
    email: __exported_json
  };

  $label.hide();
  $loading.show();

  ajax_request(data, function(response) {

    $label.show();
    $loading.hide();
    __self.removeClass('ec-control-save-disabled');
    iziToast.success({
      title: 'Success!',
      message: 'Template saved successfully',
      position: 'bottomRight'
    });
  });
});


jQuery(document).on('click', '#ec-modal-template-save', function() {
  var __self = jQuery(this);

  if (__self.hasClass('ec-clicked')) {
    return false;
  }
  var $label = __self.find('.ec-modal-input-submit-label');
  var $loading = __self.find('.ec-modal-input-submit-loading');
  var $name = jQuery('#ec-modal-template-name');

  if ($name.val().length == 0) {
    $name.addClass('ec-modal-input-has-error');
    return false;
  }

  $name.removeClass('ec-modal-input-has-error');
  __self.addClass('ec-clicked');
  $label.hide();
  $loading.show();

  var __html = functions.export_json();

  var data = {
    action: 'template_new_save',
    email: __html,
    name: $name.val()
  };

  ajax_request(data, function(response) {
    load_saved_templates(function() {
      jQuery('#modal-save').hide();
      jQuery('#modal-library .ec-modal-library-content').show();
      jQuery('#modal-library .ec-modal-library-preview').hide();

      jQuery('#modal-library').show();
      jQuery('.ec-modal-library-tabs-item[data-content="#modal-library-my-templates"]').click();

      $name.val('');
      __self.removeClass('ec-clicked');
      $label.show();
      $loading.hide();
    });


  });

});


jQuery(document).on('click', '.ec-modal-library-grid-action-item.ec-modal-library-grid-action-delete', function() {
  var __self = jQuery(this);
  var __row = __self.parents('.ec-modal-library-grid-row');

  var $controls = __row.find('.ec-modal-library-grid-action-list');
  var $loading = __row.find('.ec-modal-library-grid-action-loading');

  $controls.hide();
  $loading.show();

  var data = {
    action: 'template_delete_saved',
    id: __row.attr('data-id')
  };


  ajax_request(data, function(response) {
    iziToast.success({
      title: 'SUCCESS',
      message: 'Template deleted',
      position: 'bottomRight'
    });
    __row.remove();

  }, function() {
    $controls.show();
    $loading.hide();
  });

});


jQuery(document).on('click', '.ec-modal-library-grid-action-item.ec-modal-library-grid-action-insert', function() {
  var __self = jQuery(this);
  var __row = __self.parents('.ec-modal-library-grid-row');

  var $controls = __row.find('.ec-modal-library-grid-action-list');
  var $loading = __row.find('.ec-modal-library-grid-action-loading');
  var $modal = jQuery('#modal-library');


  var _id = __row.attr('data-id');
  $controls.hide();
  $loading.show();

  setTimeout(function() {
    var _data = jsonParse(utils.saved_templates.where(_id)[0].data);

    functions.import_json(_data, true);
    generate_shortcode_for_all_text();

    $controls.show();
    $loading.hide();
    $modal.fadeOut();
  }, 300);



});


jQuery(document).on('click', '#ec-modal-template-save-as', function() {
  var __self = jQuery(this);
  var $type = jQuery('#ec_woo_save_as_email_type');
  var $lang = jQuery('#ec_woo_save_as_lang');
  if (__self.hasClass('ec-clicked')) {
    return false;
  }
  var has_error = false;
  // if ($lang.val() == '') {
  //   has_error = true;
  //   $lang.addClass('ec-modal-input-has-error');
  // }
  if ($type.val() == '') {
    has_error = true;
    $type.addClass('ec-modal-input-has-error');
  }

  if (has_error == true) {
    return false;
  }

  $type.removeClass('ec-modal-input-has-error');
  var $loading = jQuery('.ec-modal-input-submit-loading');
  var $label = jQuery('.ec-modal-input-submit-label');

  var __exported_json = functions.export_json();
  var data = {
    action: 'template_save_as',
    lang: $lang.val(),
    type: $type.val(),
    email: __exported_json
  };

  $label.hide();
  $loading.show();
  __self.addClass('ec-clicked');

  ajax_request(data, function(response) {

    $label.show();
    $loading.hide();
    __self.removeClass('ec-clicked');
    jQuery('#modal-save-as').fadeOut();
    iziToast.success({
      title: 'Success!',
      message: 'Saved successfully',
      position: 'bottomRight'
    });
  });
});



jQuery(document).on('change', '#ec-settings-show-product-img', function() {
  var _self = jQuery(this);
  if (_self.is(':checked')) {
    jQuery('.ec-settings-product-image').show();
  } else {
    jQuery('.ec-settings-product-image').hide();
  }
});


jQuery(document).on('change', '.ec-settings-item', function() {
  var data = {
    action: 'save_settings',
    img_width: jQuery('#ec-settings-product-img-width').val(),
    img_height: jQuery('#ec-settings-product-img-height').val(),
    show_img: jQuery('#ec-settings-show-product-img').is(":checked") == true ? 1 : 0,
    show_sku: jQuery('#ec-settings-show-product-sku').is(":checked") == true ? 1 : 0,
    cell_padding: jQuery('#ec-settings-border-padding').val()
  };

  ajax_request(data, function(response) {
    jQuery('#ec_woo_order').change();
    iziToast.success({
      title: 'Success!',
      message: 'Settings saved',
      position: 'bottomRight'
    });

  });
});




// Setup isScrolling variable
var isScrolling;

// Listen for scroll events
window.addEventListener('scroll', function(event) {

  // Clear our timeout throughout the scroll
  window.clearTimeout(isScrolling);

  // Set a timeout to run after scrolling ends
  isScrolling = setTimeout(function() {

    // Run the callback
    //console.log('Scrolling has stopped.');
    window.scrollTo(0, 0);
  }, 66);

}, false);

jQuery(document).on('click', '.ec-control-help', function() {
  jQuery('.ec-modal-library-content').show();
  jQuery('#modal-help').fadeIn();
});

//
