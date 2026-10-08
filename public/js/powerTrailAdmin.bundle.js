"use strict";
(() => {
  // src/ts/admin/powerTrailAdmin.ts
  function initializePowerTrailAdmin() {
    document.addEventListener("submit", (event) => {
      const target = event.target;
      if (!(target instanceof HTMLFormElement) || !target.matches(".powerTrail-remove-cache-form")) {
        return;
      }
      event.preventDefault();
      const form = target;
      const confirmMessage = form.dataset.confirmMessage || "Czy na pewno chcesz usun\u0105\u0107 kesza ze \u015Bcie\u017Cki?";
      if (!window.confirm(confirmMessage)) {
        return;
      }
      const submitButton = form.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.disabled = true;
      }
      fetch(form.action, {
        method: "POST",
        body: new FormData(form),
        headers: { Accept: "application/json" }
      }).then(async (response) => {
        var _a;
        const result = await response.json();
        if (!response.ok || result.status !== "OK") {
          throw new Error(result.message || "Nie uda\u0142o si\u0119 usun\u0105\u0107 kesza z Geo\u015Acie\u017Cki.");
        }
        (_a = form.closest("tr")) == null ? void 0 : _a.remove();
      }).catch((error) => {
        window.alert(error instanceof Error ? error.message : "Nie uda\u0142o si\u0119 usun\u0105\u0107 kesza z Geo\u015Acie\u017Cki.");
        if (submitButton) {
          submitButton.disabled = false;
        }
      });
    });
  }

  // src/ts/admin/index.ts
  initializePowerTrailAdmin();
})();
