var builder = this.ec_builder || (this.ec_builder = {});
var ec_utils = builder.Utils || (builder.Utils = {});
var ec_functions = builder.Functions || (builder.Functions = {});
var ec_settings = builder.Settings || (builder.Settings = {});
var ec_templates = builder.Templates || (builder.Templates = {});

var jsonParse=function (jsonStr) {
  var __json=jsonStr;
  try {
    __json=JSON.parse(jsonStr);
  } catch (e) {

  }
  return __json;
}

var ec_woo_debug=false;
/*
 * For sending AJAX request
 */

var shortcode_list = {};

var generate_shortcode_for_all_text = function() {
  jQuery('.ec-preview-content-sortable-column[data-settings-type="text"]').each(function() {
    var __self = jQuery(this);
    var __body = __self.find('.ec-preview-content-sortable-column-body');
    var __generate = do_shortcode(__body.html());
    __body.html(__generate);
  });
}

var generate_shortcode = function(key, value) {
  var __span_tag_list = [
    '[ec_woo_site_name]',
    '[ec_woo_order_date]',
    '[ec_woo_order_time]',
    '[ec_woo_order_datetime]',
    '[ec_woo_order_link]',
    '[ec_woo_user_name]',
    '[ec_woo_billing_first_name]',
    '[ec_woo_billing_last_name]',
    '[ec_woo_user_id]',
    '[ec_woo_user_email]',
    '[ec_woo_shipping_first_name]',
    '[ec_woo_shipping_last_name]',
    '[ec_woo_billing_phone]',
    '[ec_woo_order_id]',
    '[ec_woo_billing_email]',
    '[ec_woo_order_delivery_date]'
  ];;
  var __result = '';
  // if (__span_tag_list.indexOf(key) > -1) {
  //   __result = '<span data-shortcode="' + key + '">' + value + '</span>';
  // } else {
  //   __result = '<div data-shortcode="' + key + '">' + value + '</div>';
  // }
  __result = '<span data-shortcode="' + key + '">' + value + '</span>';
  return __result;
}

var do_shortcode = function(value) {
  value = value.split('<p').join('<div');
  value = value.split('</p>').join('</div>');

  var result = value.match(/\[([\w-]+)([^]*?)(\/?)\]/g);
   for(var index in result){
     var _shortcode=result[index];
     _has_type=(/type='([^"]*)'/).test(_shortcode);
     if (_has_type) {
       var data = {
         action: 'generate_shortcode',
         shortcode:_shortcode.split("'").join('"')
       };
       ajax_request(data, function(response) {
         shortcode_list[_shortcode]=response.data;
         for (var key in shortcode_list) {
           if (value.indexOf(key) > -1) {
             var __generated_shortcode = generate_shortcode(key, shortcode_list[key]);
             value = _join(value.split(key), __generated_shortcode);
           }
         }
       });
       ;
     }
   }
   for (var key in shortcode_list) {
     if (value.indexOf(key) > -1) {
       var __generated_shortcode = generate_shortcode(key, shortcode_list[key]);
       value = _join(value.split(key), __generated_shortcode);
     }
   }
   return value;
}
var _join = function(arr, value) {
  var result = '';
  for (var i = 0; i < arr.length; i++) {
    //console.log(arr[i]);
    if (arr[i].length > 0) {
      result += arr[i];
    }
    if (i != (arr.length - 1)) {
      result += value;
    }
  }
  return result;
}

/*
 * For sending AJAX request
 */
var ajax_request = function(data, success_callback, fail_callback) {

  var _ajax_url = woo_ec_vars.ajax_url;

  data.development=ec_woo_debug==true?1:0;

  var jqxhr = jQuery.post(_ajax_url, data);

  jqxhr.done(function(response) {
    if (ec_woo_debug) {
      console.log('jqxhr.done',response);
    }
    try {
        response = JSON.parse(response);
        if (response.code == 200) {
          if (success_callback !== undefined) {
            success_callback(response);
          }
        } else {
          if (fail_callback !== undefined) {
            fail_callback(response);
          }

          iziToast.error({
            title: 'Error',
            message: response.message,
            position: 'bottomRight'
          });
        }
    } catch (e) {
      if (ec_woo_debug) {
        console.log('jqxhr.done-json parsing',response);
      }
    }

  });

  jqxhr.fail(function(response) {
    if (ec_woo_debug) {
      console.log('jqxhr.fail',response);
    }
    iziToast.error({
      title: 'Request failed',
      message: "Please check <b>logs</b> in the plugin's folder",
      position: 'bottomRight'
    });
    if (fail_callback !== undefined) {
      fail_callback(response);
    }
  });

}

var enable_save_template = function() {
  jQuery('.ec-preview-header-control-item.ec-control-save').removeClass('ec-control-save-disabled');
}

var load_saved_templates = function(callback) {
  var _savedContent = '#modal-library-my-templates .ec-modal-library-grid';
  var $saved_content = jQuery(_savedContent);

  $saved_content.html(ec_templates.template_saved_no_data('Loading...'));
  var data = {
    action: 'template_load_saved'
  };

  ajax_request(data, function(response) {
    this.ec_builder.Settings.saved_templates = response.data;
    $saved_content.html(ec_utils.saved_templates.generate(ec_utils.saved_templates.list()));
    if (callback !== undefined) {
      callback();
    }
  });
}


var scrollTop = function() {
  setTimeout(function() {
    window.scrollTo(0, 0);
  }, 200);
}
