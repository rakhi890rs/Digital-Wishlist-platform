document.addEventListener("DOMContentLoaded", function() {
    // Live character count for description
    const descInput = document.querySelector("textarea[name='description']");
    const charCounter = document.createElement("p");
    charCounter.style.textAlign = "right";
    charCounter.style.color = "#555";
    descInput.parentNode.appendChild(charCounter);

    function updateCounter() {
        let length = descInput.value.length;
        charCounter.textContent = `${length}/200 characters`;
    }

    descInput.addEventListener("input", updateCounter);
    updateCounter();

    // Loading effect on form submit
    const form = document.querySelector("form");
    const submitBtn = document.querySelector("button[type='submit']");

    form.addEventListener("submit", function() {
        submitBtn.textContent = "Updating...";
        submitBtn.disabled = true;
        submitBtn.style.opacity = "0.7";
    });
});
