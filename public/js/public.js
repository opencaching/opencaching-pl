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

  // resources/ts/components/BadgeGpxComponent.ts
  var BadgeGpxComponent = class {
    constructor(root = document) {
      this.root = root;
    }
    init() {
      const panel = this.root.querySelector("#badgeGpxOptions");
      if (!panel) {
        return;
      }
      document.addEventListener("click", (event) => {
        const target = event.target;
        if (!target) {
          return;
        }
        const btn = target.closest("[data-toggle-gpx]");
        if (!btn) {
          return;
        }
        event.preventDefault();
        console.log("test");
        panel.classList.toggle("collapsed");
      });
    }
  };

  // resources/ts/components/BadgeGainersComponent.ts
  var BadgeGainersComponent = class {
    constructor(root = document) {
      this.badgeId = null;
      this.perPage = 20;
      this.root = root;
    }
    init() {
      const rootEl = this.root.querySelector("#badge-gainers-root");
      if (!rootEl) {
        return;
      }
      const badgeIdAttr = rootEl.getAttribute("data-badge-id");
      if (!badgeIdAttr) {
        return;
      }
      this.badgeId = parseInt(badgeIdAttr, 10);
      if (Number.isNaN(this.badgeId)) {
        return;
      }
      const rows = this.root.querySelectorAll(".badge-level-row");
      rows.forEach((row) => {
        row.addEventListener("click", () => {
          const levelAttr = row.getAttribute("data-level");
          if (levelAttr === null) {
            return;
          }
          const level = parseInt(levelAttr, 10);
          if (Number.isNaN(level)) {
            return;
          }
          void this.loadGainers(level, 1);
        });
      });
    }
    cacheId(level, page) {
      return "gainers-cache-" + this.badgeId + "-" + level + "-" + page;
    }
    escapeHtml(value) {
      return value.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }
    renderUsers(users) {
      let html = "";
      for (const u of users) {
        html += "<tr>";
        html += '<td><a href="/viewprofile.php?userid=' + u.userId + '">' + this.escapeHtml(u.username) + "</a></td>";
        html += '<td class="align-right">' + u.currVal + "</td>";
        html += "<td>" + this.escapeHtml(u.levelDate) + "</td>";
        html += "</tr>";
      }
      if (users.length === 0) {
        html = '<tr><td colspan="3">No results</td></tr>';
      }
      return html;
    }
    renderPagination(total, page, level) {
      const pages = Math.ceil(total / this.perPage);
      if (pages < 2) {
        return "";
      }
      const listSize = 5;
      let leftPage = page - Math.floor(listSize / 2);
      let rightPage = page + Math.floor(listSize / 2);
      if (listSize % 2 === 0) {
        leftPage++;
      }
      if (leftPage <= 0) {
        rightPage += 1 - leftPage;
        leftPage = 1;
      }
      let html = '<ul class="pagination">';
      if (leftPage > 1) {
        const destPrev = page - 1 <= 0 ? 1 : page - 1;
        html += '<li><a href="#" data-level="' + level + '" data-page="1" title="first">&lt;&lt;</a></li>';
        html += '<li><a href="#" data-level="' + level + '" data-page="' + destPrev + '" title="prev">&lt;</a></li>';
      }
      for (let i = leftPage; i <= rightPage; i++) {
        if (i > pages) {
          break;
        }
        if (i === page) {
          html += '<li><a class="active" href="#" data-level="' + level + '" data-page="' + i + '">' + i + "</a></li>";
        } else {
          html += '<li><a href="#" data-level="' + level + '" data-page="' + i + '">' + i + "</a></li>";
        }
      }
      if (pages > rightPage) {
        const destNext = page + 1 > pages ? pages : page + 1;
        html += '<li><a href="#" data-level="' + level + '" data-page="' + destNext + '" title="next">&gt;</a></li>';
        html += '<li><a href="#" data-level="' + level + '" data-page="' + pages + '" title="last">&gt;&gt;</a></li>';
      }
      html += "</ul>";
      return html;
    }
    showCached(key) {
      const cached = document.getElementById(key);
      if (!cached) {
        return false;
      }
      const json = cached.getAttribute("data-json");
      if (!json) {
        return false;
      }
      const data = JSON.parse(json);
      const tbody = document.getElementById("gainers-tbody");
      const pag = document.getElementById("gainers-pagination");
      if (tbody) {
        tbody.innerHTML = this.renderUsers(data.users);
      }
      if (pag) {
        pag.innerHTML = this.renderPagination(data.total, data.page, data.level);
      }
      this.bindPagination(data.level);
      return true;
    }
    storeCache(key, data) {
      const div = document.createElement("div");
      div.id = key;
      div.classList.add("hidden");
      div.setAttribute("data-json", JSON.stringify(data));
      const store = document.getElementById("gainers-cache-store");
      if (store) {
        store.appendChild(div);
      }
    }
    async loadGainers(level, page) {
      const frame = document.getElementById("badge-gainers-frame");
      if (frame) {
        frame.classList.remove("hidden");
      }
      const key = this.cacheId(level, page);
      if (this.showCached(key)) {
        return;
      }
      const tbody = document.getElementById("gainers-tbody");
      const pag = document.getElementById("gainers-pagination");
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="3">Loading...</td></tr>';
      }
      if (pag) {
        pag.innerHTML = "";
      }
      if (this.badgeId === null) {
        return;
      }
      try {
        const formData = new FormData();
        formData.append("badge_id", String(this.badgeId));
        formData.append("level", String(level));
        formData.append("page", String(page));
        const resp = await fetch("/Badges.Badges/gainers", {
          method: "POST",
          body: formData,
          credentials: "same-origin"
        });
        const data = await resp.json();
        if (data.status !== "OK") {
          if (tbody) {
            tbody.innerHTML = '<tr><td colspan="3">' + this.escapeHtml(data.message || "Error") + "</td></tr>";
          }
          return;
        }
        const fullData = data;
        this.storeCache(key, fullData);
        if (tbody) {
          tbody.innerHTML = this.renderUsers(fullData.users);
        }
        if (pag) {
          pag.innerHTML = this.renderPagination(fullData.total, fullData.page, fullData.level);
        }
        this.bindPagination(fullData.level);
      } catch (e) {
        if (tbody) {
          tbody.innerHTML = '<tr><td colspan="3">Error</td></tr>';
        }
      }
    }
    bindPagination(level) {
      const links = document.querySelectorAll("#gainers-pagination a[data-page]");
      links.forEach((link) => {
        const anchor = link;
        anchor.onclick = (ev) => {
          ev.preventDefault();
          const target = ev.currentTarget;
          const pAttr = target.getAttribute("data-page");
          const lAttr = target.getAttribute("data-level");
          const p = pAttr ? parseInt(pAttr, 10) : 1;
          const lvl = lAttr ? parseInt(lAttr, 10) : level;
          if (Number.isNaN(p) || Number.isNaN(lvl)) {
            return;
          }
          void this.loadGainers(lvl, p);
        };
      });
    }
  };

  // resources/ts/components/BadgeLevelImageUploadComponent.ts
  var BadgeLevelImageUploadComponent = class {
    constructor(root = document) {
      this.root = root;
    }
    init() {
      const container = this.root.querySelector("[data-badge-level-upload]");
      if (!container) {
        return;
      }
      const badgeIdAttr = container.getAttribute("data-badge-id");
      if (!badgeIdAttr) {
        return;
      }
      const badgeId = parseInt(badgeIdAttr, 10);
      if (Number.isNaN(badgeId)) {
        return;
      }
      const modelJson = container.getAttribute("data-upload-model");
      if (!modelJson) {
        return;
      }
      let baseModel;
      try {
        baseModel = JSON.parse(modelJson);
      } catch (e) {
        return;
      }
      const previewLabel = container.getAttribute("data-ln-preview") || "Preview";
      const deleteLabel = container.getAttribute("data-ln-delete") || "Delete";
      const noneLabel = container.getAttribute("data-ln-none") || "";
      const buttons = this.root.querySelectorAll(".badge-level-upload-btn");
      buttons.forEach((btn) => {
        btn.addEventListener("click", (event) => {
          event.preventDefault();
          const levelAttr = btn.getAttribute("data-level");
          if (levelAttr === null) {
            return;
          }
          const level = parseInt(levelAttr, 10);
          if (Number.isNaN(level)) {
            return;
          }
          const ocUploadFn = window.ocUpload;
          if (typeof ocUploadFn !== "function") {
            window.alert("Upload not available");
            return;
          }
          const model = JSON.parse(JSON.stringify(baseModel));
          model.submitUrl = "/Badges.Badges/uploadLevelImageAjax/" + badgeId + "/" + level;
          ocUploadFn(model, (uploadResult) => {
            if (!uploadResult || !uploadResult.success) {
              console.log("upload fail", uploadResult && uploadResult.message);
              return;
            }
            const newImage = Array.isArray(uploadResult.newFiles) ? uploadResult.newFiles[0] : uploadResult.newFiles;
            if (!newImage) {
              return;
            }
            const cell = this.root.querySelector('[data-level-image="' + level + '"]');
            if (cell) {
              const url = newImage.charAt(0) === "/" ? newImage : "/" + newImage;
              cell.innerHTML = '<a href="' + url + '" target="_blank">' + this.escapeHtml(previewLabel) + "</a>";
              const delBtn = document.createElement("button");
              delBtn.type = "button";
              delBtn.className = "blue-button-small mt-5";
              delBtn.textContent = deleteLabel;
              delBtn.addEventListener("click", () => {
                void fetch("/Badges.Badges/deleteLevelImageAjax/" + badgeId + "/" + level, {
                  method: "POST",
                  credentials: "same-origin"
                }).then((resp) => resp.json()).then((data) => {
                  if (data.status !== "OK") {
                    return;
                  }
                  if (noneLabel !== "") {
                    cell.innerHTML = this.escapeHtml(noneLabel);
                  } else {
                    cell.innerHTML = "";
                  }
                });
              });
              cell.appendChild(delBtn);
            }
          });
        });
      });
    }
    escapeHtml(value) {
      return value.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }
  };

  // resources/ts/components/BadgeImageUploadComponent.ts
  var BadgeImageUploadComponent = class {
    constructor(root = document) {
      this.root = root;
    }
    init() {
      const container = this.root.querySelector("[data-badge-image-upload]");
      if (!container) {
        return;
      }
      const badgeIdAttr = container.getAttribute("data-badge-id");
      if (!badgeIdAttr) {
        return;
      }
      const badgeId = parseInt(badgeIdAttr, 10);
      if (Number.isNaN(badgeId)) {
        return;
      }
      const modelJson = container.getAttribute("data-upload-model");
      if (!modelJson) {
        return;
      }
      let baseModel;
      try {
        baseModel = JSON.parse(modelJson);
      } catch (e) {
        return;
      }
      const previewLabel = container.getAttribute("data-ln-preview") || "Preview";
      const deleteLabel = container.getAttribute("data-ln-delete") || "Delete";
      const noneLabel = container.getAttribute("data-ln-none") || "";
      const img = this.root.querySelector("[data-badge-image]");
      const buttons = this.root.querySelectorAll(".badge-image-upload-btn");
      const ensureDeleteButton = () => {
        let delBtn = this.root.querySelector(".badge-image-delete-btn");
        if (delBtn) {
          return delBtn;
        }
        delBtn = document.createElement("button");
        delBtn.type = "button";
        delBtn.className = "blue-button-small badge-image-delete-btn mt-10";
        delBtn.textContent = deleteLabel;
        const uploadBtn = this.root.querySelector(".badge-image-upload-btn");
        if (uploadBtn && uploadBtn.parentNode) {
          uploadBtn.parentNode.insertBefore(delBtn, uploadBtn.nextSibling);
        } else if (container.parentNode) {
          container.parentNode.appendChild(delBtn);
        }
        return delBtn;
      };
      const bindDelete = (delBtn) => {
        delBtn.onclick = () => {
          const ocUploadFn = window.fetch;
          void fetch("/Badges.Badges/deleteImageAjax/" + badgeId, {
            method: "POST",
            credentials: "same-origin"
          }).then((resp) => resp.json()).then((data) => {
            if (data.status !== "OK") {
              return;
            }
            if (img) {
              img.src = "";
              img.classList.add("hidden");
            }
            delBtn.remove();
          });
        };
      };
      if (img && img.getAttribute("src") && img.getAttribute("src") !== "") {
        bindDelete(ensureDeleteButton());
      }
      buttons.forEach((btn) => {
        btn.addEventListener("click", (event) => {
          event.preventDefault();
          const ocUploadFn = window.ocUpload;
          if (typeof ocUploadFn !== "function") {
            window.alert("Upload not available");
            return;
          }
          const model = JSON.parse(JSON.stringify(baseModel));
          model.submitUrl = "/Badges.Badges/uploadImageAjax/" + badgeId;
          ocUploadFn(model, (uploadResult) => {
            if (!uploadResult || !uploadResult.success) {
              console.log("upload fail", uploadResult && uploadResult.message);
              return;
            }
            const newImage = Array.isArray(uploadResult.newFiles) ? uploadResult.newFiles[0] : uploadResult.newFiles;
            if (!newImage) {
              return;
            }
            const url = newImage.charAt(0) === "/" ? newImage : "/" + newImage;
            if (img) {
              img.src = url;
              img.classList.remove("hidden");
            }
            bindDelete(ensureDeleteButton());
          });
        });
      });
    }
  };

  // resources/ts/index.ts
  new PowerTrailEditComponent().init();
  new RoleManagerFormComponent().init();
  new BadgeGpxComponent().init();
  new BadgeGainersComponent().init();
  new BadgeLevelImageUploadComponent().init();
  new BadgeImageUploadComponent().init();
})();
