var smwGlobalTimerInterval = null;
var smwCartExpiryHandled = false;
/******************************************************************
 * GLOBAL HEADER TIMER (ALL PAGES)
 ******************************************************************/
function getAnyActiveExpiry() {
  for (let i = 0; i < sessionStorage.length; i++) {
    const key = sessionStorage.key(i);
    if (key && key.indexOf('seat_expiry_') === 0) {
      try {
        const data = JSON.parse(sessionStorage.getItem(key));
        if (data && data.expiry && data.expiry > (Date.now() / 1000)) {
          return data.expiry;
        }
      } catch (e) {}
    }
  }
  return null;
}

/******************************************************************
 * GLOBAL TIMER RESET
 ******************************************************************/
function resetGlobalHeaderTimer() {
  const $timer = jQuery('#smw-global-seat-timer');

  if (smwGlobalTimerInterval) {
    clearInterval(smwGlobalTimerInterval);
    smwGlobalTimerInterval = null;
  }

  if ($timer.length) {
    $timer.hide().html('');
  }

  // Clear all seat-related sessionStorage
  Object.keys(sessionStorage).forEach(function (key) {
    if (key.indexOf('seat_expiry_') === 0 || key.indexOf('seat_hold_') === 0) {
      sessionStorage.removeItem(key);
    }
  });
}

/*function startGlobalHeaderTimer() {
  const expiryTs = getAnyActiveExpiry();
  const $timer = jQuery('#smw-global-seat-timer');

  // If no active expiry → stop & hide
  if (!expiryTs || !$timer.length) {
    if (smwGlobalTimerInterval) {
      clearInterval(smwGlobalTimerInterval);
      smwGlobalTimerInterval = null;
    }
    $timer.hide().html('');
    return;
  }

  // Prevent multiple intervals
  if (smwGlobalTimerInterval) {
    clearInterval(smwGlobalTimerInterval);
  }

  $timer.show();

  smwGlobalTimerInterval = setInterval(function () {
    const remain = (expiryTs * 1000) - Date.now();

    if (remain <= 0) {
      clearInterval(smwGlobalTimerInterval);
      smwGlobalTimerInterval = null;
      $timer.hide().html('');
      return;
    }

    const mins = Math.floor(remain / 60000);
    const secs = Math.floor((remain % 60000) / 1000);

    $timer.html(
      '🕒 <strong>' +
        mins +
        ':' +
        (secs < 10 ? '0' : '') +
        secs +
        '</strong> till cart is reset'
    );
  }, 1000);
}*/

function startGlobalHeaderTimer() {
  const $timer = jQuery('#smw-global-seat-timer');

  // 🔒 HARD STOP: if header timer container does not exist, do nothing
  // (this is what prevents Back / Close leaks)
  if (!$timer.length) {
    if (typeof smwGlobalTimerInterval !== 'undefined' && smwGlobalTimerInterval) {
      clearInterval(smwGlobalTimerInterval);
      smwGlobalTimerInterval = null;
    }
    return;
  }

  const expiryTs = getAnyActiveExpiry();

  // 🚫 No active seat expiry → hide timer
  if (!expiryTs) {
    if (smwGlobalTimerInterval) {
      clearInterval(smwGlobalTimerInterval);
      smwGlobalTimerInterval = null;
    }
    $timer.hide().html('');
    return;
  }

  // 🛑 Prevent multiple intervals
  if (smwGlobalTimerInterval) {
    clearInterval(smwGlobalTimerInterval);
  }

  // At this point:
  // - Header allows timer
  // - Seat expiry exists
  $timer.show();

  smwGlobalTimerInterval = setInterval(function () {
    const remain = (expiryTs * 1000) - Date.now();

    if (remain <= 0) {
      clearInterval(smwGlobalTimerInterval);
      smwGlobalTimerInterval = null;
      $timer.hide().html('');
      return;
    }

    const mins = Math.floor(remain / 60000);
    const secs = Math.floor((remain % 60000) / 1000);

    $timer.html(
      '🕒 <strong>' +
        mins +
        ':' +
        (secs < 10 ? '0' : '') +
        secs +
        '</strong> till cart is reset'
    );
  }, 1000);
}

