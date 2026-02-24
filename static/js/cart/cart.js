"use strict";

// --- ÄNDERUNG START ---
// Anstatt jeden Button einzeln zu suchen, lauschen wir auf der GANZEN Seite.
// Wenn IRGENDWO ein Formular abgeschickt wird, prüfen wir, ob es ein Warenkorb-Formular ist.
document.addEventListener("submit", (event) => {
  // Wir prüfen: Hat das Formular die Klasse "cart_form"?
  if (event.target && event.target.classList.contains("cart_form")) {
    handleCartEvent(event);
  }
});
// --- ÄNDERUNG ENDE ---

//sendet AJAX request an backend
async function sendCartRequest(productId, url) {
  const formData = new FormData();
  formData.append("product_id", productId);
  formData.append("product_amount", 1);

  const requestPayload = {
    method: "POST",
    body: formData,
  };

  let response = await fetch(url, requestPayload);

  if (!response.ok && response.status !== 401) {
    throw new Error(`HTTP error! Status: ${response.status}`);
  }
  return response.json();
}

//Event Handler für Cart Events
async function handleCartEvent(event) {
  event.preventDefault(); // WICHTIG: Verhindert das Neuladen der Seite

  const form = event.target; // Hier nutzen wir direkt das Ziel des Events
  const targetURL = form.action;
  //URL Objekt um daraus die form action zu extrahieren
  const formURL = new URL(form.action, window.location.origin);

  let action = formURL.searchParams.get("action");
  if (!action) {
    const parts = formURL.pathname.split("/").filter(Boolean);
    action = parts[parts.length - 1] || null;
  }

  const submitButton = form.querySelector('button[type="submit"]');
  const productId = form.querySelector('input[name="product_id"]').value;

  try {
    const result = await sendCartRequest(productId, targetURL);

    //redirect zur Login Seite, wenn nicht angemeldet
    if (result.loggedIn === false && result.redirectURL) {
      window.location.href = result.redirectURL;
      return;
    }

    if (result.success) {
      //animiert den Button
      if (action === "add" && submitButton) {
        animateButtonFeedback(submitButton);
      }

      const productRow = form.closest(".item_wrapper");
      const totalElement = document.querySelector(".total_sum h1");
      const productTotalElement = productRow
        ? productRow.querySelector(".price_information h2")
        : null;

      //ändert Preis
      if (totalElement && productTotalElement) {
        changeCartTotal(totalElement, result.cartTotal);
        changeProductTotal(productTotalElement, result.cartItemTotal);
      }

      //löscht Element aus DOM, wenn Anzahl 0 ist
      if (result.cartAmount == 0) {
        if (productRow) {
          productRow.remove();
        }

        if (result.cartEmpty) {
          window.location.reload();
        }
      } else {
        //verringert die Anzahl des Produkts im Warenkorb
        const amountElement =
          form.parentElement.querySelector("#product_amount");
        if (amountElement) {
          changeCartAmount(amountElement, result.cartAmount);
        }
      }
    } else {
      if (result.stockExceeded) {
        showError(submitButton, result.message);
      } else {
        showError(submitButton, "Fehler beim hinzufügen");
      }
    }
  } catch (error) {
    console.error("Fehler: ", error);
  }
}

//ändert Preis * Amount für ein Produkt
function changeProductTotal(element, productTotal) {
  const numberValue = parseFloat(productTotal);
  const formatter = new Intl.NumberFormat("de-DE", {
    style: "currency",
    currency: "EUR",
    minimumFractionDigits: 2,
  });
  element.innerText = formatter.format(numberValue);
}

function changeCartTotal(element, cartTotal) {
  const numberValue = parseFloat(cartTotal);
  const formatter = new Intl.NumberFormat("de-DE", {
    style: "currency",
    currency: "EUR",
    minimumFractionDigits: 2,
  });
  element.innerText = formatter.format(numberValue);
}

function changeCartAmount(element, cartAmount) {
  element.innerText = cartAmount;
}

function animateButtonFeedback(button) {
  //add_button wird ignoriert, denn das ist der kleine + Knopf im Warenkorb
  if (!button.classList.contains("add_button")) {
    //holt das Button Element und den alten Style
    const originalText = button.innerHTML;
    // || "" verhindert, dass setAttribute versucht den style auf null anzuwenden
    const originalStyle = button.getAttribute("style") || "";

    //feedback style
    button.innerText = "Hinzugefügt";
    button.style.backgroundColor = "#22c55e";
    button.style.color = "white";
    button.style.border = "none";
    button.disabled = true;

    //ändert style des buttons für 1,5sek
    setTimeout(() => {
      button.innerHTML = originalText;
      button.setAttribute("style", originalStyle);
      button.disabled = false;
    }, 1500);
  }
}

function showError(button, message) {
  if (!button.classList.contains("add_button")) {
    const originalText = button.innerHTML;
    const originalStyle = button.getAttribute("style") || "";

    button.innerText = message;
    button.style.backgroundColor = "#ef4444";
    button.style.color = "white";
    button.style.border = "none";
    button.disabled = true;
    button.style.fontSize = "90%";

    setTimeout(() => {
      button.innerHTML = originalText;
      button.setAttribute("style", originalStyle);
      button.disabled = false;
    }, 2000);
  }
}
