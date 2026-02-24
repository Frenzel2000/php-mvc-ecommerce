"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const inputElement = document.querySelector("#live-search-input");
  const resultElement = document.querySelector("#live-search-results");
  if (!inputElement || !resultElement) return;

  const baseUrl = (window.BASE_URL || "").replace(/\/$/, "");
  let debounceTimer;

  inputElement.addEventListener("input", () => {
    clearTimeout(debounceTimer);
    const input = inputElement.value.trim();

    if (input === "") {
      resultElement.innerHTML = "";
      resultElement.classList.remove("active");
      return;
    }

    debounceTimer = setTimeout(async () => {
      try {
        const products = await sendSearchRequest(baseUrl, input);
        renderResult(baseUrl, products, resultElement);
      } catch (error) {
        console.error(error);
      }
    }, 300);
  });

  document.addEventListener("click", (event) => {
    if (
      !inputElement.contains(event.target) &&
      !resultElement.contains(event.target)
    ) {
      resultElement.classList.remove("active");
    }
  });
});

async function sendSearchRequest(baseUrl, keyword) {
  const url = `${baseUrl}/product/processSearch?term=${encodeURIComponent(
    keyword
  )}`;

  const response = await fetch(url, {
    method: "GET",
    credentials: "same-origin",
    headers: {
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
  });

  if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
  return response.json();
}

function toAssetUrl(baseUrl, path) {
  const p = path || "";
  if (/^https?:\/\//i.test(p) || p.startsWith("data:")) return p;
  if (p.startsWith("/")) return p;
  return `${baseUrl}/${p.replace(/^\/+/, "")}`;
}

function renderResult(baseUrl, jsonData, resultElement) {
  resultElement.innerHTML = "";

  if (!jsonData.productsFound || !jsonData.data || jsonData.data.length === 0) {
    resultElement.innerHTML =
      '<div style="padding:15px; color:#666;">Keine Produkte gefunden.</div>';
    resultElement.classList.add("active");
    return;
  }

  jsonData.data.forEach((product) => {
    const priceFormatted = new Intl.NumberFormat("de-DE", {
      style: "currency",
      currency: "EUR",
    }).format(product.price);

    const productLink = `${baseUrl}/product/show/${product.product_id}`;
    const imageSrc = toAssetUrl(baseUrl, product.asset_path);

    const html = `
      <a href="${productLink}" class="search-result-item">
        <img src="${imageSrc}" class="search-result-thumb" alt="${product.product_name}">
        <div>
          <div style="font-weight: bold;">${product.product_name}</div>
          <div style="font-size: 0.9em; color: #666;">${priceFormatted}</div>
        </div>
      </a>
    `;
    resultElement.insertAdjacentHTML("beforeend", html);
  });

  resultElement.classList.add("active");
}
