"use strict";
(() => {
  // resources/ts/components/PowerTrailEditComponent.ts
  async function postForm(form) {
    const response = await fetch(form.action, {
      method: "POST",
      body: new FormData(form),
      headers: { Accept: "application/json" }
    });
    const result = await response.json();
    if (!response.ok || result.status !== "OK") {
      throw new Error(result.message || form.dataset.lnErrorMessage || "Error");
    }
  }
  function showError(error, form) {
    window.alert(form.dataset.lnErrorMessage || (error instanceof Error ? error.message : "Error"));
  }
  var PowerTrailEditComponent = class {
    constructor(root = document) {
      this.root = root;
    }
    init() {
      this.root.querySelectorAll(".powerTrail-remove-cache-form").forEach((form) => this.bindRemoveCacheForm(form));
      this.root.querySelectorAll(".powerTrail-status").forEach((container) => this.bindStatus(container));
    }
    bindRemoveCacheForm(form) {
      form.onsubmit = (event) => {
        event.preventDefault();
        const confirmMessage = form.dataset.lnConfirmMessage || "";
        if (!window.confirm(confirmMessage)) {
          return;
        }
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
          submitButton.disabled = true;
        }
        postForm(form).catch((error) => {
          showError(error, form);
          if (submitButton) {
            submitButton.disabled = false;
          }
        }).finally(() => this.refreshAndReload(form.dataset.refreshUrl));
      };
    }
    bindStatus(container) {
      const label = container.querySelector(".powerTrail-status-label");
      const changeButton = container.querySelector(".powerTrail-status-change-button");
      const form = container.querySelector(".powerTrail-status-form");
      const select = container.querySelector('select[name="status"]');
      const submitButton = form == null ? void 0 : form.querySelector('button[type="submit"]');
      if (!label || !changeButton || !form || !select) {
        return;
      }
      changeButton.onclick = () => {
        changeButton.hidden = true;
        form.hidden = false;
      };
      form.onsubmit = (event) => {
        event.preventDefault();
        if (submitButton) {
          submitButton.disabled = true;
        }
        postForm(form).then(() => {
          var _a;
          label.textContent = ((_a = select.selectedOptions[0].textContent) == null ? void 0 : _a.trim()) || "";
        }).catch((error) => showError(error, form)).finally(() => {
          if (submitButton) {
            submitButton.disabled = false;
          }
          form.hidden = true;
          changeButton.hidden = false;
        });
      };
    }
    refreshAndReload(refreshUrl) {
      const reload = () => window.location.reload();
      if (!refreshUrl) {
        reload();
        return;
      }
      fetch(refreshUrl, { method: "POST" }).catch(() => void 0).then(reload);
    }
  };

  // resources/ts/index.ts
  new PowerTrailEditComponent().init();
})();
