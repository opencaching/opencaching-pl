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
    return result;
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
      this.root.querySelectorAll(".powerTrail-wis").forEach((container) => this.bindWis(container));
      this.root.querySelectorAll(".powerTrail-date-created").forEach((container) => this.bindDateCreated(container));
      this.root.querySelectorAll(".powerTrail-description").forEach((container) => this.bindDescription(container));
      this.root.querySelectorAll(".powerTrail-founders").forEach((container) => this.bindFounders(container));
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
        postForm(form).then((result) => {
          var _a;
          (_a = form.closest("tr")) == null ? void 0 : _a.remove();
          this.updateCacheCount(result.newCachesCount);
        }).catch((error) => {
          showError(error, form);
          if (submitButton) {
            submitButton.disabled = false;
          }
        });
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
    bindWis(container) {
      const label = container.querySelector(".powerTrail-wis-label");
      const changeButton = container.querySelector(".powerTrail-wis-change-button");
      const form = container.querySelector(".powerTrail-wis-form");
      const input = form == null ? void 0 : form.querySelector('input[name="wis"]');
      const submitButton = form == null ? void 0 : form.querySelector('button[type="submit"]');
      if (!label || !changeButton || !form || !input) {
        return;
      }
      changeButton.onclick = () => {
        changeButton.hidden = true;
        form.hidden = false;
        input.focus();
      };
      form.onsubmit = (event) => {
        event.preventDefault();
        if (submitButton) {
          submitButton.disabled = true;
        }
        postForm(form).then((result) => {
          var _a;
          const wis = (_a = result.wis) != null ? _a : Number(input.value);
          label.textContent = String(wis);
          input.value = String(wis);
        }).catch((error) => showError(error, form)).finally(() => {
          if (submitButton) {
            submitButton.disabled = false;
          }
          form.hidden = true;
          changeButton.hidden = false;
        });
      };
    }
    bindDateCreated(container) {
      const label = container.querySelector(".powerTrail-date-created-label");
      const changeButton = container.querySelector(".powerTrail-date-created-change-button");
      const form = container.querySelector(".powerTrail-date-created-form");
      const input = form == null ? void 0 : form.querySelector('input[name="dateCreated"]');
      const submitButton = form == null ? void 0 : form.querySelector('button[type="submit"]');
      if (!label || !changeButton || !form || !input) {
        return;
      }
      changeButton.onclick = () => {
        label.hidden = true;
        changeButton.hidden = true;
        form.hidden = false;
        input.focus();
      };
      form.onsubmit = (event) => {
        event.preventDefault();
        if (submitButton) {
          submitButton.disabled = true;
        }
        postForm(form).then((result) => {
          var _a;
          const dateCreated = (_a = result.dateCreated) != null ? _a : input.value;
          label.textContent = dateCreated;
          input.value = dateCreated;
        }).catch((error) => showError(error, form)).finally(() => {
          if (submitButton) {
            submitButton.disabled = false;
          }
          form.hidden = true;
          label.hidden = false;
          changeButton.hidden = false;
        });
      };
    }
    bindDescription(container) {
      var _a, _b, _c, _d, _e;
      const editButton = container.querySelector(".powerTrail-description-edit-button");
      const targetId = container.dataset.descriptionTarget;
      const target = targetId ? document.getElementById(targetId) : null;
      const form = (_a = target == null ? void 0 : target.querySelector(".powerTrail-description-form")) != null ? _a : null;
      const panel = (_b = target == null ? void 0 : target.querySelector(".powerTrail-description-panel")) != null ? _b : null;
      const textarea = (_c = target == null ? void 0 : target.querySelector("textarea")) != null ? _c : null;
      const hiddenInput = form == null ? void 0 : form.querySelector('input[name="description"]');
      const saveButton = form == null ? void 0 : form.querySelector(".powerTrail-description-save-button");
      const hideButton = (_d = target == null ? void 0 : target.querySelector(".powerTrail-description-hide-button")) != null ? _d : null;
      const message = (_e = target == null ? void 0 : target.querySelector(".powerTrail-description-message")) != null ? _e : null;
      if (!editButton || !target || !form || !panel || !textarea || !hiddenInput) {
        return;
      }
      const getEditor = () => {
        var _a2;
        const tinymce = window.tinymce;
        return (_a2 = tinymce == null ? void 0 : tinymce.get(textarea.id)) != null ? _a2 : null;
      };
      const setMessage = (text, isError = false) => {
        if (!message) {
          return;
        }
        message.textContent = text;
        message.classList.toggle("text-danger", isError);
        message.classList.toggle("text-success", !isError && text !== "");
      };
      const openPanel = () => {
        panel.style.overflow = "hidden";
        panel.style.maxHeight = `${panel.scrollHeight}px`;
        panel.classList.add("is-open");
        panel.setAttribute("aria-hidden", "false");
        editButton.disabled = true;
        setMessage("");
        window.setTimeout(() => {
          var _a2;
          if (!panel.classList.contains("is-open")) {
            return;
          }
          panel.style.maxHeight = `${panel.scrollHeight}px`;
          panel.style.overflow = "visible";
          const editor = getEditor();
          (_a2 = editor == null ? void 0 : editor.getContainer()) == null ? void 0 : _a2.classList.add("w-100");
          editor == null ? void 0 : editor.focus();
          window.dispatchEvent(new Event("resize"));
        }, 350);
      };
      const closePanel = () => {
        panel.style.overflow = "hidden";
        panel.style.maxHeight = "0px";
        panel.classList.remove("is-open");
        panel.setAttribute("aria-hidden", "true");
        editButton.disabled = false;
        setMessage("");
      };
      editButton.onclick = openPanel;
      if (hideButton) {
        hideButton.onclick = closePanel;
      }
      form.onsubmit = (event) => {
        event.preventDefault();
        const editor = getEditor();
        hiddenInput.value = editor ? editor.getContent() : textarea.value;
        if (saveButton) {
          saveButton.disabled = true;
        }
        postForm(form).then((result) => {
          setMessage(result.message || "");
        }).catch((error) => {
          setMessage(
            error instanceof Error ? error.message : form.dataset.lnErrorMessage || "Error",
            true
          );
        }).finally(() => {
          if (saveButton) {
            saveButton.disabled = false;
          }
        });
      };
    }
    bindFounders(container) {
      const list = container.querySelector(".powerTrail-founders-list");
      const editButton = container.querySelector(".powerTrail-founders-edit-button");
      const finishButton = container.querySelector(".powerTrail-founders-finish-button");
      const addForm = container.querySelector(".powerTrail-add-founder-form");
      const input = addForm == null ? void 0 : addForm.querySelector('input[name="username"]');
      const addSubmitButton = addForm == null ? void 0 : addForm.querySelector('button[type="submit"]');
      if (!list || !editButton || !finishButton || !addForm || !input) {
        return;
      }
      container.querySelectorAll(".powerTrail-remove-founder-form").forEach((form) => this.bindRemoveFounderForm(form, container));
      const setEditing = (editing) => {
        container.classList.toggle("powerTrail-founders--editing", editing);
        editButton.hidden = editing;
        addForm.hidden = !editing;
        finishButton.hidden = !editing;
        container.querySelectorAll(".powerTrail-founder-remove").forEach((button) => {
          button.hidden = !editing;
        });
        if (editing) {
          input.focus();
        } else {
          input.value = "";
          this.setFoundersMessage(container, "");
        }
      };
      editButton.onclick = () => setEditing(true);
      finishButton.onclick = () => setEditing(false);
      addForm.onsubmit = (event) => {
        event.preventDefault();
        if (addSubmitButton) {
          addSubmitButton.disabled = true;
        }
        postForm(addForm).then((result) => {
          if (result.founder) {
            this.appendFounder(container, result.founder);
          }
          input.value = "";
          this.setFoundersMessage(container, result.message || "");
        }).catch((error) => {
          this.setFoundersMessage(
            container,
            error instanceof Error ? error.message : addForm.dataset.lnErrorMessage || "Error",
            true
          );
        }).finally(() => {
          if (addSubmitButton) {
            addSubmitButton.disabled = false;
          }
          input.focus();
        });
      };
    }
    bindRemoveFounderForm(form, container) {
      form.onsubmit = (event) => {
        var _a;
        event.preventDefault();
        if (!window.confirm(form.dataset.lnConfirmMessage || "")) {
          return;
        }
        const request = postForm(form);
        (_a = form.closest(".powerTrail-founder")) == null ? void 0 : _a.remove();
        this.updateFoundersEmptyState(container);
        request.then((result) => {
          this.setFoundersMessage(container, result.message || "");
        }).catch((error) => {
          this.setFoundersMessage(
            container,
            error instanceof Error ? error.message : form.dataset.lnErrorMessage || "Error",
            true
          );
        });
      };
    }
    appendFounder(container, founder) {
      var _a;
      const list = container.querySelector(".powerTrail-founders-list");
      if (!list) {
        return;
      }
      (_a = container.querySelector(".powerTrail-founders-empty")) == null ? void 0 : _a.remove();
      const form = document.createElement("form");
      form.className = "powerTrail-founder powerTrail-remove-founder-form d-inline-flex align-items-center mr-5";
      form.method = "post";
      form.action = container.dataset.removeAction || "";
      form.dataset.lnConfirmMessage = container.dataset.lnConfirmMessage || "";
      form.dataset.lnErrorMessage = container.dataset.lnRemoveError || "";
      const link = document.createElement("a");
      link.href = `/viewprofile.php?userid=${encodeURIComponent(String(founder.user_id))}`;
      link.target = "_blank";
      link.rel = "noopener noreferrer";
      link.textContent = founder.username;
      const userId = document.createElement("input");
      userId.type = "hidden";
      userId.name = "userId";
      userId.value = String(founder.user_id);
      const removeButton = document.createElement("button");
      removeButton.type = "submit";
      removeButton.className = "red-button-small px-5 ml-5 powerTrail-founder-remove";
      removeButton.title = container.dataset.lnRemoveLabel || "Remove";
      removeButton.textContent = "\xD7";
      removeButton.hidden = !container.classList.contains("powerTrail-founders--editing");
      form.append(link, userId, removeButton);
      list.append(form);
      this.bindRemoveFounderForm(form, container);
    }
    updateFoundersEmptyState(container) {
      const list = container.querySelector(".powerTrail-founders-list");
      if (!list) {
        return;
      }
      const hasFounders = list.querySelector(".powerTrail-founder") !== null;
      const empty = container.querySelector(".powerTrail-founders-empty");
      if (hasFounders) {
        empty == null ? void 0 : empty.remove();
        return;
      }
      if (!empty) {
        const emptyMessage = document.createElement("span");
        emptyMessage.className = "powerTrail-founders-empty";
        emptyMessage.textContent = container.dataset.lnNoFounders || "";
        list.append(emptyMessage);
      }
    }
    setFoundersMessage(container, message, isError = false) {
      const element = container.querySelector(".powerTrail-founders-message");
      if (!element) {
        return;
      }
      element.textContent = message;
      element.classList.toggle("text-danger", isError);
      element.classList.toggle("text-success", !isError && message !== "");
    }
    updateCacheCount(count) {
      if (count === void 0) {
        return;
      }
      this.root.querySelectorAll(".powerTrail-cache-count").forEach((element) => {
        element.textContent = String(count);
      });
    }
  };

  // resources/ts/components/RoleManagerFormComponent.ts
  var RoleManagerFormComponent = class {
    constructor(root = document) {
      this.root = root;
    }
    init() {
      this.root.querySelectorAll(".role-manager-form").forEach((form) => this.bindForm(form));
    }
    bindForm(form) {
      form.onsubmit = (event) => {
        event.preventDefault();
        const submitter = event.submitter instanceof HTMLButtonElement ? event.submitter : null;
        const isRemoveAll = (submitter == null ? void 0 : submitter.dataset.lnAction) === "remove_all";
        if (isRemoveAll) {
          const confirmMessage = form.dataset.lnConfirmRemoveAll || "";
          if (confirmMessage && !window.confirm(confirmMessage)) {
            return;
          }
        }
        void fetch(form.action, {
          method: "POST",
          body: new FormData(form),
          headers: {
            Accept: "application/json"
          }
        }).catch(() => void 0).finally(() => window.location.reload());
      };
    }
  };

  // resources/ts/index.ts
  new PowerTrailEditComponent().init();
  new RoleManagerFormComponent().init();
})();
