document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById("toggle-can-see-total");

  if (!toggle) return;

  toggle.addEventListener("change", function () {
    const status = toggle.checked ? 1 : 0;

    const formData = new FormData();
    formData.append("action", "toggle_can_see_total");
    formData.append("status", status);

    fetch(woocommerce_params.ajax_url, {
      method: "POST",
      credentials: "same-origin",
      body: formData,
    });
  });
});
