jQuery(function ($) {
  const SERVICE_SELECT_HTML = `
    <div class="order-service-type-wrap" style="padding:12px 16px;border-bottom:1px solid #eee;">
      <label style="display:block;font-weight:600;margin-bottom:4px;">
        Service Type
      </label>
      <select id="order_service_type" style="width:100%;">
        <option value="">-- Select service --</option>
        <option value="Hourly/Disposal">Hourly / Disposal</option>
        <option value="Airport Arrival Transfer">Airport Arrival Transfer (Per Trip)</option>
        <option value="Airport Departure Transfer">Airport Departure Transfer (Per Trip)</option>
        <option value="Point-to-point Transfer">Point-to-point Transfer (Per Trip)</option>
      </select>
    </div>
  `;

  function getOrderId() {
    return $("#post_ID").val();
  }

  function loadServiceType(orderId) {
    return $.get(ajaxurl, {
      action: "get_order_service_type",
      order_id: orderId,
    });
  }

  function loadAllVehicles() {
    return $.get(ajaxurl, {
      action: "get_all_vehicles",
    });
  }

  // ===== OBSERVE ADD PRODUCT MODAL =====
  const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        if (node.id !== "wc-backbone-modal-dialog") return;

        const $modal = $(node);
        if ($modal.find("#order_service_type").length) return;

        const $header = $modal.find(".wc-backbone-modal-header");
        if (!$header.length) return;

        $header.after(SERVICE_SELECT_HTML);

        // Inject Vehicle Container
        const $vehicleContainer = $('<div class="quick-select-vehicles-wrap" style="padding:12px 16px; border-bottom:1px solid #eee; background: #fafafa;"><label style="display:block;font-weight:600;margin-bottom:8px;">Quick Select Vehicle</label><div class="vehicle-tags-list" style="display:flex; flex-wrap:wrap; gap:8px;">Loading vehicles...</div></div>');
        $modal.find(".order-service-type-wrap").after($vehicleContainer);

        // Load Vehicles
        loadAllVehicles().done(function(res) {
          if (!res.success) return;
          const $list = $modal.find(".vehicle-tags-list");
          $list.empty();
          
          res.data.forEach(function(vehicle) {
            const $tag = $(`<span class="vehicle-tag" data-id="${vehicle.id}" style="padding: 4px 10px; background:#fff; border:1px solid #ddd; border-radius:15px; cursor:pointer; font-size:12px; transition: all 0.2s; white-space:nowrap;">${vehicle.name}</span>`);
            
            $tag.hover(
              function() { $(this).css({ 'background': '#f0f0f0', 'border-color': '#bbb' }); },
              function() { $(this).css({ 'background': '#fff', 'border-color': '#ddd' }); }
            );

            $tag.on('click', function() {
              const vId = $(this).data('id');
              const vName = $(this).text();
              
              const $allSearches = $modal.find('select.wc-product-search');
              let $search = $allSearches.filter(function() {
                return !$(this).val();
              }).first();

              if (!$search.length) {
                $search = $allSearches.last();
              }

              if ($search.length) {
                // Add option if not exists
                if ($search.find(`option[value="${vId}"]`).length === 0) {
                  const newOption = new Option(vName, vId, true, true);
                  $search.append(newOption).trigger('change');
                } else {
                  $search.val(vId).trigger('change');
                }
                
                // Visual feedback
                $modal.find('.vehicle-tag').css({ 'border-color': '#ddd', 'background': '#fff', 'color': '#000' });
                $(this).css({ 'border-color': '#2271b1', 'background': '#f0f6fb', 'color': '#2271b1' });
              }
            });

            $list.append($tag);
          });
        });

        const orderId = getOrderId();
        if (!orderId) return;

        // ===== LOAD SERVICE TYPE FROM DB =====
        loadServiceType(orderId).done(function (res) {
          if (!res.success) return;

          const service = res.data.service_type;
          if (!service) return;

          $("#order_service_type").val(service).prop("disabled", true).css({
            "background-color": "#f8f8f8",
            cursor: "not-allowed",
            opacity: "0.8",
          });
        });
      });
    });
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });

  // ===== VALIDATE + SEND SERVICE TYPE WHEN ADD PRODUCT =====
  $(document).ajaxSend(function (event, jqxhr, settings) {
    let dataStr = "";

    if (typeof settings.data === "string") {
      dataStr = settings.data;
    } else if (typeof settings.data === "object") {
      dataStr = $.param(settings.data);
    }

    if (!dataStr.includes("woocommerce_add_order_item")) return;

    const service = $("#order_service_type").val();

    if (!service) {
      alert("Please select Service Type before adding product.");
      jqxhr.abort();
      return;
    }

    // ===== VALIDATE MIN QUANTITY FOR HOURLY DISPOSAL =====
    if (service === "Hourly/Disposal") {
      let qty = 0;

      if (typeof settings.data === "string") {
        const params = new URLSearchParams(settings.data);
        for (let [key, value] of params.entries()) {
          console.log(`Param: ${key} = ${value}`);
          if (key.includes("[qty]") || key === "quantity" || key === "qty") {
            qty = parseInt(value || 0);
            break;
          }
        }
      } else if (typeof settings.data === "object" && settings.data !== null) {
        qty = parseInt(settings.data.quantity || settings.data.qty || 0);
      }

      if (!qty || qty <= 0) {
        const modalQty =
          $("#wc-backbone-modal-dialog .quantity input[type=number]").val() ||
          $("#wc-backbone-modal-dialog input[name*='[qty]']").val();
        if (modalQty) {
          qty = parseInt(modalQty);
        }
      }

      if (qty > 0 && qty < 3) {
        alert(
          "For Hourly / Disposal service, the minimum quantity is 3 hours.",
        );
        jqxhr.abort();

        if ($.unblockUI) {
          $.unblockUI();
        }
        $(
          ".wc-backbone-modal-content, #wc-backbone-modal-dialog, .wc-order-items-editable",
        ).each(function () {
          if ($(this).unblock) $(this).unblock();
        });
        $(".blockUI").remove();
        $(".loading").removeClass("loading");

        return;
      }
    }

    if (typeof settings.data === "string") {
      settings.data += "&order_service_type=" + encodeURIComponent(service);
    } else if (typeof settings.data === "object" && settings.data !== null) {
      settings.data.order_service_type = service;
    }
  });
});
