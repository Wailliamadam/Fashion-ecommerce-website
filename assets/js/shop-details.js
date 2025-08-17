document.addEventListener("DOMContentLoaded", function () {
  const bgElements = document.querySelectorAll(".set-bg");

  bgElements.forEach(function (el) {
    const bg = el.getAttribute("data-setbg");
    if (bg) {
      el.style.backgroundImage = `url('${bg}')`;
      el.style.backgroundSize = "cover";
      el.style.backgroundPosition = "center";
      el.style.backgroundRepeat = "no-repeat";
    }
  });
});

// Switch Tabs with Transition

document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".nav-link");
  const panes = document.querySelectorAll(".tab-pane");

  tabs.forEach((tab) => {
    tab.addEventListener("click", function (e) {
      e.preventDefault();

      // Remove active classes
      tabs.forEach((t) => t.classList.remove("active"));
      panes.forEach((pane) => pane.classList.remove("active"));

      // Add active class to clicked tab
      tab.classList.add("active");

      // Get target pane ID
      const targetId = tab.getAttribute("href");
      const targetPane = document.querySelector(targetId);

      // Show the target pane
      if (targetPane) {
        targetPane.classList.add("active");
      }
    });
  });
});

// Active Class on Click
document.addEventListener("DOMContentLoaded", function () {
  // Size Selection
  const sizeLabels = document.querySelectorAll(
    ".product__details__option__size label"
  );
  sizeLabels.forEach((label) => {
    label.addEventListener("click", () => {
      sizeLabels.forEach((l) => l.classList.remove("active"));
      label.classList.add("active");
    });
  });
});

//  Color Click Activation
document.addEventListener("DOMContentLoaded", function () {
  const colorLabels = document.querySelectorAll(
    ".product__details__option__color label"
  );

  colorLabels.forEach((label) => {
    label.addEventListener("click", () => {
      // Remove 'active' from all
      colorLabels.forEach((l) => l.classList.remove("active"));
      // Add 'active' to clicked label
      label.classList.add("active");
    });
  });
});

// Quantity Control
document.addEventListener("DOMContentLoaded", function () {
  const proQty = document.querySelector(".pro-qty");
  const input = proQty.querySelector("input");
  const btnUp = proQty.querySelector(".fa-angle-up");
  const btnDown = proQty.querySelector(".fa-angle-down");

  btnUp.addEventListener("click", () => {
    let val = parseInt(input.value);
    input.value = val + 1;
  });

  btnDown.addEventListener("click", () => {
    let val = parseInt(input.value);
    if (val > 1) {
      input.value = val - 1;
    }
  });
});
