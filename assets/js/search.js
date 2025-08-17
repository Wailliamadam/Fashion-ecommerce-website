document.addEventListener("DOMContentLoaded", function () {
  const searchSwitch = document.querySelector(".search-switch");
  const searchModel = document.querySelector(".search-model");
  const searchClose = document.querySelector(".search-close-switch");
  const searchForm = document.querySelector(".search-model-form");
  const searchInput = document.getElementById("search-input");

  // Show the search modal
  searchSwitch.addEventListener("click", function (e) {
    e.preventDefault();
    searchModel.style.display = "flex";
    searchInput.focus();
  });

  // Close the search modal when the close button is clicked
  searchClose.addEventListener("click", function () {
    searchModel.style.display = "none";
  });

  // Close the modal after the form is submitted
  searchForm.addEventListener("submit", function () {
    searchModel.style.display = "none";
  });

  // Allow closing the modal by pressing the Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      searchModel.style.display = "none";
    }
  });
});
