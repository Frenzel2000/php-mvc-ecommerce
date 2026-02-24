document.addEventListener("DOMContentLoaded", () => {
  const pw = document.querySelector("#password");
  const pw2 = document.querySelector("#repeat_password");
  if (!pw || !pw2) return;

  const hint = document.querySelector("#pw-match-hint");
  const form = pw.closest("form");
  const submitBtn = form ? form.querySelector('button[type="submit"]') : null;

  const validate = () => {
    const a = pw.value;
    const b = pw2.value;

    // solange repeat leer ist: kein Fehler anzeigen
    if (b.length === 0) {
      pw2.setCustomValidity("");
      if (hint) hint.textContent = "";
      if (submitBtn) submitBtn.disabled = false;
      return;
    }

    if (a !== b) {
      pw2.setCustomValidity("Passwörter stimmen nicht überein");
      if (hint) hint.textContent = "Passwörter stimmen nicht überein.";
      if (submitBtn) submitBtn.disabled = true;
    } else {
      pw2.setCustomValidity("");
      if (hint) hint.textContent = "";
      if (submitBtn) submitBtn.disabled = false;
    }
  };

  pw.addEventListener("input", validate);
  pw2.addEventListener("input", validate);

  // blockt Submit auch dann, wenn jemand disabled entfernt etc.
  if (form) {
    form.addEventListener("submit", (e) => {
      validate();
      if (!form.checkValidity()) {
        e.preventDefault();
        pw2.reportValidity();
      }
    });
  }
});
