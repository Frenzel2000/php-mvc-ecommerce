"use strict";

document.addEventListener("DOMContentLoaded", () => {
  console.log("Unified Filter/LoadMore JS geladen.");

  const filterContainer = document.querySelector(".filter-container");
  const filterBtn = document.getElementById("filter-btn");
  const loadMoreBtn = document.getElementById("load-more-btn");

  const limit = 6;
  let currentOffset = limit;

  if (!filterContainer) {
    return;
  }

  if (filterBtn) {
    //lädt Produkte neu, wenn filter button geklickt wird
    filterBtn.addEventListener("click", async (event) => {
      event.preventDefault();
      currentOffset = 0;

      const outputDiv = document.getElementById("products-output");
      if (outputDiv) {
        outputDiv.innerHTML = "";

        if (loadMoreBtn) loadMoreBtn.style.display = "inline-block";

        //true als argument sorgt dafür, dass alles komplett neu geladen wird
        const itemsLoaded = await loadProducts(outputDiv, true);

        if (itemsLoaded) {
          currentOffset += limit;
        }
      }
    });
  }

  //lädt mehr Produkte, wenn load more button geklickt wird
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener("click", async (event) => {
      event.preventDefault();
      console.log("Mehr laden geklickt!");

      const outputDiv = document.getElementById("products-output");

      //false als argument sorgt dafür, dass nicht neu geladen wird, sondern alles angehängt wird
      const itemsLoaded = await loadProducts(outputDiv, false);

      if (itemsLoaded) {
        currentOffset += limit;
      }
    });
  }

  //lädt Produkte basierend auf den Kriterien
  async function loadProducts(container, replaceContent) {
    //sammelt Input Elmente aus dem Filter-HTML block
    const minInput = document.getElementById("min-price");
    const maxInput = document.getElementById("max-price");
    const availCheck = document.getElementById("check-available");
    const bestCheck = document.getElementById("check-bestseller");

    //holt Daten aus Input Elementen
    const minPrice = minInput.value;
    const maxPrice = maxInput.value;
    const isAvailable = availCheck.checked;
    const isBestseller = bestCheck.checked;

    //ließt Attribute aus der Seite aus (damit filter dynamisch sein kann)
    const controller = filterContainer.dataset.controller;
    const mode = filterContainer.dataset.mode;
    const id = filterContainer.dataset.id || "";
    const term = filterContainer.dataset.term || "";

    // BASE_URL aus globaler Variable (oder leer, falls nicht gesetzt)
    // trailing slash sauber entfernen:
    const baseurl = (window.BASE_URL || "").replace(/\/$/, "");

    // Query-Parameter
    const params = new URLSearchParams({
      min: String(minPrice),
      max: String(maxPrice),
      available: String(isAvailable),
      bestseller: String(isBestseller),
      offset: String(currentOffset),
      limit: String(limit),
    });

    // Routing-URL bauen
    // Standard: /{controller}/ajaxFilter + optional id
    let url = `${baseurl}/${controller}/ajaxFilter`;

    // Category: /category/ajaxFilter/{id}
    if (mode === "category") {
      if (!id) {
        console.error("FEHLER: data-id fehlt für mode=category");
        return false;
      }
      url += `/${encodeURIComponent(id)}`;
    } else if (mode === "search") {
      if (!filterContainer.hasAttribute("data-term")) {
        console.error("FEHLER: data-term fehlt für mode=search");
        return false;
      }
      params.set("term", term ?? "");
    }

    url += `?${params.toString()}`;

    try {
      //sendet request ans backend
      const response = await fetch(url);
      if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);

      const html = await response.text();
      const trimmedHtml = html.trim();

      //prüft ob Elemente im HTML enthalten sind
      const hasProducts = trimmedHtml.includes('class="bestseller"');

      if (!hasProducts) {
        //Keine Produkte wurden gefunden
        if (!replaceContent) {
          //Hinweis, dass es keine Produkte mehr zum Laden gibt
          const loadMoreBtn = document.getElementById("load-more-btn");
          if (loadMoreBtn) loadMoreBtn.style.display = "none";
          alert("Keine weiteren Produkte verfügbar.");
        } else {
          //entfernt den mehr laden Button
          container.innerHTML = html;
          if (loadMoreBtn) loadMoreBtn.style.display = "none";
        }
        return false;
      }

      //Produkte wurden gefunden
      if (replaceContent) {
        container.innerHTML = html;
      } else {
        //Div um darin den neuen Content abzulagern
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = html;

        //Sucht existierenden Wrapper um dort die Kinder einzuhängen
        const existingWrapper = container.querySelector(".wrapper_bestseller");
        const newWrapper = tempDiv.querySelector(".wrapper_bestseller");

        //hängt jedes Kind in den gefundenen Wrapper ein, damit wieder alles sauber auf einer Ebene zusammenpasst
        if (existingWrapper && newWrapper) {
          while (newWrapper.firstChild) {
            existingWrapper.appendChild(newWrapper.firstChild);
          }
        } else {
          //fügt HTML direkt in die Seite ein, wenn Wrapper nicht gefunden wurde (nur Fallback)
          container.insertAdjacentHTML("beforeend", html);
        }
      }
      return true;
    } catch (error) {
      console.error("Netzwerk Fehler:", error);
      return false;
    }
  }
});
