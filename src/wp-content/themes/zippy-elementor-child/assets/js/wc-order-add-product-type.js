jQuery(function ($) {
  console.log("order service type script loaded");

  const SERVICE_SELECT_HTML = `
    <div class="order-service-type-wrap" style="padding:12px 16px;border-bottom:1px solid #eee;">
      <label style="display:block;font-weight:600;margin-bottom:4px;">
        Service Type
      </label>
      <select id="order_service_type" style="width:100%;">
        <option value="">-- Select service --</option>
        <option value="Hourly/Disposal">Hourly / Disposal</option>
        <option value="Airport Arrival Transfer">Airport Arrival Transfer</option>
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

        const orderId = getOrderId();
        if (!orderId) return;

        // ===== LOAD SERVICE TYPE FROM DB =====
        loadServiceType(orderId).done(function (res) {
          if (!res.success) return;

          const service = res.data.service_type;
          if (!service) return;

          $("#order_service_type").val(service);
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

    // Gửi service type
    settings.data += "&order_service_type=" + encodeURIComponent(service);
  });
});