(function ($) {
  "use strict";

  /******************************************************************
   * CONFIG
   ******************************************************************/
  var HOLD_MINUTES = 5;            // Seat hold duration
  var HOLD_MS = HOLD_MINUTES * 60 * 1000;

  /******************************************************************
   * SESSION STORAGE HELPERS (No Cookies)
   ******************************************************************/
  function ssGet(key) {
    try { return JSON.parse(sessionStorage.getItem(key)); }
    catch (e) { return null; }
  }
  function ssSet(key, val) {
    try { sessionStorage.setItem(key, JSON.stringify(val)); }
    catch (e) {}
  }
  function ssRemove(key) { sessionStorage.removeItem(key); }

  // Keys used per variation id
  function keyHold(vid) { return 'seat_hold_' + vid; }          // {vid, seat, title, expiry}
  function keyExpiry(vid) { return 'seat_expiry_' + vid; }      // {expiry:number}

  /******************************************************************
   * COUNTDOWN (works on PDP or Cart where .smw_woo_cc_notice exists)
   ******************************************************************/
  function renderCountdown($ele, expiryTs, title, onExpire) {
    if (!$ele || !$ele.length) return;

    function tick() {
      var now = Date.now();
      var remain = (expiryTs * 1000) - now;
      if (remain > 0) {
        var minutes = Math.floor(remain / 60000);
        var seconds = Math.floor((remain % 60000) / 1000);
        $ele.html(
          'We will hold your webinar (' + (title || $ele.data('title') || 'Webinar') + ') seats for ' +
          '<ins>' + minutes + ' minutes ' + (seconds < 10 ? '0' : '') + seconds + ' seconds</ins> only.'
        );
      } else {
        if (smwCartExpiryHandled) return;
        smwCartExpiryHandled = true;

        clearInterval(timer);

        // Remove notice completely (not just text)
        $ele.closest('.woocommerce-error, .woocommerce-message').remove();
        $ele.remove();
        if (typeof onExpire === 'function') onExpire();
        // Clear WooCommerce cart via AJAX, then reload
            jQuery.post(
              window.smwAjaxUrl,
              { action: 'smw_clear_cart' },
              function () {

                // Sync WooCommerce UI
                jQuery(document.body).trigger('wc_cart_emptied');
                jQuery(document.body).trigger('updated_wc_div');

                // Stop & reset global timer
                resetGlobalHeaderTimer();

                window.location.href = window.location.href;

              }
            );

        }
    }

    tick();
    var timer = setInterval(tick, 1000);
    return timer;
  }

  /******************************************************************
   * SEAT UI RESET (original helper)
   ******************************************************************/
  function as_reset($ele) {
    var _ckdln = $ele.closest('ul.my-tickets').find('input:checkbox:checked').length;
    setTimeout(function () {
      $('ul.my-tickets.de-active .ticket-box').prop('checked', false);
    }, 10);
    var _atr = $ele.data("value");
    $("#pa_seat").val(_atr).change();
    $("input[name=quantity]").val(_ckdln).change();
  }

  /******************************************************************
   * PARTICIPANT REFRESH (original)
   ******************************************************************/
  function getParticipant(_id) {
    $.get('?participant=true&productid=' + _id, function (data) {
      $(".tab-participant").html(data);
      setTimeout(function () { getParticipant(_id); }, 60000);
    });
  }

  /******************************************************************
   * DOCUMENT READY
   ******************************************************************/
  $(function () {

    startGlobalHeaderTimer();

    /******************************************************************
   * STOP HEADER TIMER WHEN CART IS CLEARED
   ******************************************************************/
    jQuery(document.body).on(
      'removed_from_cart wc_cart_emptied updated_wc_div',
      function () {
        resetGlobalHeaderTimer();
      }
    );

    /********************
     * Cart Page Notice
     ********************/
    var $notices = $('.smw_woo_cc_notice');
    if ($notices.length) {
      $notices.each(function () {
        var $this = $(this);
        var vid = $this.data('id');            // Variation ID expected
        var title = $this.data('title') || ''; // Optional in your markup
        var hold = ssGet(keyHold(vid));        // {vid, seat, title, expiry}
        var expiryObj = ssGet(keyExpiry(vid)); // {expiry}
        var expiryTs = 0;

        // Prefer explicit expiry store, fallback to hold.expiry if present
        if (expiryObj && typeof expiryObj.expiry === 'number') {
          expiryTs = expiryObj.expiry;
        } else if (hold && typeof hold.expiry === 'number') {
          expiryTs = hold.expiry;
        }

        if (expiryTs && expiryTs > (Date.now() / 1000)) {
          // Countdown until expiry; on expire, clear storage
          renderCountdown($this, expiryTs, (hold && hold.title) || title, function () {
            ssRemove(keyHold(vid));
            ssRemove(keyExpiry(vid));
          });
        } else {
          $this.text('Your seat hold has expired or is not set.');
        }
      });
    }

    /********************
     * Random Generator (original)
     ********************/
  /* function getMultipleRandom(arr, num) {
      const shuffled = [...arr].sort(() => 0.5 - Math.random());
      return shuffled.slice(0, num);
    }

  // Single-seat random generator: picks one unchecked, available seat per click
$("#generate-random").on("click", function (e) {
  e.preventDefault();

  // Select available checkboxes that are NOT:
  // - permanent (.rbtn-tt-perma)
  // - temporary (.rbtn-tt-temp)
  // - waiting (.wait)
  // - already checked by user (:checked)
  var $available = $(".my-tickets input[type=checkbox]")
                    .not(".rbtn-tt-perma")
                    .not(".rbtn-tt-temp")
                    .not(".wait")
                    .not(":checked");

  if ($available.length === 0) {
    alert("No available seats left to select.");
    return;
  }

  // pick a random index and trigger click on that checkbox
  var randIndex = Math.floor(Math.random() * $available.length);
  $($available[randIndex]).trigger("click");
});*/

    /********************
     * MULTI RANDOM SEAT GENERATOR (Number Input Version)
     ********************/

    function getAvailableSeats() {
        return jQuery(".my-tickets input[type=checkbox]")
            .not(".rbtn-tt-perma")
            .not(".rbtn-tt-temp")
            .not(".wait")
            .not(":checked");
    }

    function validateSeatInput() {
        let availableCount = getAvailableSeats().length;
        let $input = jQuery("#random-seat-count");
        let val = parseInt($input.val());

        if (isNaN(val) || val < 1) val = 1;
        if (val > availableCount) val = availableCount;

        $input.attr("max", availableCount);
        $input.val(val);
    }

    // When user manually types number
    jQuery(document).on("input", "#random-seat-count", function(){
        validateSeatInput();
    });

    // Main random button
    jQuery(document).on("click", "#generate-random", function(e){
        e.preventDefault();

        validateSeatInput();

        let seatCount = parseInt(jQuery("#random-seat-count").val());
        let $available = getAvailableSeats();

        if ($available.length === 0) {
            alert("No available seats left.");
            return;
        }

        // Clear previously selected seats
        jQuery(".ticket-box:checked")
            .prop("checked", false)
            .trigger("change");

        // Shuffle and select seats
        let shuffled = $available.sort(() => 0.5 - Math.random()).slice(0, seatCount);

        shuffled.each(function(){
            jQuery(this).trigger("click");
        });
    });

    // Update limits when seat availability changes
    jQuery(document).on("change", ".ticket-box", function(){
        validateSeatInput();
    });

    // When modal opens
    jQuery(document).on("click", "#smw-open-seat-modal", function(){
        setTimeout(validateSeatInput, 300);
    });

    /********************
     * Seat Selection (modified: uses sessionStorage)
     ********************/
    $(".ticket-box").change(function () {
      var $ele = $(this);
      var checkedCount = $ele.closest('ul.my-tickets').find('input:checkbox:checked').length;
      if (checkedCount > 0) {
        var vid = $ele.data("vid");
        var seat = $ele.val();

        console.log(`DEBUG: Seat clicked: ${seat} (Variation ID: ${vid}), Checked count = ${checkedCount}`);

        if (!vid) {
          console.warn('DEBUG: data-vid missing on .ticket-box element');
          return;
        }

        $ele.addClass('wait');
        $.get(`?availabilitycheck=true&variationid=${vid}&seat=${seat}`, function (cdata) {
          $ele.removeClass('wait');
          if (cdata == 1 || cdata === "1") { // Available
            $('ul.my-tickets').addClass('de-active');
            $ele.closest('ul.my-tickets').removeClass('de-active');
            $(".proceed-cart").show();
            $(".proceed-cart-info").hide();

            var seat_txt = (checkedCount === 1) ? 'seat' : 'seats';
            $(".prc-qty").html(`You have selected ${checkedCount} ${seat_txt}.`);

            // Store a hold in sessionStorage for 5 minutes
            var expiryTs = Math.floor(Date.now() / 1000) + (HOLD_MS / 1000);
            var title = $('.product_title, h1.product_title, .entry-title').first().text().trim() || 'Webinar';

            ssSet(keyHold(vid), { vid: vid, seat: seat, title: title, expiry: expiryTs });
            ssSet(keyExpiry(vid), { expiry: expiryTs }); // also stored in a separate key for simpler read

            console.log(`DEBUG: Hold stored: vid=${vid}, seat=${seat}, expiry=${expiryTs}`);

            // If a notice exists on this page, show live countdown
            var $notice = $('.smw_woo_cc_notice[data-id="' + vid + '"]');
            if ($notice.length) {
              renderCountdown($notice, expiryTs, title, function () {
                ssRemove(keyHold(vid));
                ssRemove(keyExpiry(vid));
              });
            }

            // Also auto-clear after 5 minutes (for PDP)
            setTimeout(function () {

              // Only clear UI + storage — NOT cart
              var current = ssGet(keyHold(vid));
              if (!current || current.expiry !== expiryTs) return;

              ssRemove(keyHold(vid));
              ssRemove(keyExpiry(vid));

              // Reset UI on PDP only
              $('ul.my-tickets').removeClass('de-active');
              $(".proceed-cart").hide();
              $(".proceed-cart-info").show();
              $ele.prop('checked', false);

              // Notice text only (no cart logic here)
              $(".smw_woo_cc_notice[data-id='" + vid + "']")
                .text('Your seat reservation expired.');

            }, HOLD_MS);

            as_reset($ele);

          } else {
            $ele.prop('checked', false).prop('disabled', true).addClass('rbtn-tt-temp');
            $(".availabilitycheck-msg").html(cdata).show();
            setTimeout(function () { $(".availabilitycheck-msg").html('').hide(); }, 3000);
            as_reset($ele);
          }
        });

      } else {
        // None checked
        $('ul.my-tickets').removeClass('de-active');
        $(".proceed-cart").hide();
        $(".proceed-cart-info").show();
        as_reset($ele);
      }
    });

    /********************
     * Proceed Cart (original behavior + SS validation)
     ********************/
    $(".prc-btn").click(function (e) {
      e.preventDefault();
      console.log('DEBUG: Proceed button clicked');

      // Verify we have a hold for the current product (use first ticket-box's vid as context)
      var $any = $('.ticket-box').first();
      var vid = $any.length ? $any.data('vid') : null;
      if (vid) {
        var hold = ssGet(keyHold(vid));
        if (!hold || hold.expiry <= (Date.now() / 1000)) {
          alert('Your seat hold has expired. Please reselect a seat.');
          return;
        }
      }

      var $btn = $(".single_add_to_cart_button");
      if ($btn.length) {
        $btn.trigger('click');  // should be non-AJAX for webinars per your PHP filter
        console.log('DEBUG: Triggered .single_add_to_cart_button click');
      } else if ($('form.cart').length) {
        $('form.cart').trigger('submit');
        console.log('DEBUG: Submitted form.cart');
      } else {
        console.warn('DEBUG: No add-to-cart button or form found.');
      }
    });

    /********************
     * "sss" Button - AJAX OTP (original)
     ********************/
    $(".sss").click(function (e) {
      $.ajax({
        dataType: "json",
        method: "POST",
        url: (typeof smwfa !== 'undefined' ? smwfa.ajaxurl : '/wp-admin/admin-ajax.php'),
        data: {
          dataType: "json",
          'action': 'smw_woo_ajax_function',
          'type': 'otp-send',
          'nonce': (typeof smwfa !== 'undefined' ? smwfa.nonce : '')
        },
        success: function (data) { },
        error: function (err) {
          console.log(err);
        },
        cache: false,
      });
    });

    /********************
     * Participant auto refresh (original)
     ********************/
    if ($(".tab-participant").length > 0) {
      var _id = $(".tab-participant").data("id");
      getParticipant(_id);
    }

    /********************
     * Winner Selection (original)
     ********************/
    $("input[name=rbtnseats]").click(function (e) {
      var _id = $(this).data('id');
      $("#tw_orderid").val(_id);
    });

    $("#tw_mywinnerform").submit(function (e) {
      e.preventDefault();
      $("#btn_select_winnerf").attr('disabled', 'disabled');
      $("#btn_select_winnerf span").html('Please wait...');

      var _pid = $("#tw_pid").val();
      var _oid = $("#tw_orderid").val();
      var _seat = $("input[name=rbtnseats]:checked").val();
      $("#wsnm").html($("#wsnm_" + _seat).html());
      $("#wsnmst").html(_seat);
      $.get('?myaction=selectwinner&pid=' + _pid + '&oid=' + _oid + '&seat=' + _seat, function (data) {
        $("#btn_select_winnerf").remove();
        $("input[name=rbtnseats]").attr('disabled', 'disabled');
        $('#sbWinner').show();
      });
    });

    $(".btn-close").click(function (e) {
      e.preventDefault();
      $('#sbWinner').hide();
      location.reload();
    });

  }); // ready

})(jQuery);
/******************************************************************
 * RELEASE SESSION HOLDS WHEN CART ITEM IS REMOVED
 ******************************************************************/
jQuery(function ($) {

  function clearAllSeatHolds() {
    Object.keys(sessionStorage).forEach(function (key) {
      if (key.indexOf('seat_hold_') === 0 || key.indexOf('seat_expiry_') === 0) {
        sessionStorage.removeItem(key);
      }
    });
  }

  // Fired when cart item is removed via AJAX
  $(document.body).on('removed_from_cart wc_cart_emptied updated_wc_div', function () {
    clearAllSeatHolds();
  });

});